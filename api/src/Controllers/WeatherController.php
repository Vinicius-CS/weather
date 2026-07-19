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

    $cities = new CitiesService();
    $service = new WeatherService();
    $searchers = new SearchesService();

    $weather = $service->fetch(
      $tipo,
      [
        'latitude' => $latitude,
        'longitude' => $longitude
      ]
    );

    if ($tipo === 'weather')
    {
      $place = $cities->reverse((float) $latitude, (float) $longitude);
      $ip = ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? '') ?: ($_SERVER['REMOTE_ADDR'] ?? '');

      if ($place !== null)
      {
        $weather['city'] = $place['name'];
        $weather['state'] = $place['state'];

        if ($ip !== '')
        {
          $searchers->log(
            $place['name'],
            trim(explode(',', $ip)[0])
          );
        }
      }
    }

    Response::json($weather);
  }
}
