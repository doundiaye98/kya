CREATE DATABASE IF NOT EXISTS kya_cars
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE kya_cars;

CREATE TABLE IF NOT EXISTS roles (
  id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(32) NOT NULL UNIQUE,
  label VARCHAR(64) NOT NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO roles (id, slug, label) VALUES
  (1, 'admin', 'Administrateur'),
  (2, 'client', 'Client');

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id TINYINT UNSIGNED NOT NULL,
  nom VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  telephone VARCHAR(30) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  statut ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  last_login DATETIME DEFAULT NULL,
  CONSTRAINT fk_users_role FOREIGN KEY (role_id) REFERENCES roles(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  vehicule VARCHAR(160) NOT NULL,
  type_offre ENUM('location','vente') NOT NULL DEFAULT 'location',
  date_debut DATE DEFAULT NULL,
  date_fin DATE DEFAULT NULL,
  message TEXT,
  statut ENUM('en_attente','confirmee','annulee','terminee') NOT NULL DEFAULT 'en_attente',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_reservations_user (user_id, created_at),
  INDEX idx_reservations_statut (statut, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(160) NOT NULL,
  brand VARCHAR(80) NOT NULL,
  type VARCHAR(80) NOT NULL,
  year SMALLINT UNSIGNED DEFAULT NULL,
  seats TINYINT UNSIGNED DEFAULT 5,
  transmission VARCHAR(40) DEFAULT 'Automatique',
  fuel VARCHAR(40) DEFAULT 'Essence',
  offer_location TINYINT(1) NOT NULL DEFAULT 1,
  offer_vente TINYINT(1) NOT NULL DEFAULT 0,
  price_day INT UNSIGNED DEFAULT NULL,
  price_sale INT UNSIGNED DEFAULT NULL,
  featured TINYINT(1) NOT NULL DEFAULT 0,
  image VARCHAR(500) DEFAULT NULL,
  statut ENUM('actif','inactif') NOT NULL DEFAULT 'actif',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_products_visible (statut, featured, name)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS app_sessions (
  id VARCHAR(128) NOT NULL PRIMARY KEY,
  data MEDIUMBLOB NOT NULL,
  last_activity INT UNSIGNED NOT NULL,
  INDEX idx_activity (last_activity)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS error_logs (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  level VARCHAR(20) NOT NULL DEFAULT 'error',
  message TEXT NOT NULL,
  context VARCHAR(190) DEFAULT NULL,
  ip VARCHAR(45) DEFAULT NULL,
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS login_attempts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45) NOT NULL,
  email VARCHAR(190) NOT NULL,
  attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_time (ip, attempted_at),
  INDEX idx_email_time (email, attempted_at)
) ENGINE=InnoDB;
