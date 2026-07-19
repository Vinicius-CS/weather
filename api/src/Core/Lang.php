<?php

declare(strict_types=1);

final class Lang
{
  private static string $current = 'en';

  private const MESSAGES = [
    'en' => [
      'invalid_type' => 'Provide the "type" parameter as "weather" or "forecast"',
      'invalid_coordinates' => 'Provide the "latitude" and "longitude" parameters',
      'invalid_text' => 'Provide the "text" parameter',
      'city_not_found' => 'City not found',
      'connection_failed' => 'Failed to connect to OpenWeatherMap',
      'api_error' => 'OpenWeatherMap error',
      'missing_api_key' => 'OPENWEATHER_API_KEY not set in the .env file',
      'method_not_allowed' => 'Method not allowed',
      'not_found' => 'Route not found',
      'internal_error' => 'Internal server error',
    ],
    'pt_br' => [
      'invalid_type' => 'Informe o parâmetro "type" como "weather" ou "forecast"',
      'invalid_coordinates' => 'Informe os parâmetros "latitude" e "longitude"',
      'invalid_text' => 'Informe o parâmetro "text"',
      'city_not_found' => 'Cidade não encontrada',
      'connection_failed' => 'Falha ao conectar na OpenWeatherMap',
      'api_error' => 'Erro na OpenWeatherMap',
      'missing_api_key' => 'OPENWEATHER_API_KEY não configurada no arquivo .env',
      'method_not_allowed' => 'Método não permitido',
      'not_found' => 'Rota não encontrada',
      'internal_error' => 'Erro interno no servidor',
    ],
  ];

  public static function set(string $lang): void
  {
    self::$current = str_starts_with(strtolower($lang), 'pt') ? 'pt_br' : 'en';
  }

  public static function current(): string
  {
    return self::$current;
  }

  public static function get(string $key): string
  {
    return self::MESSAGES[self::$current][$key] ?? self::MESSAGES['en'][$key] ?? $key;
  }
}
