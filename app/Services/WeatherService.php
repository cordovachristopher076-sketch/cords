<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    /**
     * Retrieve and normalize the current weather for the configured location.
     *
     * @return array{location: string, temperature: float|int, condition: string, humidity: int, wind_speed: float|int}|null
     */
    public function current(): ?array
    {
        $city = config('services.weather.city');
        $latitude = config('services.weather.latitude');
        $longitude = config('services.weather.longitude');

        if (! is_string($city) || $city === '' || ! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        try {
            $response = Http::connectTimeout(3)
                ->timeout(5)
                ->get(config('services.weather.endpoint'), [
                    'latitude' => (float) $latitude,
                    'longitude' => (float) $longitude,
                    'current' => 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code',
                    'timezone' => 'auto',
                ]);
        } catch (ConnectionException $exception) {
            Log::warning('Weather API connection failed.', [
                'city' => $city,
                'message' => $exception->getMessage(),
            ]);

            return null;
        }

        if (! $response->successful()) {
            Log::warning('Weather API returned an unsuccessful response.', [
                'city' => $city,
                'status' => $response->status(),
            ]);

            return null;
        }

        $payload = $response->json();

        if (! is_array($payload) || ! isset($payload['current'])) {
            Log::warning('Weather API returned an invalid payload.', ['city' => $city]);

            return null;
        }

        $current = $payload['current'];

        if (! is_array($current)
            || ! isset($current['temperature_2m'], $current['relative_humidity_2m'], $current['wind_speed_10m'], $current['weather_code'])) {
            Log::warning('Weather API returned an invalid payload.', ['city' => $city]);

            return null;
        }

        return [
            'location' => $city,
            'temperature' => $current['temperature_2m'],
            'condition' => $this->conditionForCode((int) $current['weather_code']),
            'humidity' => (int) $current['relative_humidity_2m'],
            'wind_speed' => $current['wind_speed_10m'],
        ];
    }

    protected function conditionForCode(int $code): string
    {
        return match ($code) {
            0 => 'clear sky',
            1 => 'mainly clear',
            2 => 'partly cloudy',
            3 => 'overcast',
            45, 48 => 'fog',
            51, 53, 55 => 'drizzle',
            56, 57 => 'freezing drizzle',
            61, 63, 65 => 'rain',
            66, 67 => 'freezing rain',
            71, 73, 75, 77 => 'snow',
            80, 81, 82 => 'rain showers',
            85, 86 => 'snow showers',
            95 => 'thunderstorm',
            96, 99 => 'thunderstorm with hail',
            default => 'weather conditions',
        };
    }
}
