-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 18-07-2025 a las 02:15:02
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `laboratorio`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesos`
--

CREATE TABLE `accesos` (
  `id` int(11) NOT NULL,
  `id_usuario` varchar(50) NOT NULL,
  `tipo` enum('alumno','maestro') NOT NULL,
  `laboratorio` varchar(50) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(11) NOT NULL,
  `matricula` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp(),
  `grupo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `matricula`, `nombre`, `password`, `fecha_registro`, `grupo`) VALUES
(2, '23040138', 'Juan Antonio de Jesus Gonzalez Rangel', '23040138', '2025-06-19 14:01:16', '6TIEVNDA'),
(4, '21045156', 'Mario García', 'maro21', '2025-06-30 16:26:02', ''),
(5, '22004148', 'Mairene Espejel', '2200412', '2025-07-06 21:05:35', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos`
--

CREATE TABLE `grupos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos`
--

INSERT INTO `grupos` (`id`, `nombre`) VALUES
(1, '6TIEVNDA'),
(6, '6TIEVNDB'),
(7, '3TIEVNDA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupos_maestros`
--

CREATE TABLE `grupos_maestros` (
  `id` int(11) NOT NULL,
  `maestro_id` int(11) NOT NULL,
  `grupo` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `grupos_maestros`
--

INSERT INTO `grupos_maestros` (`id`, `maestro_id`, `grupo`) VALUES
(0, 1, '6TIEVNDA');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ingresos_usuarios`
--

CREATE TABLE `ingresos_usuarios` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` enum('alumno','maestro') NOT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ingresos_usuarios`
--

INSERT INTO `ingresos_usuarios` (`id`, `usuario_id`, `tipo`, `fecha_ingreso`) VALUES
(1, 2, 'alumno', '2025-06-19 14:24:38'),
(2, 1, 'maestro', '2025-06-19 14:54:46'),
(3, 1, 'maestro', '2025-06-19 15:22:29'),
(4, 1, 'maestro', '2025-06-19 18:22:35'),
(5, 1, 'maestro', '2025-06-19 18:40:24'),
(6, 2, 'alumno', '2025-06-19 18:44:22'),
(7, 1, 'maestro', '2025-06-19 18:50:17'),
(8, 1, 'maestro', '2025-06-19 18:51:00'),
(9, 1, 'maestro', '2025-06-19 20:30:58'),
(10, 1, 'maestro', '2025-06-23 14:01:25'),
(11, 2, 'alumno', '2025-06-23 14:23:53'),
(12, 1, 'maestro', '2025-06-23 14:24:11'),
(13, 1, 'maestro', '2025-06-23 14:55:43'),
(14, 1, 'maestro', '2025-06-23 15:42:35'),
(15, 1, 'maestro', '2025-06-23 16:20:11'),
(16, 1, 'maestro', '2025-06-23 16:48:44'),
(17, 1, 'maestro', '2025-06-23 19:27:05'),
(18, 1, 'maestro', '2025-06-23 20:05:53'),
(19, 1, 'maestro', '2025-06-24 13:45:22'),
(20, 1, 'maestro', '2025-06-24 13:58:01'),
(21, 1, 'maestro', '2025-06-24 14:06:43'),
(22, 1, 'maestro', '2025-06-24 14:15:39'),
(23, 1, 'maestro', '2025-06-24 14:56:33'),
(24, 1, 'maestro', '2025-06-24 17:06:00'),
(25, 1, 'maestro', '2025-06-24 17:20:21'),
(26, 1, 'maestro', '2025-06-24 17:23:46'),
(27, 1, 'maestro', '2025-06-24 19:08:32'),
(28, 1, 'maestro', '2025-06-25 19:12:36'),
(29, 1, 'maestro', '2025-06-25 19:36:21'),
(30, 2, 'alumno', '2025-06-27 15:34:13'),
(31, 1, 'maestro', '2025-06-27 15:36:33'),
(32, 1, 'maestro', '2025-06-27 17:06:33'),
(33, 1, 'maestro', '2025-06-27 19:29:50'),
(34, 1, 'maestro', '2025-06-27 20:08:20'),
(35, 4, 'maestro', '2025-06-30 15:40:13'),
(36, 2, 'alumno', '2025-06-30 15:44:53'),
(37, 1, 'maestro', '2025-06-30 16:04:33'),
(38, 1, 'maestro', '2025-06-30 16:12:53'),
(39, 1, 'maestro', '2025-06-30 16:24:17'),
(40, 1, 'maestro', '2025-06-30 16:31:02'),
(41, 1, 'maestro', '2025-06-30 16:52:17'),
(42, 1, 'maestro', '2025-06-30 16:52:50'),
(43, 1, 'maestro', '2025-06-30 16:58:24'),
(44, 1, 'maestro', '2025-06-30 17:04:08'),
(45, 1, 'maestro', '2025-06-30 17:11:55'),
(46, 1, 'maestro', '2025-06-30 17:25:58'),
(47, 2, 'alumno', '2025-06-30 17:30:14'),
(48, 1, 'maestro', '2025-06-30 17:31:48'),
(49, 1, 'maestro', '2025-06-30 17:38:39'),
(50, 2, 'alumno', '2025-06-30 18:37:20'),
(51, 1, 'maestro', '2025-06-30 18:45:56'),
(52, 2, 'alumno', '2025-06-30 19:30:29'),
(53, 2, 'alumno', '2025-06-30 20:05:48'),
(54, 1, 'maestro', '2025-06-30 20:19:39'),
(55, 1, 'maestro', '2025-07-01 13:57:39'),
(56, 1, 'maestro', '2025-07-01 14:03:02'),
(57, 1, 'maestro', '2025-07-01 14:38:17'),
(58, 1, 'maestro', '2025-07-01 14:40:41'),
(59, 1, 'maestro', '2025-07-01 16:23:21'),
(60, 2, 'alumno', '2025-07-01 16:32:24'),
(61, 1, 'maestro', '2025-07-01 16:34:22'),
(62, 1, 'maestro', '2025-07-01 16:41:01'),
(63, 2, 'alumno', '2025-07-01 16:45:36'),
(64, 1, 'maestro', '2025-07-01 16:45:57'),
(65, 1, 'maestro', '2025-07-01 17:28:00'),
(66, 1, 'maestro', '2025-07-01 19:37:55'),
(67, 1, 'maestro', '2025-07-01 21:03:49'),
(68, 1, 'maestro', '2025-07-02 13:34:57'),
(69, 1, 'maestro', '2025-07-02 13:37:04'),
(70, 1, 'maestro', '2025-07-02 13:46:51'),
(71, 1, 'maestro', '2025-07-02 16:10:37'),
(72, 1, 'maestro', '2025-07-02 16:18:22'),
(73, 1, 'maestro', '2025-07-02 16:24:41'),
(74, 1, 'maestro', '2025-07-02 16:30:43'),
(75, 1, 'maestro', '2025-07-02 16:35:45'),
(76, 2, 'alumno', '2025-07-02 19:26:30'),
(77, 1, 'maestro', '2025-07-03 13:51:41'),
(78, 1, 'maestro', '2025-07-03 14:57:50'),
(79, 1, 'maestro', '2025-07-03 15:35:09'),
(80, 1, 'maestro', '2025-07-03 16:50:38'),
(81, 1, 'maestro', '2025-07-03 17:22:58'),
(82, 1, 'maestro', '2025-07-03 19:37:40'),
(83, 1, 'maestro', '2025-07-04 14:07:43'),
(84, 1, 'maestro', '2025-07-04 16:11:20'),
(85, 1, 'maestro', '2025-07-04 16:32:57'),
(86, 2, 'alumno', '2025-07-04 16:35:01'),
(87, 1, 'maestro', '2025-07-04 16:36:14'),
(88, 1, 'maestro', '2025-07-04 17:06:17'),
(89, 2, 'alumno', '2025-07-04 17:18:58'),
(90, 1, 'maestro', '2025-07-04 17:20:14'),
(91, 1, 'maestro', '2025-07-04 17:38:51'),
(92, 1, 'maestro', '2025-07-04 19:05:56'),
(93, 1, 'maestro', '2025-07-04 19:43:30'),
(94, 1, 'maestro', '2025-07-05 11:44:15'),
(95, 1, 'maestro', '2025-07-06 18:22:49'),
(96, 1, 'maestro', '2025-07-06 19:31:16'),
(97, 2, 'alumno', '2025-07-06 19:33:54'),
(98, 1, 'maestro', '2025-07-06 19:36:41'),
(99, 5, 'alumno', '2025-07-06 21:09:51'),
(100, 1, 'maestro', '2025-07-06 21:20:23'),
(101, 6, 'maestro', '2025-07-06 21:23:53'),
(102, 1, 'maestro', '2025-07-07 13:36:21'),
(103, 1, 'maestro', '2025-07-07 13:45:32'),
(104, 1, 'maestro', '2025-07-07 14:43:26'),
(105, 2, 'alumno', '2025-07-07 14:45:48'),
(106, 1, 'maestro', '2025-07-07 14:58:48'),
(107, 2, 'alumno', '2025-07-07 15:09:42'),
(108, 1, 'maestro', '2025-07-07 15:13:16'),
(109, 1, 'maestro', '2025-07-07 16:41:40'),
(110, 1, 'maestro', '2025-07-07 16:43:35'),
(111, 1, 'maestro', '2025-07-07 17:15:47'),
(112, 1, 'maestro', '2025-07-07 20:13:55'),
(113, 1, 'maestro', '2025-07-08 20:47:37'),
(114, 1, 'maestro', '2025-07-08 20:48:07'),
(115, 1, 'maestro', '2025-07-08 20:49:00'),
(116, 1, 'maestro', '2025-07-08 20:49:29'),
(117, 1, 'maestro', '2025-07-09 13:22:44'),
(118, 2, 'alumno', '2025-07-09 13:23:16'),
(119, 1, 'maestro', '2025-07-09 14:52:42'),
(120, 1, 'maestro', '2025-07-09 15:15:47'),
(121, 1, 'maestro', '2025-07-09 15:17:23'),
(122, 1, 'maestro', '2025-07-10 17:05:37'),
(123, 7, 'maestro', '2025-07-11 19:34:55'),
(124, 1, 'maestro', '2025-07-11 20:03:26'),
(125, 2, 'alumno', '2025-07-14 16:19:20');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorios`
--

CREATE TABLE `laboratorios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `laboratorios`
--

INSERT INTO `laboratorios` (`id`, `nombre`) VALUES
(1, 'Laboratorio 1'),
(2, 'Laboratorio 2'),
(3, 'Laboratorio 3'),
(4, 'Laboratorio 4'),
(5, 'Laboratorio 5'),
(6, 'Laboratorio Cisco'),
(7, 'Laboratorio Multimedia'),
(8, 'Laboratorio Servidor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `laboratorio_componentes`
--

CREATE TABLE `laboratorio_componentes` (
  `id` int(11) NOT NULL,
  `laboratorio_id` int(11) NOT NULL,
  `componente` varchar(100) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `laboratorio_componentes`
--

INSERT INTO `laboratorio_componentes` (`id`, `laboratorio_id`, `componente`, `cantidad`) VALUES
(1, 1, 'Maquinas', 25),
(2, 1, 'Mouses', 21),
(4, 1, 'Teclados', 25),
(5, 1, 'PC', 24),
(6, 1, 'Monitores', 25),
(7, 1, 'Cable red', 21),
(8, 1, 'Multiconectores', 5),
(9, 2, 'Maquinas', 27),
(10, 2, 'Mouses', 7),
(11, 2, 'Teclados', 27),
(12, 2, 'PC', 27),
(13, 2, 'Monitores', 27),
(14, 2, 'Cable red', 27),
(15, 2, 'Conectores', 29),
(16, 3, 'Maquinas', 19),
(17, 3, 'Mouses', 4),
(18, 3, 'Teclados', 19),
(19, 3, 'PC', 19),
(20, 3, 'Monitores', 19),
(21, 3, 'Cable red', 15),
(22, 3, 'Multiconectores', 4),
(23, 4, 'Maquinas', 29),
(24, 4, 'Mouses', 17),
(25, 4, 'Teclados', 26),
(26, 4, 'PC', 28),
(27, 4, 'Monitores', 29),
(28, 4, 'Cable red', 20),
(29, 4, 'Multiconectores', 3),
(30, 5, 'Maquinas', 24),
(31, 5, 'Mouses', 8),
(32, 5, 'Teclados', 24),
(33, 5, 'PC', 24),
(34, 5, 'Monitores', 24),
(35, 5, 'Cable red', 24),
(36, 5, 'Multiconectores', 0),
(40, 6, 'Maquinas', 22),
(41, 6, 'Teclados', 22),
(42, 6, 'Mouses', 18),
(43, 6, 'Gabinete', 20),
(44, 6, 'Adaptador', 15),
(45, 6, 'Router', 27),
(46, 6, 'Switch', 25),
(47, 6, 'CableSerial', 37),
(48, 6, 'SmartSerial', 14),
(49, 6, 'CableDirecto', 71),
(50, 6, 'Cable SFP', 9),
(51, 6, 'Miniswitch', 2),
(52, 6, 'Access Point', 3),
(53, 6, 'TV', 1),
(54, 6, 'HDMI', 1),
(55, 7, 'Reflectores', 3),
(56, 7, 'Set de luces', 2),
(57, 7, 'Conexión', 1),
(58, 7, 'Maquinas', 2),
(59, 7, 'Teclados', 2),
(60, 7, 'Mouses', 2),
(61, 7, 'Adaptador', 2),
(62, 7, 'Varilla de Riel (Camara)', 1),
(63, 7, 'Lentes Realidad Virtual con Cable USB', 1),
(64, 7, 'Caja de Luz', 2),
(65, 7, 'Adaptador Universal de Conectores Eléctricos', 2),
(66, 7, 'Bocinas', 3),
(67, 7, 'Tapón para Camara', 1),
(68, 7, 'Cámara', 1),
(69, 7, 'Tableta Gráfica', 1),
(70, 7, 'Interfaz de Audio', 1),
(71, 7, 'Conectores', 1),
(72, 7, 'Control', 1),
(73, 7, 'Cajas con Adaptadores', 2),
(74, 7, 'Filamento de Impresora 3D', 1),
(75, 7, 'Especialista en Filamento', 1),
(76, 8, 'USP', 2),
(77, 8, 'Mesas', 14),
(78, 8, 'Sillas', 25),
(79, 8, 'Maquinas', 16),
(80, 8, 'Teclados', 15),
(81, 8, 'Mouses', 11),
(82, 8, 'Gabinetes', 34),
(83, 8, 'Adaptador', 7),
(84, 8, 'Cable Serial', 8),
(85, 8, 'Cable Directo', 8),
(86, 8, 'Computadoras Antiguas', 2),
(87, 8, 'Teclado Antiguo', 1),
(88, 8, 'Gabinete Antiguo', 1),
(89, 8, 'Servidor de RACK', 1),
(90, 8, 'Switch', 2),
(91, 1, 'TV', 1),
(92, 1, 'HDMI', 1),
(93, 2, 'TV', 1),
(94, 2, 'HDMI', 1),
(95, 3, 'TV', 1),
(96, 3, 'HDMI', 1),
(97, 4, 'TV', 1),
(98, 4, 'HDMI', 1),
(99, 5, 'TV', 1),
(100, 5, 'HDMI', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `maestros`
--

CREATE TABLE `maestros` (
  `id` int(11) NOT NULL,
  `empleado` varchar(20) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `maestros`
--

INSERT INTO `maestros` (`id`, `empleado`, `nombre`, `password`, `fecha_registro`) VALUES
(1, '990619', 'Patricia Guadalupe Mora González', 'paty01', '2025-06-19 14:02:04'),
(4, '999999', 'Javier', '02', '2025-06-30 15:39:28'),
(6, '994499', 'López Belmares David', 'dbelmares', '2025-07-06 21:22:13'),
(7, '990988', 'Jose Huerta', 'jhuerta01', '2025-07-11 14:49:38');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reportes`
--

CREATE TABLE `reportes` (
  `id` int(11) NOT NULL,
  `maestro_id` int(11) NOT NULL,
  `laboratorio` int(11) NOT NULL,
  `observaciones` text NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `componentes` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reportes`
--

INSERT INTO `reportes` (`id`, `maestro_id`, `laboratorio`, `observaciones`, `fecha`, `componentes`) VALUES
(13, 1, 3, 'hola', '2025-07-07 20:09:32', '{\"Maquinas\":\"19\",\"Mouses\":\"4\",\"Teclados\":\"19\",\"PC\":\"19\",\"Monitores\":\"19\",\"Cable red\":\"15\",\"Multiconectores\":\"4\",\"TV\":\"1\",\"HDMI\":\"1\"}'),
(40, 1, 1, 'hollaaaaa', '2025-07-07 22:12:16', '{\"Maquinas\":25,\"Mouses\":21,\"Teclados\":25,\"PC\":24,\"Monitores\":25,\"Cable red\":21,\"Multiconectores\":5,\"TV\":1,\"HDMI\":1}');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesos`
--
ALTER TABLE `accesos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos`
--
ALTER TABLE `grupos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `grupos_maestros`
--
ALTER TABLE `grupos_maestros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `ingresos_usuarios`
--
ALTER TABLE `ingresos_usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `laboratorio_componentes`
--
ALTER TABLE `laboratorio_componentes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `maestros`
--
ALTER TABLE `maestros`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reportes`
--
ALTER TABLE `reportes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesos`
--
ALTER TABLE `accesos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `grupos`
--
ALTER TABLE `grupos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `ingresos_usuarios`
--
ALTER TABLE `ingresos_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT de la tabla `laboratorios`
--
ALTER TABLE `laboratorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `laboratorio_componentes`
--
ALTER TABLE `laboratorio_componentes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT de la tabla `maestros`
--
ALTER TABLE `maestros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `reportes`
--
ALTER TABLE `reportes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
