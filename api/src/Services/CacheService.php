<?php

declare(strict_types=1);

final class CacheService
{
  public function get(string $endpoint, float $latitude, float $longitude, string $lang): array | null
  {
    try
    {
      $sql = <<<EOQ
SELECT
  payload
FROM
  cache
WHERE
  endpoint = ? AND
  latitude = ? AND
  longitude = ? AND
  lang = ? AND
  fetched_at > (NOW() - INTERVAL 900 SECOND)
EOQ;

      $stmt = Database::connection()->prepare($sql);

      $stmt->execute([
        $endpoint,
        $latitude,
        $longitude,
        $lang,
      ]);

      $row = $stmt->fetch();

      return $row !== false ? json_decode((string) $row['payload'], true) : null;
    }
    catch (PDOException)
    {
      // Não impedir o funcionamento
      return null;
    }
  }

  public function put(string $endpoint, float $latitude, float $longitude, array $payload, string $lang): void
  {
    try
    {
      $sql = <<<EOQ
INSERT INTO
  cache
  (
    endpoint,
    latitude,
    longitude,
    lang,
    payload,
    fetched_at
  )
VALUES
  (
    ?,
    ?,
    ?,
    ?,
    ?,
    NOW()
  )
ON DUPLICATE KEY UPDATE
  payload = VALUES(payload),
  fetched_at = NOW()
EOQ;

      $stmt = Database::connection()->prepare($sql);

      $stmt->execute([
        $endpoint,
        $latitude,
        $longitude,
        $lang,
        json_encode($payload, JSON_UNESCAPED_UNICODE),
      ]);
    }
    catch (PDOException)
    {
      // Não impedir o funcionamento
    }
  }
}
