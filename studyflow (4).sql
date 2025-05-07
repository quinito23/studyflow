-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 07-05-2025 a las 19:53:22
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
-- Base de datos: `studyflow`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno`
--

CREATE TABLE `alumno` (
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno`
--

INSERT INTO `alumno` (`id_usuario`) VALUES
(35),
(38),
(39);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_grupo`
--

CREATE TABLE `alumno_grupo` (
  `id_usuario` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_grupo`
--

INSERT INTO `alumno_grupo` (`id_usuario`, `id_grupo`) VALUES
(37, 1),
(38, 1),
(38, 2),
(38, 3),
(38, 4),
(39, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumno_tutor`
--

CREATE TABLE `alumno_tutor` (
  `id_usuario` int(11) NOT NULL,
  `id_tutor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alumno_tutor`
--

INSERT INTO `alumno_tutor` (`id_usuario`, `id_tutor`) VALUES
(8, 3),
(10, 5),
(33, 2),
(35, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anonimo`
--

CREATE TABLE `anonimo` (
  `id_anonimo` int(11) NOT NULL,
  `correo` varchar(255) NOT NULL,
  `contrasenia` varchar(255) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anonimo`
--

INSERT INTO `anonimo` (`id_anonimo`, `correo`, `contrasenia`, `nombre`, `apellidos`, `telefono`) VALUES
(1, 'tungsahur@studyflow.com', 'asdd32dqd', 'tung', 'sahur', '12312312312'),
(2, 'kinito@gmail.com', 'asdasd34', 'quinito', 'alias', '21312312'),
(3, 'avita@gmail.com', 'asdasd3242', 'eevita', 'dinamita', '213123123'),
(4, 'profesor@gmail.com', 'profe', 'profe', 'profe', '1123123123');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `asignatura`
--

CREATE TABLE `asignatura` (
  `id_asignatura` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `nivel` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `asignatura`
--

INSERT INTO `asignatura` (`id_asignatura`, `nombre`, `nivel`, `descripcion`, `id_usuario`) VALUES
(1, 'Matemáticas', 'Básico', 'Curso de matemáticas básicas', NULL),
(2, 'Física', 'Intermedio', 'Introducción a la física clásica', 37),
(3, 'Programación', 'Avanzado', 'Fundamentos de programación en Python', NULL),
(4, 'Historia', 'Básico', 'Historia mundial del siglo XX', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `aula`
--

CREATE TABLE `aula` (
  `id_aula` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `capacidad` int(11) NOT NULL,
  `estado` enum('libre','reservada') DEFAULT 'libre',
  `equipamiento` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `aula`
--

INSERT INTO `aula` (`id_aula`, `nombre`, `capacidad`, `estado`, `equipamiento`) VALUES
(1, 'Aula 101', 20, 'libre', NULL),
(2, 'Aula 102', 20, 'libre', NULL),
(3, 'Aula 103', 20, 'libre', NULL),
(4, 'Aula 201', 20, 'libre', NULL),
(5, 'Aula 202', 20, 'libre', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo`
--

CREATE TABLE `grupo` (
  `id_grupo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `capacidad_maxima` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL
) ;

--
-- Volcado de datos para la tabla `grupo`
--

INSERT INTO `grupo` (`id_grupo`, `nombre`, `capacidad_maxima`, `id_asignatura`) VALUES
(1, 'Grupo A - Matemáticas', 30, 1),
(2, 'Grupo B - Matemáticas', 25, 1),
(3, 'Grupo A - Física', 20, 2),
(4, 'Grupo A - Programación', 15, 3),
(5, 'Grupo A - Historia', 35, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `profesor`
--

CREATE TABLE `profesor` (
  `id_usuario` int(11) NOT NULL,
  `sueldo` varchar(50) DEFAULT NULL,
  `jornada` varchar(50) DEFAULT NULL,
  `fecha_inicio_contrato` date DEFAULT NULL,
  `fecha_fin_contrato` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `profesor`
--

INSERT INTO `profesor` (`id_usuario`, `sueldo`, `jornada`, `fecha_inicio_contrato`, `fecha_fin_contrato`) VALUES
(18, NULL, '', NULL, NULL),
(37, NULL, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reserva`
--

CREATE TABLE `reserva` (
  `id_reserva` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_aula` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL,
  `id_grupo` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('activa','pasada') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `reserva`
--

INSERT INTO `reserva` (`id_reserva`, `id_usuario`, `id_aula`, `id_asignatura`, `id_grupo`, `fecha`, `hora_inicio`, `hora_fin`, `estado`) VALUES
(6, 37, 2, 2, 3, '2025-05-06', '10:20:00', '11:19:00', 'activa'),
(7, 37, 1, 2, 5, '2025-05-06', '10:22:00', '11:25:00', 'activa'),
(8, 37, 1, 1, 1, '2025-05-06', '11:31:00', '12:32:00', 'activa'),
(9, 37, 3, 3, 4, '2025-05-06', '11:00:00', '13:48:00', 'activa'),
(10, 37, 1, 2, 3, '2025-05-06', '13:00:00', '14:37:00', 'activa'),
(11, 36, 1, 2, 3, '2025-05-09', '10:00:00', '11:00:00', 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `id_solicitud` int(11) NOT NULL,
  `id_anonimo` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado','rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_realizacion` date NOT NULL,
  `rol_propuesto` enum('profesor','alumno') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitud`
--

INSERT INTO `solicitud` (`id_solicitud`, `id_anonimo`, `estado`, `fecha_realizacion`, `rol_propuesto`) VALUES
(1, 1, 'aceptado', '2025-04-28', 'alumno'),
(2, 2, 'aceptado', '2025-04-29', 'alumno'),
(3, 3, 'aceptado', '2025-04-29', 'alumno'),
(4, 4, 'aceptado', '2025-05-05', 'profesor');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud_asignatura`
--

CREATE TABLE `solicitud_asignatura` (
  `id_solicitud` int(11) NOT NULL,
  `id_asignatura` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `solicitud_asignatura`
--

INSERT INTO `solicitud_asignatura` (`id_solicitud`, `id_asignatura`) VALUES
(4, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tutor_legal`
--

CREATE TABLE `tutor_legal` (
  `id_tutor` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `telefono` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tutor_legal`
--

INSERT INTO `tutor_legal` (`id_tutor`, `nombre`, `apellidos`, `telefono`) VALUES
(2, 'Joaquín', 'Alias Perez', '123123123123123'),
(3, 'María', 'Gómez Pérez', '123456789'),
(4, 'Juan', 'Rodríguez López', '987654321'),
(5, 'Ana', 'Martínez Sánchez', '555666777');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `DNI` varchar(20) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasenia` varchar(255) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellidos`, `DNI`, `telefono`, `correo`, `contrasenia`, `fecha_nacimiento`, `rol`) VALUES
(7, 'Carlos', 'López García', '11111111A', '111222333', 'carlos.lopez@example.com', 'contrasenia123', '2005-03-15', 'alumno'),
(8, 'Lucía', 'Fernández Díaz', '22222222B', '222333444', 'lucia.fernandez@example.com', 'contrasenia123', '2006-07-22', 'alumno'),
(9, 'Pedro', 'Sánchez Ruiz', '33333333C', '333444555', 'pedro.sanchez@example.com', 'contrasenia123', '2004-11-30', 'alumno'),
(10, 'Sofía', 'Martínez Gómez', '44444444D', '444555666', 'sofia.martinez@example.com', 'contrasenia123', '2005-09-10', 'alumno'),
(18, 'tung', 'sahur', '', '12312312312', 'tungsahur2121@studyflow.com', 'asdd32dqd', NULL, 'profesor'),
(33, 'tung', 'sahur', '12312312312T', '12312312312', 'tungsahur@studyflow.com', 'asdd32dqd', '2006-02-15', 'alumno'),
(34, 'quinito', 'alias', '', '21312312', 'kinito@gmail.com', 'asdasd34', NULL, 'alumno'),
(35, 'eevita', 'dinamita', '1312313t', '213123123', 'avita@gmail.com', 'asdasd3242', '2007-05-16', 'alumno'),
(36, 'admin', 'admin', '213132312T', '1312312312', 'admin@gmail.com', 'admin', '2005-01-14', 'administrador'),
(37, 'profe', 'profe', '', '1123123123', 'profesor@gmail.com', 'profe', NULL, 'alumno'),
(38, 'Elena', 'Gómez Torres', '55555555E', '555666777', 'elena.gomez@example.com', 'contrasenia123', '2006-01-20', 'alumno'),
(39, 'Miguel', 'Pérez Rodríguez', '66666666F', '666777888', 'miguel.perez@example.com', 'contrasenia123', '2005-12-10', 'alumno');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `alumno_grupo`
--
ALTER TABLE `alumno_grupo`
  ADD PRIMARY KEY (`id_usuario`,`id_grupo`),
  ADD KEY `fk_alumno_grupo_grupo` (`id_grupo`);

--
-- Indices de la tabla `alumno_tutor`
--
ALTER TABLE `alumno_tutor`
  ADD PRIMARY KEY (`id_usuario`,`id_tutor`),
  ADD KEY `id_tutor` (`id_tutor`);

--
-- Indices de la tabla `anonimo`
--
ALTER TABLE `anonimo`
  ADD PRIMARY KEY (`id_anonimo`),
  ADD KEY `idx_correo` (`correo`);

--
-- Indices de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD PRIMARY KEY (`id_asignatura`),
  ADD KEY `fk_asignatura_usuario` (`id_usuario`);

--
-- Indices de la tabla `aula`
--
ALTER TABLE `aula`
  ADD PRIMARY KEY (`id_aula`);

--
-- Indices de la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD PRIMARY KEY (`id_grupo`),
  ADD KEY `fk_grupo_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD PRIMARY KEY (`id_usuario`);

--
-- Indices de la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD PRIMARY KEY (`id_reserva`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_asignatura` (`id_asignatura`),
  ADD KEY `idx_reserva_aula_fecha` (`id_aula`,`fecha`,`estado`),
  ADD KEY `idx_reserva_grupo_asignatura` (`id_grupo`,`id_asignatura`,`estado`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `fk_solicitud_anonimo` (`id_anonimo`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_rol_propuesto` (`rol_propuesto`);

--
-- Indices de la tabla `solicitud_asignatura`
--
ALTER TABLE `solicitud_asignatura`
  ADD PRIMARY KEY (`id_solicitud`,`id_asignatura`),
  ADD KEY `id_asignatura` (`id_asignatura`);

--
-- Indices de la tabla `tutor_legal`
--
ALTER TABLE `tutor_legal`
  ADD PRIMARY KEY (`id_tutor`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anonimo`
--
ALTER TABLE `anonimo`
  MODIFY `id_anonimo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `asignatura`
--
ALTER TABLE `asignatura`
  MODIFY `id_asignatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `aula`
--
ALTER TABLE `aula`
  MODIFY `id_aula` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `grupo`
--
ALTER TABLE `grupo`
  MODIFY `id_grupo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reserva`
--
ALTER TABLE `reserva`
  MODIFY `id_reserva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id_solicitud` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tutor_legal`
--
ALTER TABLE `tutor_legal`
  MODIFY `id_tutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alumno`
--
ALTER TABLE `alumno`
  ADD CONSTRAINT `fk_alumno_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumno_grupo`
--
ALTER TABLE `alumno_grupo`
  ADD CONSTRAINT `fk_alumno_grupo_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_alumno_grupo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `alumno_tutor`
--
ALTER TABLE `alumno_tutor`
  ADD CONSTRAINT `alumno_tutor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE,
  ADD CONSTRAINT `alumno_tutor_ibfk_2` FOREIGN KEY (`id_tutor`) REFERENCES `tutor_legal` (`id_tutor`) ON DELETE CASCADE;

--
-- Filtros para la tabla `asignatura`
--
ALTER TABLE `asignatura`
  ADD CONSTRAINT `fk_asignatura_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `profesor` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `grupo`
--
ALTER TABLE `grupo`
  ADD CONSTRAINT `fk_grupo_asignatura` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `profesor`
--
ALTER TABLE `profesor`
  ADD CONSTRAINT `profesor_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reserva`
--
ALTER TABLE `reserva`
  ADD CONSTRAINT `reserva_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  ADD CONSTRAINT `reserva_ibfk_2` FOREIGN KEY (`id_aula`) REFERENCES `aula` (`id_aula`),
  ADD CONSTRAINT `reserva_ibfk_3` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`),
  ADD CONSTRAINT `reserva_ibfk_4` FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`);

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `fk_solicitud_anonimo` FOREIGN KEY (`id_anonimo`) REFERENCES `anonimo` (`id_anonimo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `solicitud_asignatura`
--
ALTER TABLE `solicitud_asignatura`
  ADD CONSTRAINT `solicitud_asignatura_ibfk_1` FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE,
  ADD CONSTRAINT `solicitud_asignatura_ibfk_2` FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
