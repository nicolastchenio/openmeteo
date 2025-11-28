<?php

namespace App\Models;

/**
 * Modèle représentant une tranche horaire de données météorologiques fusionnées.
 */
class WeatherData
{
    public ?string $time = null;
    public ?float $temperature_2m = null;
    public ?float $relative_humidity_2m = null;
    public ?float $pressure_msl = null;
    public ?float $wind_speed_10m = null;
    public ?float $precipitation = null;
    public ?float $sea_surface_temperature = null;

    public function __construct(array $hourlyData, int $index)
    {
        $this->time = $hourlyData['time'][$index] ?? null;
        $this->temperature_2m = $hourlyData['temperature_2m'][$index] ?? null;
        $this->relative_humidity_2m = $hourlyData['relative_humidity_2m'][$index] ?? null;
        $this->pressure_msl = $hourlyData['pressure_msl'][$index] ?? null;
        $this->wind_speed_10m = $hourlyData['wind_speed_10m'][$index] ?? null;
        $this->precipitation = $hourlyData['precipitation'][$index] ?? null;
        $this->sea_surface_temperature = $hourlyData['sea_surface_temperature'][$index] ?? null;
    }
    
    /**
     * Vérifie si les données essentielles pour l'évaluation sont présentes.
     */
    public function hasRequiredDataForEvaluation(): bool
    {
        return isset(
            $this->pressure_msl,
            $this->relative_humidity_2m,
            $this->wind_speed_10m,
            $this->sea_surface_temperature,
            $this->precipitation
        );
    }
}
