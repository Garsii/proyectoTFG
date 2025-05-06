-- Disable foreign key checks to allow dropping in any order
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Drop tables in reverse dependency order
DROP TABLE IF EXISTS `registros`;
DROP TABLE IF EXISTS `rutinas_usuario`;
DROP TABLE IF EXISTS `reservas_clases`;
DROP TABLE IF EXISTS `tarjetas`;
DROP TABLE IF EXISTS `puntos_acceso`;
DROP TABLE IF EXISTS `rutinas`;
DROP TABLE IF EXISTS `clases`;
DROP TABLE IF EXISTS `usuarios`;
DROP TABLE IF EXISTS `puestos`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `personal_access_tokens`;
DROP TABLE IF EXISTS `migrations`;
DROP TABLE IF EXISTS `aforo`;

-- Re-enable foreign key checks
/*!40014 SET FOREIGN_KEY_CHECKS=1 */;
/*!40014 SET UNIQUE_CHECKS=1 */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- 1. puestos
CREATE TABLE `puestos` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. usuarios
CREATE TABLE `usuarios` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `rol` ENUM('usuario','admin') NOT NULL DEFAULT 'usuario',
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` ENUM('activo','revocado') NOT NULL DEFAULT 'activo',
  `puesto_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_usuarios_puesto` FOREIGN KEY (`puesto_id`) REFERENCES `puestos`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. tarjetas
CREATE TABLE `tarjetas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `uid` VARCHAR(20) NOT NULL UNIQUE,
  `usuario_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_tarjetas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. clases
CREATE TABLE `clases` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT,
  `fecha_inicio` DATETIME,
  `fecha_fin` DATETIME,
  `cupo` INT,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. rutinas
CREATE TABLE `rutinas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `descripcion` TEXT,
  `nivel` ENUM('principiante','intermedio','avanzado'),
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. puntos_acceso
CREATE TABLE `puntos_acceso` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(50) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. reservas_clases
CREATE TABLE `reservas_clases` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `clase_id` INT UNSIGNED NOT NULL,
  `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reservas_clase` FOREIGN KEY (`clase_id`) REFERENCES `clases`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. rutinas_usuario
CREATE TABLE `rutinas_usuario` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` BIGINT UNSIGNED NOT NULL,
  `rutina_id` INT UNSIGNED NOT NULL,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `duracion` INT,
  `nivel` ENUM('principiante','intermedio','avanzado'),
  `url_video` VARCHAR(255),
  `fecha_modificacion` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_rutinas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rutina` FOREIGN KEY (`rutina_id`) REFERENCES `rutinas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. registros
CREATE TABLE `registros` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `usuario_id` BIGINT UNSIGNED DEFAULT NULL,
  `tarjeta_id` INT UNSIGNED DEFAULT NULL,
  `punto_acceso_id` INT UNSIGNED DEFAULT NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `acceso` ENUM('permitido','denegado') DEFAULT 'denegado',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `fk_registros_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_registros_tarjeta` FOREIGN KEY (`tarjeta_id`) REFERENCES `tarjetas`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_registros_punto` FOREIGN KEY (`punto_acceso_id`) REFERENCES `puntos_acceso`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Maintain migrations & other default tables
CREATE TABLE `migrations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `migration` VARCHAR(255) NOT NULL,
  `batch` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` VARCHAR(255) NOT NULL PRIMARY KEY,
  `value` TEXT NOT NULL,
  `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_resets` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  INDEX (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `personal_access_tokens` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `token` VARCHAR(64) NOT NULL UNIQUE,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  INDEX (`user_id`),
  CONSTRAINT `fk_pat_user` FOREIGN KEY (`user_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Restore foreign key and unique checks
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
