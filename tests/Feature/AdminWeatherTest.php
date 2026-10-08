<?php

use App\Models\User;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

test('admin dashboard maps weather api data into the weather section', function () {
    config([
        'services.weather.city' => 'Berlin',
        'services.weather.latitude' => 52.52,
        'services.weather.longitude' => 13.41,
    ]);

    Http::fake([
        'api.open-meteo.com/*' => Http::response([
            'current' => [
                'temperature_2m' => 30.5,
                'relative_humidity_2m' => 74,
                'wind_speed_10m' => 15.1,
                'weather_code' => 0,
            ],
        ]),
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Berlin')
        ->assertSee('30.5')
        ->assertSee('clear sky')
        ->assertSee('74%')
        ->assertSee('15.1 m/s');

    Http::assertSent(function (Request $request): bool {
        parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

        return ($query['latitude'] ?? null) === '52.52'
            && ($query['longitude'] ?? null) === '13.41'
            && ($query['current'] ?? null) === 'temperature_2m,relative_humidity_2m,wind_speed_10m,weather_code'
            && ($query['timezone'] ?? null) === 'auto';
    });
});

test('admin dashboard shows a fallback when the weather api fails', function () {
    config([
        'services.weather.city' => 'Berlin',
        'services.weather.latitude' => 52.52,
        'services.weather.longitude' => 13.41,
    ]);

    Http::fake([
        'api.open-meteo.com/*' => Http::response([], 503),
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Weather information is currently unavailable.')
        ->assertSee('Recent Attendance Audit Logs');
});

test('admin dashboard shows a fallback when the weather api cannot connect', function () {
    config([
        'services.weather.city' => 'Berlin',
        'services.weather.latitude' => 52.52,
        'services.weather.longitude' => 13.41,
    ]);

    Http::fake([
        'api.open-meteo.com/*' => Http::failedConnection(),
    ]);

    $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin User']);

    $this->actingAs($admin)
        ->get('/admin/dashboard')
        ->assertOk()
        ->assertSee('Weather information is currently unavailable.');
});
