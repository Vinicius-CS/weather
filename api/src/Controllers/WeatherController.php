<?php

declare(strict_types=1);

final class WeatherController
{
  public function show(array $data): never
  {
    $tipo = $data['type'] ?? '';

    if (!in_array($tipo, ['weather', 'forecast'], true))
    {
      throw new InvalidArgumentException('Informe o parâmetro "type" como "weather" ou "forecast"');
    }

    $latitude = $data['latitude'] ?? null;
    $longitude = $data['longitude'] ?? null;

    if (!is_numeric($latitude) || !is_numeric($longitude))
    {
      throw new InvalidArgumentException('Informe os parâmetros "latitude" e "longitude"');
    }

    $weatherService = new WeatherService();
    $searchersService = new SearchesService();

    $weather = $weatherService->fetch(
      $tipo,
      [
        'latitude' => $latitude,
        'longitude' => $longitude
      ]
    );

    if ($tipo === 'weather')
    {
      $ip = ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '') ?: ($_SERVER['REMOTE_ADDR'] ?? '');
      $city = $weather['city'] ?? '';

      if ($ip !== '' && $city !== '')
      {
        $searchersService->log(
          $city,
          trim(explode(',', $ip)[0])
        );
      }
    }

    Response::json($weather);
  }
}
