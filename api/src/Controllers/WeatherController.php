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

    Response::json($service->fetch($tipo, [
      'lat' => round((float) $latitude, 4),
      'lon' => round((float) $longitude, 4),
    ]));
  }
}
