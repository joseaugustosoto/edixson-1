-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2025 a las 00:34:36
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
-- Base de datos: `gestion_pnf`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aldea`
--

CREATE TABLE `aldea` (
  `aldea_id` int(11) NOT NULL,
  `nombre_aldea` varchar(200) NOT NULL,
  `direccion` text DEFAULT NULL,
  `parroquia_id` int(11) NOT NULL,
  `estado` enum('Activa','Inactiva') NOT NULL DEFAULT 'Activa'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena las Aldeas (Instituciones)';

--
-- Volcado de datos para la tabla `aldea`
--

INSERT INTO `aldea` (`aldea_id`, `nombre_aldea`, `direccion`, `parroquia_id`, `estado`) VALUES
(1, 'Aldea Bolivariana Cuatricentenario', 'Urb. Raúl Leoni, X Etapa, Av. XX', 8, 'Activa'),
(2, 'Aldea Bolivariana Máximo Arteaga Pérez', 'Barrio Bolívar, entrando por \"El Pescaito\"', 8, 'Activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aldea_pnf`
--

CREATE TABLE `aldea_pnf` (
  `aldea_pnf_id` int(11) NOT NULL,
  `aldea_id` int(11) NOT NULL,
  `pnf_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Tabla de unión PNF-Aldea (Oferta Académica)';

--
-- Volcado de datos para la tabla `aldea_pnf`
--

INSERT INTO `aldea_pnf` (`aldea_pnf_id`, `aldea_id`, `pnf_id`) VALUES
(5, 1, 2),
(7, 1, 3),
(6, 2, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `datos_personales`
--

CREATE TABLE `datos_personales` (
  `datos_personales_id` int(11) NOT NULL,
  `nacionalidad` enum('V','E','P') NOT NULL COMMENT 'V: Venezolano, E: Extranjero, P: Pasaporte',
  `cedula` varchar(20) NOT NULL,
  `primer_nombre` varchar(50) NOT NULL,
  `segundo_nombre` varchar(50) DEFAULT NULL,
  `primer_apellido` varchar(50) NOT NULL,
  `segundo_apellido` varchar(50) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `estado_civil` enum('Soltero','Casado','Divorciado','Viudo') NOT NULL,
  `sexo` enum('M','F') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los datos personales de los usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direccion_habitacion`
--

CREATE TABLE `direccion_habitacion` (
  `direccion_id` int(11) NOT NULL,
  `datos_personales_id` int(11) NOT NULL,
  `estado_id` int(11) NOT NULL,
  `municipio_id` int(11) NOT NULL,
  `parroquia_id` int(11) NOT NULL,
  `barrio_sector` varchar(256) DEFAULT NULL,
  `avenida` varchar(100) DEFAULT NULL,
  `calle` varchar(100) DEFAULT NULL,
  `casa_apto` varchar(100) DEFAULT NULL,
  `referencia` text DEFAULT NULL,
  `telefono_celular` varchar(15) NOT NULL,
  `telefono_otro` varchar(15) DEFAULT NULL,
  `correo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena la dirección de habitación de los usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado`
--

CREATE TABLE `estado` (
  `estado_id` int(11) NOT NULL,
  `nombre_estado` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `estado`
--

INSERT INTO `estado` (`estado_id`, `nombre_estado`) VALUES
(23, 'Zulia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `municipio`
--

CREATE TABLE `municipio` (
  `municipio_id` int(11) NOT NULL,
  `nombre_municipio` varchar(255) NOT NULL,
  `estado_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `municipio`
--

INSERT INTO `municipio` (`municipio_id`, `nombre_municipio`, `estado_id`) VALUES
(13, 'Maracaibo', 23);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `parroquia`
--

CREATE TABLE `parroquia` (
  `parroquia_id` int(11) NOT NULL,
  `nombre_parroquia` varchar(255) NOT NULL,
  `municipio_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `parroquia`
--

INSERT INTO `parroquia` (`parroquia_id`, `nombre_parroquia`, `municipio_id`) VALUES
(1, 'Antonio Borjas Romero', 13),
(2, 'Bolivar', 13),
(3, 'Cacique Mara', 13),
(4, 'Caracciolo Parra Pérez', 13),
(5, 'Cecilio Acosta', 13),
(6, 'Chiquinquirá', 13),
(7, 'Coquivacoa', 13),
(8, 'Francisco Eugenio Bustamante', 13),
(9, 'Idelfonso Vásquez', 13),
(10, 'Juana de Ávila', 13),
(11, 'Luis Hurtado Higuera', 13),
(12, 'Manuel Dagnino', 13),
(13, 'Olegario Villalobos', 13),
(14, 'Raúl Leoni', 13),
(15, 'Santa Lucía', 13),
(16, 'Venancio Pulgar', 13),
(17, 'San Isidro', 13),
(18, 'Cristo de Aranza', 13);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pnf`
--

CREATE TABLE `pnf` (
  `pnf_id` int(11) NOT NULL,
  `codigo_pnf` varchar(50) DEFAULT NULL,
  `nombre_pnf` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los Programas Nacionales de Formación';

--
-- Volcado de datos para la tabla `pnf`
--

INSERT INTO `pnf` (`pnf_id`, `codigo_pnf`, `nombre_pnf`, `descripcion`, `estado`) VALUES
(2, 'informatica', 'INFORMÁTICA', 'Informática', 'Activo'),
(3, 'educacion', 'EDUCACIÓN', 'Educación', 'Activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preinscripcion`
--

CREATE TABLE `preinscripcion` (
  `preinscripcion_id` int(11) NOT NULL,
  `datos_personales_id` int(11) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `pnf_id` int(11) NOT NULL,
  `trayecto` varchar(50) NOT NULL DEFAULT 'Inicial',
  `aldea_id` int(11) NOT NULL,
  `fecha_registro` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Almacena los datos de preinscripción de los usuarios';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `nombre_usuario` varchar(100) NOT NULL,
  `contrasena_hash` varchar(255) NOT NULL,
  `rol` varchar(50) NOT NULL DEFAULT 'Editor',
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Usuarios para el panel de control';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `nombre_usuario`, `contrasena_hash`, `rol`, `creado_en`) VALUES
(1, 'admin', '$2y$10$NxoWPG85rt.tT62UcwflQ.OiQGELV6q0RqFXrfuOgWgfPACyxqi1e', 'Admin', '2025-05-04 03:30:05');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `aldea`
--
ALTER TABLE `aldea`
  ADD PRIMARY KEY (`aldea_id`),
  ADD UNIQUE KEY `uq_nombre_aldea_parroquia` (`nombre_aldea`,`parroquia_id`),
  ADD KEY `fk_aldea_parroquia_idx` (`parroquia_id`);

--
-- Indices de la tabla `aldea_pnf`
--
ALTER TABLE `aldea_pnf`
  ADD PRIMARY KEY (`aldea_pnf_id`),
  ADD UNIQUE KEY `uq_aldea_pnf` (`aldea_id`,`pnf_id`),
  ADD KEY `fk_aldea_pnf_pnf_idx` (`pnf_id`);

--
-- Indices de la tabla `datos_personales`
--
ALTER TABLE `datos_personales`
  ADD PRIMARY KEY (`datos_personales_id`),
  ADD UNIQUE KEY `cedula` (`cedula`);

--
-- Indices de la tabla `direccion_habitacion`
--
ALTER TABLE `direccion_habitacion`
  ADD PRIMARY KEY (`direccion_id`),
  ADD KEY `estado_id` (`estado_id`),
  ADD KEY `municipio_id` (`municipio_id`),
  ADD KEY `parroquia_id` (`parroquia_id`),
  ADD KEY `direccion_habitacion_ibfk_1` (`datos_personales_id`);

--
-- Indices de la tabla `estado`
--
ALTER TABLE `estado`
  ADD PRIMARY KEY (`estado_id`);

--
-- Indices de la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD PRIMARY KEY (`municipio_id`),
  ADD KEY `estado_id` (`estado_id`);

--
-- Indices de la tabla `parroquia`
--
ALTER TABLE `parroquia`
  ADD PRIMARY KEY (`parroquia_id`),
  ADD KEY `municipio_id` (`municipio_id`);

--
-- Indices de la tabla `pnf`
--
ALTER TABLE `pnf`
  ADD PRIMARY KEY (`pnf_id`),
  ADD UNIQUE KEY `nombre_pnf` (`nombre_pnf`),
  ADD UNIQUE KEY `codigo_pnf` (`codigo_pnf`);

--
-- Indices de la tabla `preinscripcion`
--
ALTER TABLE `preinscripcion`
  ADD PRIMARY KEY (`preinscripcion_id`),
  ADD KEY `pnf_id` (`pnf_id`),
  ADD KEY `aldea_id` (`aldea_id`),
  ADD KEY `preinscripcion_ibfk_1` (`datos_personales_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`),
  ADD UNIQUE KEY `nombre_usuario` (`nombre_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `aldea`
--
ALTER TABLE `aldea`
  MODIFY `aldea_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `aldea_pnf`
--
ALTER TABLE `aldea_pnf`
  MODIFY `aldea_pnf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `datos_personales`
--
ALTER TABLE `datos_personales`
  MODIFY `datos_personales_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `direccion_habitacion`
--
ALTER TABLE `direccion_habitacion`
  MODIFY `direccion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `pnf`
--
ALTER TABLE `pnf`
  MODIFY `pnf_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `preinscripcion`
--
ALTER TABLE `preinscripcion`
  MODIFY `preinscripcion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `aldea`
--
ALTER TABLE `aldea`
  ADD CONSTRAINT `fk_aldea_parroquia` FOREIGN KEY (`parroquia_id`) REFERENCES `parroquia` (`parroquia_id`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `aldea_pnf`
--
ALTER TABLE `aldea_pnf`
  ADD CONSTRAINT `fk_aldea_pnf_aldea` FOREIGN KEY (`aldea_id`) REFERENCES `aldea` (`aldea_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aldea_pnf_pnf` FOREIGN KEY (`pnf_id`) REFERENCES `pnf` (`pnf_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direccion_habitacion`
--
ALTER TABLE `direccion_habitacion`
  ADD CONSTRAINT `direccion_habitacion_ibfk_1` FOREIGN KEY (`datos_personales_id`) REFERENCES `datos_personales` (`datos_personales_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direccion_habitacion_ibfk_2` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`estado_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direccion_habitacion_ibfk_3` FOREIGN KEY (`municipio_id`) REFERENCES `municipio` (`municipio_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `direccion_habitacion_ibfk_4` FOREIGN KEY (`parroquia_id`) REFERENCES `parroquia` (`parroquia_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `municipio`
--
ALTER TABLE `municipio`
  ADD CONSTRAINT `municipio_ibfk_1` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`estado_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `parroquia`
--
ALTER TABLE `parroquia`
  ADD CONSTRAINT `parroquia_ibfk_1` FOREIGN KEY (`municipio_id`) REFERENCES `municipio` (`municipio_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `preinscripcion`
--
ALTER TABLE `preinscripcion`
  ADD CONSTRAINT `preinscripcion_ibfk_1` FOREIGN KEY (`datos_personales_id`) REFERENCES `datos_personales` (`datos_personales_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `preinscripcion_ibfk_2` FOREIGN KEY (`pnf_id`) REFERENCES `pnf` (`pnf_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `preinscripcion_ibfk_3` FOREIGN KEY (`aldea_id`) REFERENCES `aldea` (`aldea_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
