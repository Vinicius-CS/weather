<?php

declare(strict_types=1);

final class CitiesService
{
  private const BASE_URL = 'https://api.openweathermap.org/geo/1.0/direct';

  private string $apiKey;

  public function __construct()
  {
    $this->apiKey = getenv('OPENWEATHER_API_KEY') ?: '';

    if ($this->apiKey === '')
    {
      throw new RuntimeException('OPENWEATHER_API_KEY não configurada no arquivo .env');
    }
  }

  public function fetch(string $text): array
  {
    $ch = curl_init(self::BASE_URL . '?' . http_build_query([
      'q' => $text,
      'limit' => 5,
      'appid' => $this->apiKey,
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

    if ($status !== 200 || !is_array($data))
    {
      throw new RuntimeException('Erro na OpenWeatherMap: ' . ($data['message'] ?? "HTTP {$status}"));
    }

    $results = [];

    foreach ($data as $item)
    {
      $results[] = [
        'name' => (string) ($item['name'] ?? ''),
        'state' => (string) ($item['state'] ?? ''),
        'country' => (string) ($item['country'] ?? ''),
        'latitude' => (float) ($item['lat'] ?? 0),
        'longitude' => (float) ($item['lon'] ?? 0),
      ];
    }

    return $results;
  }
}
