<?php

declare(strict_types=1);

final class SearchesController
{
  public function top(): never
  {
    $service = new SearchesService();

    Response::json($service->top());
  }
}