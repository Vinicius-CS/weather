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
      throw new RuntimeException(Lang::get('missing_api_key'));
    }
  }

  public function fetch(string $endpoint, array $location): array
  {
    $cache = new CacheService();

    $latitude = round((float) $location['latitude'], 6);
    $longitude = round((float) $location['longitude'], 6);
    $lang = Lang::current();

    if (($cached = $cache->get($endpoint, $latitude, $longitude, $lang)) !== null)
    {
      return $cached;
    }

    $ch = curl_init(self::BASE_URL . '/' . $endpoint . '?' . http_build_query([
      'lat' => $latitude,
      'lon' => $longitude,
      'lang' => $lang,
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
      throw new RuntimeException(Lang::get('connection_failed'));
    }

    $data = json_decode((string) $body, true);

    if ($status === 404)
    {
      throw new InvalidArgumentException(Lang::get('city_not_found'));
    }

    if ($status !== 200 || !is_array($data))
    {
      throw new RuntimeException(Lang::get('api_error') . ': ' . ($data['message'] ?? "HTTP {$status}"));
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

      $citiesService = new CitiesService();

      $place = $citiesService->reverse($latitude, $longitude);

      if ($place !== null)
      {
        $data['city'] = $place['name'];
        $data['state'] = $place['state'];
      }
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

    $cache->put($endpoint, $latitude, $longitude, $data, $lang);

    return $data;
  }
}