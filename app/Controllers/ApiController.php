<?php

namespace App\Controllers;

use App\Services\OpenMeteoClient;
use App\Services\CycloneRiskEvaluator;

/**
 * Contrôleur principal pour gérer les requêtes de l'API.
 */
class ApiController
{
    private OpenMeteoClient $meteoClient;
    private CycloneRiskEvaluator $riskEvaluator;

    public function __construct()
    {
        $this->meteoClient = new OpenMeteoClient();
        $this->riskEvaluator = new CycloneRiskEvaluator();
    }

    /**
     * Gère la requête d'analyse de risque cyclonique.
     *
     * @param float $latitude
     * @param float $longitude
     * @return array
     */
    public function handleAnalysisRequest(float $latitude, float $longitude): array
    {
        // 1. Appeler les deux endpoints Open-Meteo
        $atmosData = $this->meteoClient->getAtmosphericData($latitude, $longitude);
        $marineData = $this->meteoClient->getMarineData($latitude, $longitude);

        // 2. Gérer les erreurs de récupération des données
        if ($atmosData === null || $marineData === null) {
            http_response_code(503); // Service Unavailable
            return [
                'error' => true,
                'message' => 'Impossible de récupérer les données météorologiques complètes. Le service Open-Meteo est peut-être indisponible ou les paramètres sont invalides.'
            ];
        }

        // 3. Fusionner les données et évaluer le risque
        $result = $this->riskEvaluator->evaluateCycloneRisk($atmosData, $marineData);
        
        http_response_code(200);
        return $result;
    }
}
