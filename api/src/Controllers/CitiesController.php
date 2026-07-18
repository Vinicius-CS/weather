<?php

declare(strict_types=1);

final class CitiesController
{
  public function show(array $data): never
  {
    $text = $data['text'] ?? '';

    if (strlen($text) < 1)
    {
      throw new InvalidArgumentException('Informe o parâmetro "text"');
    }

    $service = new CitiesService();

    Response::json($service->fetch($text));
  }
}
