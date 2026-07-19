<?php

declare(strict_types=1);

final class WeatherService
{
  private const BASE_URL = 'https://api.openweathermap.org/data/2.5';

  private string $apiKey;

  public function __construct()
  {
    $this->apiKey = getenv('OPENWEATHER_API_KEY') ?: '';

    if ($this->apiKey === '')
    {
      throw new RuntimeException('OPENWEATHER_API_KEY não configurada no arquivo .env');
    }
  }

  public function fetch(string $endpoint, array $location): array
  {
    $cache = new CacheService();

    $latitude = round((float) $location['latitude'], 6);
    $longitude = round((float) $location['longitude'], 6);

    if (($cached = $cache->get($endpoint, $latitude, $longitude)) !== null)
    {
      return $cached;
    }

    $ch = curl_init(self::BASE_URL . '/' . $endpoint . '?' . http_build_query([
      'lat' => $latitude,
      'lon' => $longitude,
      'appid' => $this->apiKey,
      'units' => 'metric'
    ]));

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 10,
    ]);

    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);

    if ($body === false)
    {
      throw new RuntimeException('Falha ao conectar na OpenWeatherMap');
    }

    $data = json_decode((string) $body, true);

    if ($status === 404)
    {
      throw new InvalidArgumentException('Cidade não encontrada');
    }

    if ($status !== 200 || !is_array($data))
    {
      throw new RuntimeException('Erro na OpenWeatherMap: ' . ($data['message'] ?? "HTTP {$status}"));
    }

    if ($endpoint === 'weather')
    {
      $data = [
        'city' => $data['name'],
        'state' => '',
        'country' => $data['sys']['country'] ?? '',
        'icon' => $data['weather'][0]['icon'],
        'description' => $data['weather'][0]['description'],
        'temp' => $data['main']['temp'],
        'feels_like' => $data['main']['feels_like'],
        'temp_min' => $data['main']['temp_min'],
        'temp_max' => $data['main']['temp_max'],
        'humidity' => $data['main']['humidity'],
        'wind' => $data['wind']['speed'],
      ];
    }
    else
    {
      $data = [
        'list' => array_map(
          fn (array $item) => [
            'date' => $item['dt_txt'],
            'icon' => $item['weather'][0]['icon'],
            'description' => $item['weather'][0]['description'],
            'temp_min' => $item['main']['temp_min'],
            'temp_max' => $item['main']['temp_max'],
          ],
          $data['list']
        ),
      ];
    }

    $cache->put($endpoint, $latitude, $longitude, $data);

    return $data;
  }
}