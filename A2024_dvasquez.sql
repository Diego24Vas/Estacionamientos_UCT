-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Servidor: db
-- Tiempo de generación: 30-05-2025 a las 17:39:49
-- Versión del servidor: 10.6.18-MariaDB-0ubuntu0.22.04.1
-- Versión de PHP: 8.1.2-1ubuntu2.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `A2024_dvasquez`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1118_Lab03`
--

CREATE TABLE `INFO1118_Lab03` (
  `Nombre` varchar(20) NOT NULL,
  `Genero` varchar(20) NOT NULL,
  `Precio` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1118_Lab03`
--

INSERT INTO `INFO1118_Lab03` (`Nombre`, `Genero`, `Precio`) VALUES
('horizon', 'aventura', 12),
('spiderman', 'hero', 120),
('spiderman 2', 'hero', 120),
('hitman', 'accion', 1234),
('supermarquet', 'accion', 1234),
('pepe', 'si', 203),
('pepe', 'si', 203),
('minecraft', 'aventura', 110101),
('candy crash', 'casual', 9999),
('candy crash', 'casual', 9999),
('a', 'a', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1118_Lab05`
--

CREATE TABLE `INFO1118_Lab05` (
  `usuario` varchar(10) NOT NULL,
  `clave` char(32) NOT NULL,
  `nombre` varchar(10) NOT NULL,
  `apellido` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1118_Lab05`
--

INSERT INTO `INFO1118_Lab05` (`usuario`, `clave`, `nombre`, `apellido`) VALUES
('juan33', '81dc9bdb52d04dc20036dbd8313ed055', 'juan', 'perez'),
('ucetito', '8542516f8870173d7d1daba1daaaf0a1', 'panchito', 'de la ros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_Estacionamiento`
--

CREATE TABLE `INFO1170_Estacionamiento` (
  `IdEstacionamiento` varchar(4) NOT NULL,
  `Ubicacion` varchar(50) DEFAULT NULL,
  `Estado` enum('Disponible','Ocupado','Reservado') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1170_Estacionamiento`
--

INSERT INTO `INFO1170_Estacionamiento` (`IdEstacionamiento`, `Ubicacion`, `Estado`) VALUES
('A01', 'Zona A', 'Ocupado'),
('A02', 'Zona A', 'Ocupado'),
('A03', 'Zona A', 'Ocupado'),
('A04', 'Zona A', 'Ocupado'),
('A05', 'Zona A', 'Ocupado'),
('A06', 'Zona A', 'Ocupado'),
('A07', 'Zona A', 'Ocupado'),
('A08', 'Zona A', 'Ocupado'),
('A09', 'Zona A', 'Ocupado'),
('A10', 'Zona A', 'Ocupado'),
('A11', 'Zona A', 'Ocupado'),
('A12', 'Zona A', 'Ocupado'),
('A13', 'Zona A', 'Ocupado'),
('A14', 'Zona A', 'Disponible'),
('A15', 'Zona A', 'Disponible'),
('A16', 'Zona A', 'Ocupado'),
('A17', 'Zona A', 'Ocupado'),
('A18', 'Zona A', 'Ocupado'),
('A19', 'Zona A', 'Ocupado'),
('A20', 'Zona A', 'Disponible'),
('A21', 'Zona A', 'Ocupado'),
('A22', 'Zona A', 'Ocupado'),
('A23', 'Zona A', 'Ocupado'),
('A24', 'Zona A', 'Disponible'),
('A25', 'Zona A', 'Disponible'),
('A26', 'Zona A', 'Disponible'),
('A27', 'Zona A', 'Ocupado'),
('A28', 'Zona A', 'Disponible'),
('A29', 'Zona A', 'Disponible'),
('A30', 'Zona A', 'Disponible'),
('A31', 'Zona A', 'Disponible'),
('A32', 'Zona A', 'Disponible'),
('A33', 'Zona A', 'Disponible'),
('A34', 'Zona A', 'Disponible'),
('A35', 'Zona A', 'Disponible'),
('A36', 'Zona A', 'Disponible'),
('A37', 'Zona A', 'Disponible'),
('A38', 'Zona A', 'Disponible'),
('A39', 'Zona A', 'Disponible'),
('A40', 'Zona A', 'Disponible'),
('A41', 'Zona A', 'Disponible'),
('A42', 'Zona A', 'Disponible'),
('A43', 'Zona A', 'Disponible'),
('A44', 'Zona A', 'Disponible'),
('A45', 'Zona A', 'Disponible'),
('A46', 'Zona A', 'Disponible'),
('A47', 'Zona A', 'Disponible'),
('A48', 'Zona A', 'Disponible'),
('A49', 'Zona A', 'Disponible'),
('A50', 'Zona A', 'Disponible'),
('A51', 'Zona A', 'Disponible'),
('A52', 'Zona A', 'Disponible'),
('A53', 'Zona A', 'Disponible'),
('A54', 'Zona A', 'Disponible'),
('A55', 'Zona A', 'Disponible'),
('A56', 'Zona A', 'Disponible'),
('A57', 'Zona A', 'Disponible'),
('A58', 'Zona A', 'Disponible'),
('A59', 'Zona A', 'Disponible'),
('B1', 'Zona B', 'Ocupado'),
('B10', 'Zona B', 'Disponible'),
('B100', 'Zona B', 'Ocupado'),
('B101', 'Zona B', 'Ocupado'),
('B102', 'Zona B', 'Ocupado'),
('B103', 'Zona B', 'Ocupado'),
('B104', 'Zona B', 'Ocupado'),
('B105', 'Zona B', 'Ocupado'),
('B106', 'Zona B', 'Disponible'),
('B107', 'Zona B', 'Disponible'),
('B108', 'Zona B', 'Disponible'),
('B109', 'Zona B', 'Disponible'),
('B11', 'Zona B', 'Ocupado'),
('B110', 'Zona B', 'Ocupado'),
('B12', 'Zona B', 'Ocupado'),
('B13', 'Zona B', 'Disponible'),
('B14', 'Zona B', 'Ocupado'),
('B15', 'Zona B', 'Disponible'),
('B16', 'Zona B', 'Ocupado'),
('B17', 'Zona B', 'Ocupado'),
('B18', 'Zona B', 'Disponible'),
('B19', 'Zona B', 'Ocupado'),
('B2', 'Zona B', 'Ocupado'),
('B20', 'Zona B', 'Disponible'),
('B21', 'Zona B', 'Ocupado'),
('B22', 'Zona B', 'Disponible'),
('B23', 'Zona B', 'Disponible'),
('B24', 'Zona B', 'Disponible'),
('B25', 'Zona B', 'Disponible'),
('B26', 'Zona B', 'Disponible'),
('B27', 'Zona B', 'Disponible'),
('B28', 'Zona B', 'Disponible'),
('B29', 'Zona B', 'Disponible'),
('B3', 'Zona B', 'Disponible'),
('B30', 'Zona B', 'Disponible'),
('B31', 'Zona B', 'Disponible'),
('B32', 'Zona B', 'Disponible'),
('B33', 'Zona B', 'Disponible'),
('B34', 'Zona B', 'Disponible'),
('B35', 'Zona B', 'Disponible'),
('B36', 'Zona B', 'Disponible'),
('B37', 'Zona B', 'Disponible'),
('B38', 'Zona B', 'Disponible'),
('B39', 'Zona B', 'Disponible'),
('B4', 'Zona B', 'Disponible'),
('B40', 'Zona B', 'Disponible'),
('B41', 'Zona B', 'Disponible'),
('B42', 'Zona B', 'Disponible'),
('B43', 'Zona B', 'Disponible'),
('B44', 'Zona B', 'Disponible'),
('B45', 'Zona B', 'Disponible'),
('B46', 'Zona B', 'Disponible'),
('B47', 'Zona B', 'Disponible'),
('B48', 'Zona B', 'Disponible'),
('B49', 'Zona B', 'Disponible'),
('B5', 'Zona B', 'Disponible'),
('B50', 'Zona B', 'Disponible'),
('B51', 'Zona B', 'Disponible'),
('B52', 'Zona B', 'Disponible'),
('B53', 'Zona B', 'Disponible'),
('B54', 'Zona B', 'Disponible'),
('B55', 'Zona B', 'Disponible'),
('B56', 'Zona B', 'Disponible'),
('B57', 'Zona B', 'Disponible'),
('B58', 'Zona B', 'Disponible'),
('B59', 'Zona B', 'Disponible'),
('B6', 'Zona B', 'Disponible'),
('B60', 'Zona B', 'Disponible'),
('B61', 'Zona B', 'Disponible'),
('B62', 'Zona B', 'Disponible'),
('B63', 'Zona B', 'Disponible'),
('B64', 'Zona B', 'Disponible'),
('B65', 'Zona B', 'Disponible'),
('B66', 'Zona B', 'Disponible'),
('B67', 'Zona B', 'Disponible'),
('B68', 'Zona B', 'Disponible'),
('B69', 'Zona B', 'Disponible'),
('B7', 'Zona B', 'Disponible'),
('B70', 'Zona B', 'Disponible'),
('B71', 'Zona B', 'Disponible'),
('B72', 'Zona B', 'Disponible'),
('B73', 'Zona B', 'Disponible'),
('B74', 'Zona B', 'Disponible'),
('B75', 'Zona B', 'Disponible'),
('B76', 'Zona B', 'Disponible'),
('B77', 'Zona B', 'Disponible'),
('B78', 'Zona B', 'Disponible'),
('B79', 'Zona B', 'Disponible'),
('B8', 'Zona B', 'Disponible'),
('B80', 'Zona B', 'Disponible'),
('B81', 'Zona B', 'Disponible'),
('B82', 'Zona B', 'Disponible'),
('B83', 'Zona B', 'Disponible'),
('B84', 'Zona B', 'Disponible'),
('B85', 'Zona B', 'Disponible'),
('B86', 'Zona B', 'Disponible'),
('B87', 'Zona B', 'Disponible'),
('B88', 'Zona B', 'Disponible'),
('B89', 'Zona B', 'Disponible'),
('B9', 'Zona B', 'Disponible'),
('B90', 'Zona B', 'Disponible'),
('B91', 'Zona B', 'Disponible'),
('B92', 'Zona B', 'Disponible'),
('B93', 'Zona B', 'Disponible'),
('B94', 'Zona B', 'Disponible'),
('B95', 'Zona B', 'Disponible'),
('B96', 'Zona B', 'Disponible'),
('B97', 'Zona B', 'Disponible'),
('B98', 'Zona B', 'Disponible'),
('B99', 'Zona B', 'Disponible'),
('C1', 'Zona C', 'Disponible'),
('C10', 'Zona C', 'Disponible'),
('C11', 'Zona C', 'Disponible'),
('C12', 'Zona C', 'Disponible'),
('C13', 'Zona C', 'Disponible'),
('C14', 'Zona C', 'Disponible'),
('C15', 'Zona C', 'Disponible'),
('C16', 'Zona C', 'Disponible'),
('C17', 'Zona C', 'Disponible'),
('C18', 'Zona C', 'Disponible'),
('C19', 'Zona C', 'Disponible'),
('C2', 'Zona C', 'Disponible'),
('C20', 'Zona C', 'Disponible'),
('C21', 'Zona C', 'Ocupado'),
('C22', 'Zona C', 'Disponible'),
('C23', 'Zona C', 'Disponible'),
('C24', 'Zona C', 'Ocupado'),
('C25', 'Zona C', 'Disponible'),
('C26', 'Zona C', 'Disponible'),
('C27', 'Zona C', 'Disponible'),
('C28', 'Zona C', 'Disponible'),
('C29', 'Zona C', 'Disponible'),
('C3', 'Zona C', 'Disponible'),
('C30', 'Zona C', 'Disponible'),
('C31', 'Zona C', 'Disponible'),
('C32', 'Zona C', 'Disponible'),
('C33', 'Zona C', 'Disponible'),
('C34', 'Zona C', 'Disponible'),
('C35', 'Zona C', 'Disponible'),
('C36', 'Zona C', 'Disponible'),
('C37', 'Zona C', 'Disponible'),
('C38', 'Zona C', 'Disponible'),
('C39', 'Zona C', 'Disponible'),
('C4', 'Zona C', 'Disponible'),
('C40', 'Zona C', 'Disponible'),
('C41', 'Zona C', 'Disponible'),
('C42', 'Zona C', 'Disponible'),
('C43', 'Zona C', 'Disponible'),
('C44', 'Zona C', 'Disponible'),
('C45', 'Zona C', 'Disponible'),
('C46', 'Zona C', 'Disponible'),
('C47', 'Zona C', 'Disponible'),
('C48', 'Zona C', 'Disponible'),
('C49', 'Zona C', 'Disponible'),
('C5', 'Zona C', 'Disponible'),
('C50', 'Zona C', 'Disponible'),
('C51', 'Zona C', 'Disponible'),
('C52', 'Zona C', 'Disponible'),
('C53', 'Zona C', 'Disponible'),
('C54', 'Zona C', 'Disponible'),
('C55', 'Zona C', 'Disponible'),
('C56', 'Zona C', 'Disponible'),
('C57', 'Zona C', 'Disponible'),
('C58', 'Zona C', 'Disponible'),
('C59', 'Zona C', 'Disponible'),
('C6', 'Zona C', 'Disponible'),
('C60', 'Zona C', 'Disponible'),
('C61', 'Zona C', 'Disponible'),
('C62', 'Zona C', 'Disponible'),
('C63', 'Zona C', 'Disponible'),
('C64', 'Zona C', 'Disponible'),
('C65', 'Zona C', 'Disponible'),
('C66', 'Zona C', 'Disponible'),
('C67', 'Zona C', 'Disponible'),
('C68', 'Zona C', 'Disponible'),
('C69', 'Zona C', 'Disponible'),
('C7', 'Zona C', 'Disponible'),
('C70', 'Zona C', 'Disponible'),
('C71', 'Zona C', 'Disponible'),
('C72', 'Zona C', 'Disponible'),
('C73', 'Zona C', 'Disponible'),
('C74', 'Zona C', 'Disponible'),
('C75', 'Zona C', 'Disponible'),
('C76', 'Zona C', 'Disponible'),
('C77', 'Zona C', 'Disponible'),
('C78', 'Zona C', 'Disponible'),
('C79', 'Zona C', 'Disponible'),
('C8', 'Zona C', 'Disponible'),
('C80', 'Zona C', 'Disponible'),
('C81', 'Zona C', 'Disponible'),
('C82', 'Zona C', 'Disponible'),
('C83', 'Zona C', 'Disponible'),
('C84', 'Zona C', 'Disponible'),
('C85', 'Zona C', 'Disponible'),
('C86', 'Zona C', 'Disponible'),
('C87', 'Zona C', 'Disponible'),
('C88', 'Zona C', 'Disponible'),
('C89', 'Zona C', 'Disponible'),
('C9', 'Zona C', 'Disponible'),
('C90', 'Zona C', 'Disponible'),
('C91', 'Zona C', 'Disponible'),
('C92', 'Zona C', 'Disponible'),
('C93', 'Zona C', 'Disponible'),
('C94', 'Zona C', 'Disponible'),
('D1', 'Zona D', 'Disponible'),
('D10', 'Zona D', 'Disponible'),
('D11', 'Zona D', 'Disponible'),
('D12', 'Zona D', 'Disponible'),
('D13', 'Zona D', 'Disponible'),
('D14', 'Zona D', 'Disponible'),
('D15', 'Zona D', 'Disponible'),
('D16', 'Zona D', 'Disponible'),
('D17', 'Zona D', 'Disponible'),
('D18', 'Zona D', 'Disponible'),
('D19', 'Zona D', 'Disponible'),
('D2', 'Zona D', 'Disponible'),
('D20', 'Zona D', 'Disponible'),
('D21', 'Zona D', 'Disponible'),
('D22', 'Zona D', 'Disponible'),
('D23', 'Zona D', 'Disponible'),
('D24', 'Zona D', 'Disponible'),
('D25', 'Zona D', 'Disponible'),
('D26', 'Zona D', 'Disponible'),
('D3', 'Zona D', 'Disponible'),
('D4', 'Zona D', 'Disponible'),
('D5', 'Zona D', 'Disponible'),
('D6', 'Zona D', 'Disponible'),
('D7', 'Zona D', 'Disponible'),
('D8', 'Zona D', 'Disponible'),
('D9', 'Zona D', 'Disponible');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_HistorialRegistros`
--

CREATE TABLE `INFO1170_HistorialRegistros` (
  `IdRegistro` int(11) NOT NULL,
  `IdVehiculo` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `accion` enum('Entrada','Salida') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1170_HistorialRegistros`
--

INSERT INTO `INFO1170_HistorialRegistros` (`IdRegistro`, `IdVehiculo`, `fecha`, `accion`) VALUES
(1, 10, '2024-11-25 19:46:57', 'Entrada'),
(2, 10, '2024-11-25 19:55:53', 'Salida'),
(8, 14, '2024-11-25 21:18:00', 'Entrada'),
(10, 14, '2024-11-25 21:18:19', 'Salida'),
(11, 15, '2024-11-26 20:31:23', 'Entrada'),
(12, 16, '2024-11-26 20:32:20', 'Entrada'),
(13, 16, '2024-11-26 20:33:15', 'Salida'),
(15, 17, '2024-11-27 00:48:16', 'Entrada'),
(16, 17, '2024-11-27 00:48:32', 'Salida'),
(17, 17, '2024-11-27 00:48:58', 'Entrada'),
(18, 18, '2024-11-27 01:32:51', 'Entrada'),
(19, 18, '2024-11-27 01:33:17', 'Salida'),
(20, 19, '2024-11-27 02:38:24', 'Entrada'),
(21, 20, '2024-11-27 05:17:25', 'Entrada'),
(22, 21, '2024-11-27 05:56:46', 'Entrada'),
(23, 22, '2024-11-27 16:40:09', 'Entrada'),
(24, 1, '2024-11-27 17:17:44', 'Salida'),
(25, 2, '2024-11-27 17:18:02', 'Salida'),
(26, 23, '2024-11-27 17:30:23', 'Entrada'),
(27, 23, '2024-11-27 17:36:16', 'Salida'),
(28, 24, '2025-03-23 20:20:09', 'Entrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_MarcasVehiculos`
--

CREATE TABLE `INFO1170_MarcasVehiculos` (
  `id` int(11) NOT NULL,
  `nombre_marca` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1170_MarcasVehiculos`
--

INSERT INTO `INFO1170_MarcasVehiculos` (`id`, `nombre_marca`) VALUES
(62, 'ALFA ROMEO'),
(43, 'AUDI'),
(34, 'BAIC'),
(72, 'BENTLEY'),
(26, 'BMW'),
(61, 'BRILLIANCE'),
(37, 'BYD'),
(10, 'CHANGAN'),
(14, 'CHERY'),
(2, 'CHEVROLET'),
(15, 'CITROEN'),
(47, 'CUPR'),
(33, 'DFM'),
(25, 'DFSK'),
(45, 'DONG FENG'),
(58, 'DS'),
(42, 'EXEED'),
(65, 'FARIZON'),
(67, 'FERRARI'),
(46, 'FIAT'),
(6, 'FORD'),
(19, 'FOTON'),
(64, 'FUSO'),
(27, 'GAC MOTOR'),
(32, 'GEELY'),
(7, 'GWM'),
(30, 'HONDA'),
(1, 'HYUNDAI'),
(60, 'IVECO'),
(18, 'JAC'),
(50, 'JAECOO'),
(63, 'JAGUAR'),
(36, 'JEEP'),
(20, 'JETOUR'),
(51, 'JIM'),
(24, 'JMC'),
(38, 'KAIYI'),
(44, 'KARRY'),
(4, 'KIA'),
(54, 'LAND ROVER'),
(70, 'LANDKING'),
(69, 'LEAPMOTOR'),
(53, 'LEXUS'),
(66, 'LIVAN'),
(40, 'MAHINDRA'),
(68, 'MASERATI'),
(12, 'MAXUS'),
(13, 'MAZDA'),
(29, 'MERCEDES BENZ'),
(8, 'MG'),
(52, 'MINI'),
(9, 'MITSUBISHI'),
(71, 'NETA'),
(11, 'NISSAN'),
(28, 'OMODA'),
(22, 'OPEL'),
(5, 'PEUGEOT'),
(56, 'PORSCHE'),
(23, 'RAM'),
(31, 'RENAULT'),
(57, 'SEAT'),
(48, 'SHINERAY'),
(49, 'SKODA'),
(21, 'SSANGYONG'),
(16, 'SUBARU'),
(3, 'SUZUKI'),
(39, 'SWM'),
(41, 'TESLA'),
(17, 'VOLKSWAGEN'),
(35, 'VOLVO'),
(59, 'ZNA'),
(55, 'ZXAUTO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_RecuperacionPassword`
--

CREATE TABLE `INFO1170_RecuperacionPassword` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(100) NOT NULL,
  `expira` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_RegistroUsuarios`
--

CREATE TABLE `INFO1170_RegistroUsuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contraseña` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1170_RegistroUsuarios`
--

INSERT INTO `INFO1170_RegistroUsuarios` (`id`, `nombre`, `email`, `contraseña`) VALUES
(27, 'rpedraza', 'rpedraza2024@alu.uct.cl', '$2y$10$0fMAAmWTm4THsgmTlJ89kOumJ/3KnjZ.wm4W3ZyNjco20hLN5THgy'),
(28, 'rorr', 'rpedraza2024a@alu.uct.cl', '$2y$10$9xjoRt2sjF0KAQFSvWnuQ.TOgLVNAl92XD/uuKTy83MJQQ4mp/lGy'),
(29, 'rori', 'asdksj024@alu.uct.cl', '$2y$10$jiAmf52hyaLoMagikeJhgum/VLmt.U7t9Ea3Pk6KZnVJjHn04yRju'),
(30, 'roori', 'roori@gmail.com', '$2y$10$A.F73ghtulIiN6Awx84vtupnKsMwMZXLyV2Yo3aO7C7o603lMkQte'),
(31, 'malatin', 'matalk@gmail.com', '$2y$10$wcark7zSWI5zxWRn.gFvtOUXMeJfiFHM3HGMFaayzJ3IcNC.8FF/q'),
(32, 'pichulin', 'pichulitacorta3cm@gmail.com', '$2y$10$0g6fcDIadqn0s5HEHGqYmOKsi3AzpklQa.R.yPO0TZofsTBw.H4p2'),
(33, 'PICHULON', 'tengolatulalarga24cm@mastersex.com', '$2y$10$44xpC3ScyILmrZHHrS7/YehDnOBxOb.HiHfygh14mQAsO1j09NcQC'),
(34, 'admin', 'admin@gmail.com', '$2y$10$R6vz4Oz7FxSJxsX0RisCyeGrBUE4frJhwpSr7OryrJUChOyV9dA92'),
(35, 'vixomatu', 'vixomatu@gmail.com', '$2y$10$ITca7SBD5Rd.3fIRq5dgLeHW6uSn/8v3L4UppCy.hpTp6gn10bN2u'),
(36, 'hola', 'dp8773958@gmail.com', '$2y$10$hv6R5b1ACxuGk6cnbpFYZevkRMjThM0G/Uc21kRpT7g2jXmJfE40y'),
(37, 'administrador1', 'administrador1@uct.cl', '$2y$10$Mg6iUu3fT3iIOlE.ewPFHe7AKZvht1pukQg98RFK0iOHy6lMYQYtu'),
(38, 'Administrador2', 'administrador2@uct.cl', '$2y$10$cP4q2mXE6BZmA6Q4VuR9KebhyrR9fthduR/SBVXmY3KVESf8AOKki'),
(39, 'Carito', 'asda@adksjad.com', '$2y$10$TlkR7faDX8bOfVQDXS5TvOZXrJgrBDdDHv55dVbbNeiH2CtD86UsW'),
(40, 'ADMINISTRADOR', 'askdjka@gmail.com', '$2y$10$Ec5p9nZgXw40/Fvoy3pCQ.DDBuSyQPCeT8LZ1mwwSUrQMr1oeimeG');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_Reservas`
--

CREATE TABLE `INFO1170_Reservas` (
  `id` int(11) NOT NULL,
  `evento` varchar(255) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `usuario` varchar(255) NOT NULL,
  `patente` varchar(10) NOT NULL,
  `zona` varchar(10) NOT NULL,
  `usuario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `INFO1170_VehiculosRegistrados`
--

CREATE TABLE `INFO1170_VehiculosRegistrados` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `patente` varchar(20) NOT NULL,
  `espacio_estacionamiento` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `INFO1170_VehiculosRegistrados`
--

INSERT INTO `INFO1170_VehiculosRegistrados` (`id`, `nombre`, `apellido`, `patente`, `espacio_estacionamiento`) VALUES
(1, 'ANTI', 'TONI', 'MARK10', 'B10'),
(2, 'CHENCHO', 'CORLEONE', 'CHEN69', 'B10'),
(3, 'PEREZ', 'ANTONIO', 'JKKS44', 'A07'),
(4, 'TONY ', 'MONTANA', 'IIKW1', 'A08'),
(5, 'PERE', 'MARTINEZ', 'KKNJ22', 'A09'),
(6, 'JUANA', 'DEL CARMEN', 'KISJ12', 'A10'),
(7, 'MARTIN', 'SANHUEZA', 'PKNS21', 'A10'),
(8, 'JUAN', 'CARO', 'KKNS12', 'B100'),
(10, 'MATIAS', 'CARCAMO', 'YDAS67', 'A12'),
(14, 'SDFGHJ', 'BNIBHY', '78BX4D', 'A13'),
(15, 'DFSFDS', 'ACSAVADS', 'ASDQ12', 'A12'),
(16, 'SCASVAS', 'ASCEWBVFDS', 'ASD123', 'A13'),
(17, 'VICENTE', 'MATUS', 'DJFP54', 'B101'),
(18, 'ZCXBXJ', 'XZCZXC', 'ZXCR34', 'A13'),
(19, 'LUIS', 'VERGARA', '34JKDJK', 'B101'),
(20, 'LALO', 'LOA', 'SFDF12', 'B102'),
(21, 'NICOLA', 'MARTINEZ', 'NFKS23', 'B103'),
(22, 'ARMANDO', 'ESTEBAN QUITO', 'BFZW42', 'B105'),
(23, 'USUARIO', 'GOD', 'JSDO93', 'B10'),
(24, 'DANIEL', 'PRADO', 'TYFS34', 'A13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `test`
--

CREATE TABLE `test` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `INFO1170_Estacionamiento`
--
ALTER TABLE `INFO1170_Estacionamiento`
  ADD PRIMARY KEY (`IdEstacionamiento`);

--
-- Indices de la tabla `INFO1170_HistorialRegistros`
--
ALTER TABLE `INFO1170_HistorialRegistros`
  ADD PRIMARY KEY (`IdRegistro`),
  ADD KEY `IdVehiculo` (`IdVehiculo`);

--
-- Indices de la tabla `INFO1170_MarcasVehiculos`
--
ALTER TABLE `INFO1170_MarcasVehiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nombre_marca` (`nombre_marca`);

--
-- Indices de la tabla `INFO1170_RecuperacionPassword`
--
ALTER TABLE `INFO1170_RecuperacionPassword`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `INFO1170_RegistroUsuarios`
--
ALTER TABLE `INFO1170_RegistroUsuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `INFO1170_Reservas`
--
ALTER TABLE `INFO1170_Reservas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `INFO1170_VehiculosRegistrados`
--
ALTER TABLE `INFO1170_VehiculosRegistrados`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `test`
--
ALTER TABLE `test`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `INFO1170_HistorialRegistros`
--
ALTER TABLE `INFO1170_HistorialRegistros`
  MODIFY `IdRegistro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `INFO1170_MarcasVehiculos`
--
ALTER TABLE `INFO1170_MarcasVehiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT de la tabla `INFO1170_RecuperacionPassword`
--
ALTER TABLE `INFO1170_RecuperacionPassword`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `INFO1170_RegistroUsuarios`
--
ALTER TABLE `INFO1170_RegistroUsuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT de la tabla `INFO1170_Reservas`
--
ALTER TABLE `INFO1170_Reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `INFO1170_VehiculosRegistrados`
--
ALTER TABLE `INFO1170_VehiculosRegistrados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `test`
--
ALTER TABLE `test`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `INFO1170_HistorialRegistros`
--
ALTER TABLE `INFO1170_HistorialRegistros`
  ADD CONSTRAINT `INFO1170_HistorialRegistros_ibfk_1` FOREIGN KEY (`IdVehiculo`) REFERENCES `INFO1170_VehiculosRegistrados` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `INFO1170_RecuperacionPassword`
--
ALTER TABLE `INFO1170_RecuperacionPassword`
  ADD CONSTRAINT `INFO1170_RecuperacionPassword_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `INFO1170_RegistroUsuarios` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
