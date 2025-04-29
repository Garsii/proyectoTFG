-- Script para crear todas las tablas de la base de datos naturagym
-- Incluye DROP IF EXISTS para poder ejecutarse sobre una base vacía o existente.

SET FOREIGN_KEY_CHECKS = 0;

-- Tabla usuarios
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('usuario','admin') DEFAULT 'usuario',
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('activo','revocado') DEFAULT 'activo',
  `puesto_id` INT(11) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_puesto_id_foreign` (`puesto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla puestos
DROP TABLE IF EXISTS `puestos`;
CREATE TABLE `puestos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `puestos_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign key usuarios → puestos
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_puesto_id_foreign` FOREIGN KEY (`puesto_id`) REFERENCES `puestos`(`id`);

-- Tabla tarjetas
DROP TABLE IF EXISTS `tarjetas`;
CREATE TABLE `tarjetas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(20) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tarjetas_uid_unique` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla puntos_acceso
DROP TABLE IF EXISTS `puntos_acceso`;
CREATE TABLE `puntos_acceso` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `puntos_acceso_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla registros
DROP TABLE IF EXISTS `registros`;
CREATE TABLE `registros` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` VARCHAR(20) NOT NULL,
  `acceso` ENUM('permitido','denegado') NOT NULL,
  `fecha` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `punto_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `registros_punto_id_foreign` (`punto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign key registros → puntos_acceso
ALTER TABLE `registros`
  ADD CONSTRAINT `registros_punto_id_foreign` FOREIGN KEY (`punto_id`) REFERENCES `puntos_acceso`(`id`);

-- Tabla aforo
DROP TABLE IF EXISTS `aforo`;
CREATE TABLE `aforo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fecha` DATE NOT NULL,
  `hora` TIME NOT NULL,
  `aforo` INT(11) NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla clases
DROP TABLE IF EXISTS `clases`;
CREATE TABLE `clases` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `instructor` VARCHAR(100) NULL,
  `cupo` INT(11) NOT NULL,
  `fecha` DATE NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla reservas_clases
DROP TABLE IF EXISTS `reservas_clases`;
CREATE TABLE `reservas_clases` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `clase_id` INT(11) NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `reservas_clases_usuario_id_foreign` (`usuario_id`),
  KEY `reservas_clases_clase_id_foreign` (`clase_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FKs reservas_clases → usuarios, clases
ALTER TABLE `reservas_clases`
  ADD CONSTRAINT `reservas_clases_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`),
  ADD CONSTRAINT `reservas_clases_clase_id_foreign` FOREIGN KEY (`clase_id`) REFERENCES `clases`(`id`);

-- Tabla dietas
DROP TABLE IF EXISTS `dietas`;
CREATE TABLE `dietas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `calorias` INT(11) NULL,
  `recomendaciones` TEXT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla productos
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `categoria` ENUM('suplementos','equipo','merchandising','otros') NOT NULL,
  `descripcion` TEXT NULL,
  `precio` DECIMAL(10,2) NOT NULL,
  `stock` INT(11) DEFAULT 0,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla rutinas
DROP TABLE IF EXISTS `rutinas`;
CREATE TABLE `rutinas` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `duracion` INT(11) NULL,
  `nivel` ENUM('principiante','intermedio','avanzado') NOT NULL,
  `url_video` VARCHAR(255) NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla rutinas_usuario
DROP TABLE IF EXISTS `rutinas_usuario`;
CREATE TABLE `rutinas_usuario` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` INT(11) NOT NULL,
  `rutina_id` INT(11) NOT NULL,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `duracion` INT(11) NULL,
  `nivel` ENUM('principiante','intermedio','avanzado') NOT NULL,
  `url_video` VARCHAR(255) NULL,
  `fecha_modificacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `rutinas_usuario_usuario_id_foreign` (`usuario_id`),
  KEY `rutinas_usuario_rutina_id_foreign` (`rutina_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- FKs rutinas_usuario → usuarios, rutinas
ALTER TABLE `rutinas_usuario`
  ADD CONSTRAINT `rutinas_usuario_usuario_id_foreign` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`),
  ADD CONSTRAINT `rutinas_usuario_rutina_id_foreign` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`);

-- Tablas de sistema Laravel (cache, jobs, migrations, sessions, etc.)

-- Tabla cache
DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL,
  `value` LONGTEXT,
  `expiration` INT(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla cache_locks
DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` VARCHAR(255) NOT NULL,
  `owner` VARCHAR(255) NOT NULL,
  `expiration` INT(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla jobs
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` VARCHAR(255) NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `attempts` TINYINT UNSIGNED NOT NULL,
  `reserved_at` INT(11) NULL,
  `available_at` INT(11) NOT NULL,
  `created_at` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla failed_jobs
DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` VARCHAR(255) NOT NULL,
  `connection` TEXT NOT NULL,
  `queue` TEXT NOT NULL,
  `payload` LONGTEXT NOT NULL,
  `exception` LONGTEXT NOT NULL,
  `failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla job_batches
DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `total_jobs` INT NOT NULL,
  `pending_jobs` INT NOT NULL,
  `failed_jobs` INT NOT NULL,
  `options` LONGTEXT NULL,
  `created_at` TIMESTAMP NULL,
  `finished_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla sessions
DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `user_id` INT(11) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla password_reset_tokens
DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`),
  KEY `password_reset_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
