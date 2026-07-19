CREATE TABLE IF NOT EXISTS searches (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  city VARCHAR(120) NOT NULL,
  client_hash CHAR(64) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_city_client (city, client_hash)
);

CREATE TABLE IF NOT EXISTS cache (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  endpoint ENUM('weather', 'forecast') NOT NULL,
  latitude DECIMAL(9, 6) NOT NULL,
  longitude DECIMAL(9, 6) NOT NULL,
  lang VARCHAR(5) NOT NULL DEFAULT 'en',
  payload JSON NOT NULL,
  fetched_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_endpoint_coords_lang (endpoint, latitude, longitude, lang)
);