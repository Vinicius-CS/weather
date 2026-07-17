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
    $ch = curl_init(self::BASE_URL . '/' . $endpoint . '?' . http_build_query($location + [
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

    return $data;
  }
}