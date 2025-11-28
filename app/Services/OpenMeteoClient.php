<?php

namespace App\Services;

/**
 * Client pour interroger les API Open-Meteo en utilisant cURL.
 */
class OpenMeteoClient
{
    private const API_FORECAST_URL = 'https://api.open-meteo.com/v1/forecast';
    private const API_MARINE_URL = 'https://marine-api.open-meteo.com/v1/marine';
    private const CONNECT_TIMEOUT = 10; // secondes
    private const REQUEST_TIMEOUT = 15; // secondes

    /**
     * Interroge l'endpoint atmosphérique.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null Les données décodées ou null en cas d'erreur.
     */
    public function getAtmosphericData(float $latitude, float $longitude): ?array
    {
        $variables = [
            'temperature_2m',
            'relative_humidity_2m',
            'pressure_msl',
            'wind_speed_10m',
            'wind_direction_10m',
            'precipitation'
        ];
        $url = $this->buildUrl(self::API_FORECAST_URL, $latitude, $longitude, $variables);
        return $this->executeRequest($url);
    }

    /**
     * Interroge l'endpoint marin.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array|null Les données décodées ou null en cas d'erreur.
     */
    public function getMarineData(float $latitude, float $longitude): ?array
    {
        $variables = [
            'sea_surface_temperature',
            'wave_height'
        ];
        $url = $this->buildUrl(self::API_MARINE_URL, $latitude, $longitude, $variables);
        return $this->executeRequest($url);
    }

    /**
     * Construit l'URL complète pour l'appel API.
     *
     * @param string $baseUrl
     * @param float $latitude
     * @param float $longitude
     * @param array $variables
     * @return string
     */
    private function buildUrl(string $baseUrl, float $latitude, float $longitude, array $variables): string
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => implode(',', $variables),
            'forecast_days' => 4,
            'timezone' => 'auto',
            'elevation' => 0
        ];
        return $baseUrl . '?' . http_build_query($params);
    }

    /**
     * Exécute une requête cURL et gère la réponse.
     *
     * @param string $url
     * @return array|null
     */
    private function executeRequest(string $url): ?array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::CONNECT_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::REQUEST_TIMEOUT);
        // Recommandé pour la production : gestion de la vérification SSL
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
            // Gérer l'erreur cURL (ex: timeout)
            // error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return null;
        }

        curl_close($ch);

        if ($httpCode !== 200) {
            // Gérer une réponse API avec un code d'erreur HTTP
            // error_log('API Error: HTTP Code ' . $httpCode . ' for URL ' . $url);
            return null;
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Gérer une réponse qui n'est pas un JSON valide
            // error_log('JSON Decode Error: ' . json_last_error_msg());
            return null;
        }
        
        if (isset($data['error']) && $data['error'] === true) {
            // Gérer une erreur fonctionnelle retournée par l'API
            // error_log('API Functional Error: ' . ($data['reason'] ?? 'Unknown reason'));
            return null;
        }

        return $data;
    }
}
