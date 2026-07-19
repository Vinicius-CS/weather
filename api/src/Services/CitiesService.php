<?php

declare(strict_types=1);

final class CitiesService
{
  private const BASE_URL = 'https://api.openweathermap.org/geo/1.0/direct';
  private const REVERSE_URL = 'https://api.openweathermap.org/geo/1.0/reverse';

  private string $apiKey;

  public function __construct()
  {
    $this->apiKey = getenv('OPENWEATHER_API_KEY') ?: '';

    if ($this->apiKey === '')
    {
      throw new RuntimeException(Lang::get('missing_api_key'));
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
      throw new RuntimeException(Lang::get('connection_failed'));
    }

    $data = json_decode((string) $body, true);

    if ($status !== 200 || !is_array($data))
    {
      throw new RuntimeException(Lang::get('api_error') . ': ' . ($data['message'] ?? "HTTP {$status}"));
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

  public function reverse(float $latitude, float $longitude): array | null
  {
    $ch = curl_init(self::REVERSE_URL . '?' . http_build_query([
      'lat' => $latitude,
      'lon' => $longitude,
      'limit' => 1,
      'appid' => $this->apiKey,
    ]));

    curl_setopt_array($ch, [
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_TIMEOUT => 10,
    ]);

    $body = curl_exec($ch);
    $data = $body !== false ? json_decode((string) $body, true) : null;

    return isset($data[0]['name']) ? [
      'name' => (string) $data[0]['name'],
      'state' => (string) ($data[0]['state'] ?? ''),
      'country' => (string) ($data[0]['country'] ?? ''),
    ] : null;
  }
}
