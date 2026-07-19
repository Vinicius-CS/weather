<?php

declare(strict_types=1);

require __DIR__ . '/../src/Core/Response.php';
require __DIR__ . '/../src/Core/Database.php';
require __DIR__ . '/../src/Services/CacheService.php';
require __DIR__ . '/../src/Services/CitiesService.php';
require __DIR__ . '/../src/Services/SearchesService.php';
require __DIR__ . '/../src/Services/WeatherService.php';
require __DIR__ . '/../src/Controllers/CitiesController.php';
require __DIR__ . '/../src/Controllers/SearchesController.php';
require __DIR__ . '/../src/Controllers/WeatherController.php';

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

$cities = new CitiesController();
$searches = new SearchesService();
$weather = new WeatherController();

if ($_SERVER['REQUEST_METHOD'] !== 'GET')
{
  Response::json(['error' => 'Método não permitido'], 405);
}

try
{
  switch (parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
  {
    case '/api/cities':
      $cities->show($_GET);
      break;
    case '/api/searches':
      $searches->top();
      break;
    case '/api/weather':
      $weather->show($_GET);
      break;
    default:
      Response::json(['error' => 'Rota não encontrada'], 404);
  }
}
catch (Throwable $e)
{
  Response::json(['error' => 'Erro interno no servidor'], 500);
}
