<?php

namespace App\Services;

use App\Models\WeatherData;

/**
 * Évalue le risque cyclonique à partir des données météorologiques.
 */
class CycloneRiskEvaluator
{
    private const THRESHOLDS = [
        'pressure_msl' => ['max' => 1005, 'message' => 'Pression basse'],
        'relative_humidity_2m' => ['min' => 80, 'message' => 'Humidité élevée'],
        'wind_speed_10m' => ['min' => 50, 'message' => 'Vent fort'],
        'sea_surface_temperature' => ['min' => 27, 'message' => 'SST élevée'],
        'precipitation' => ['min' => 10, 'message' => 'Fortes précipitations'],
    ];

    /**
     * Évalue le risque cyclonique en se basant sur les données fusionnées.
     * Le risque est considéré comme "true" si au moins 3 conditions sont remplies
     * pour N'IMPORTE QUELLE heure dans la période de prévision.
     *
     * @param array $dataAtmos Données de l'API atmosphérique.
     * @param array $dataMarine Données de l'API marine.
     * @return array ['risk' => bool, 'message' => string]
     */
    public function evaluateCycloneRisk(array $dataAtmos, array $dataMarine): array
    {
        if (empty($dataAtmos['hourly']['time']) || empty($dataMarine['hourly']['time'])) {
            return ['risk' => false, 'message' => 'Données de prévision manquantes ou incomplètes.'];
        }

        // Fusion des données horaires
        $mergedHourlyData = array_merge($dataAtmos['hourly'], $dataMarine['hourly']);

        $timeSlotsCount = count($mergedHourlyData['time']);

        for ($i = 0; $i < $timeSlotsCount; $i++) {
            $weatherData = new WeatherData($mergedHourlyData, $i);

            if (!$weatherData->hasRequiredDataForEvaluation()) {
                continue; // Passe à l'heure suivante si une donnée clé manque
            }

            $triggeredConditions = [];
            $conditionsCount = 0;

            // Pression < 1005 hPa
            if ($weatherData->pressure_msl < self::THRESHOLDS['pressure_msl']['max']) {
                $conditionsCount++;
                $triggeredConditions[] = self::THRESHOLDS['pressure_msl']['message'];
            }
            // Humidité > 80 %
            if ($weatherData->relative_humidity_2m > self::THRESHOLDS['relative_humidity_2m']['min']) {
                $conditionsCount++;
                $triggeredConditions[] = self::THRESHOLDS['relative_humidity_2m']['message'];
            }
            // Vent > 50 km/h
            if ($weatherData->wind_speed_10m > self::THRESHOLDS['wind_speed_10m']['min']) {
                $conditionsCount++;
                $triggeredConditions[] = self::THRESHOLDS['wind_speed_10m']['message'];
            }
            // SST > 27°C
            if ($weatherData->sea_surface_temperature > self::THRESHOLDS['sea_surface_temperature']['min']) {
                $conditionsCount++;
                $triggeredConditions[] = self::THRESHOLDS['sea_surface_temperature']['message'];
            }
            // Précipitation > 10 mm/h
            if ($weatherData->precipitation > self::THRESHOLDS['precipitation']['min']) {
                $conditionsCount++;
                $triggeredConditions[] = self::THRESHOLDS['precipitation']['message'];
            }

            // Règle finale : si au moins 3 conditions sont réunies
            if ($conditionsCount >= 3) {
                return [
                    'risk' => true,
                    'message' => "Risque cyclonique détecté. Conditions remplies: " . implode(', ', $triggeredConditions) . " (à " . date("d/m/Y H:i", strtotime($weatherData->time)) . ")."
                ];
            }
        }

        return [
            'risk' => false,
            'message' => 'Aucun risque cyclonique majeur détecté dans les 4 prochains jours selon les critères définis.'
        ];
    }
}
