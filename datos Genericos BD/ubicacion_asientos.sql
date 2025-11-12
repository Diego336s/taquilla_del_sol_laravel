-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-11-2025 a las 04:21:48
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `taquillaria_del_sol`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ubicacion_asientos`
--

CREATE TABLE `ubicacion_asientos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ubicacion` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `ubicacion_asientos`
--

INSERT INTO `ubicacion_asientos` (`id`, `ubicacion`, `created_at`, `updated_at`) VALUES
(1, 'Zona General', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(2, 'Palco Izquierdo primer piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(3, 'Palco Derecho primer piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(4, 'Palco Izquierdo segundo piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(5, 'Palco Derecho segundo piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(6, 'Palco Trasero segundo piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41'),
(7, 'Palco Trasero primer piso', '2025-11-12 00:37:41', '2025-11-12 00:37:41');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `ubicacion_asientos`
--
ALTER TABLE `ubicacion_asientos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ubicacion_asientos_ubicacion_unique` (`ubicacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `ubicacion_asientos`
--
ALTER TABLE `ubicacion_asientos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
