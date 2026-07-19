<?php

declare(strict_types=1);

final class SearchesController
{
  public function top(): never
  {
    $searchersService = new SearchesService();

    Response::json($searchersService->top());
  }
}