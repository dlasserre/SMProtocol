CREATE TABLE IF NOT EXISTS download_ip (
  id INTEGER(8) NOT NULL AUTO_INCREMENT,
  ip VARCHAR(50) NOT NULL
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download_file(
    id INTEGER(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    size LONG NOT NULL,
    version LONGTEXT NULL
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download_location (
    id_ip INTEGER(8) NOT NULL PRIMARY KEY ,
    country_code VARCHAR(255) DEFAULT NULL,
    country_name VARCHAR(255) DEFAULT NULL,
    business_zone VARCHAR(255) DEFAULT NULL,
    region VARCHAR(255) DEFAULT NULL,
    city VARCHAR(255) DEFAULT NULL,
    longitude INTEGER(8) NOT NULL,
    latitude INTEGER(8) NOT NULL,
    FOREIGN KEY (id_ip) REFERENCES download_ip(id) ON DELETE CASCADE
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download (
    id INTEGER(8) NOT NULL PRIMARY KEY AUTO_INCREMENT,
    id_ip INTEGER(8) NOT NULL,
    id_file INTEGER(8) NOT NULL,
    http_response ENUM(200, 201, 202, 203, 204, 205, 206) DEFAULT 200,
    start_at INTEGER(8) NOT NULL,
    end_at INTEGER(8) NOT NULL,
    completed BOOLEAN NOT NULL DEFAULT false,
    percent INTEGER(8) NOT NULL DEFAULT 0,
    bytes_send INTEGER(8) NOT NULL,
    FOREIGN KEY (id_ip) REFERENCES download_ip(id) ON DELETE CASCADE,
    FOREIGN KEY (id_file) REFERENCES download_file(id) ON DELETE CASCADE
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download_pid (
    id_download INTEGER(8) NOT NULL PRIMARY KEY,
    ppid INTEGER(8) NOT NULL,
    pid INTEGER(8) NOT NULL,
    memory_max_used INTEGER(8) NOT NULL DEFAULT 0,
    nb_garbage_collector_cycle INTEGER(8) NOT NULL,
    defunct BOOL DEFAULT TRUE,
  FOREIGN KEY (id_download) REFERENCES download(id)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download_debug (
    id_download INTEGER(8) NOT NULL PRIMARY KEY,
    output VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_download) REFERENCES download(id) ON DELETE CASCADE
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE download_header(
  id_download INTEGER(8) NOT NULL PRIMARY KEY,
  header VARCHAR(255) NOT NULL,
  value VARCHAR(255) NOT NULL,
  FOREIGN KEY (id_download) REFERENCES download(id)
) ENGINE=InnoDb DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS download_http_request (
  id INTEGER(8) NOT NULL,
  request TEXT NOT NULL
) ENGINE=InnoDb DEFAULT CHARSET=utf8;