<?php

declare(strict_types=1);

final class SearchesService
{
  public function log(string $city, string $ip): void
  {
    try
    {
      $sql = <<<EOQ
INSERT IGNORE INTO
  searches
  (
    city,
    client_hash
  )
VALUES
  (
    ?,
    ?
  )
EOQ;

      $stmt = Database::connection()->prepare($sql);

      $stmt->execute([
        substr(trim($city), 0, 120),
        hash('sha256', $ip),
      ]);
    }
    catch (PDOException)
    {
      // Não impedir o funcionamento
    }
  }

  public function top(): array
  {
    try
    {
      $sql = <<<EOQ
SELECT
  city,
  COUNT(*) AS total
FROM
  searches
GROUP BY
  city
HAVING
  total >= 3
ORDER BY
  total DESC
LIMIT
  5
EOQ;

      $rows = Database::connection()->query($sql)->fetchAll();
    }
    catch (PDOException)
    {
      $rows = [];
    }

    Response::json($rows);
  }
}