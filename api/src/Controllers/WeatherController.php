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

    $service = new WeatherService();
    $searchers = new SearchesService();

    $weather = $service->fetch(
      $tipo,
      [
        'lat' => round((float) $latitude, 4),
        'lon' => round((float) $longitude, 4),
      ]
    );

    $city = trim($data['city'] ?? '');
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '';

    if ($tipo === 'weather' && $city !== '' && $ip !== '')
    {
      $searchers->log(
        $city,
        trim(explode(',', $ip)[0])
      );
    }

    Response::json($weather);
  }
}
