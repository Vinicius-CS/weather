<?php

declare(strict_types=1);

final class Database
{
  private static ?PDO $pdo = null;

  public static function connection(): PDO
  {
    if (self::$pdo === null)
    {
      self::$pdo = new PDO(
        'mysql:host=' . (getenv('DB_HOST') ?: 'db') . ';dbname=' . (getenv('DB_NAME') ?: 'weather') . ';charset=utf8mb4',
        getenv('DB_USER') ?: 'weather',
        getenv('DB_PASS') ?: 'weather',
        [
          PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
          PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
      );
    }

    return self::$pdo;
  }
}
