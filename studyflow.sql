-- Crear base de datos
CREATE DATABASE IF NOT EXISTS studyflow3;
USE studyflow;

-- Configuración inicial
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Tabla usuario
CREATE TABLE `usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `DNI` VARCHAR(20) DEFAULT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `correo` VARCHAR(100) NOT NULL,
  `contrasenia` VARCHAR(255) DEFAULT NULL,
  `fecha_nacimiento` DATE DEFAULT NULL,
  `rol` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `DNI` (`DNI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla anonimo
CREATE TABLE `anonimo` (
  `id_anonimo` INT(11) NOT NULL AUTO_INCREMENT,
  `correo` VARCHAR(255) NOT NULL,
  `contrasenia` VARCHAR(255) NOT NULL,
  `nombre` VARCHAR(100) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(20) DEFAULT NULL,
  `DNI` VARCHAR(20) DEFAULT NULL,
  `fecha_nacimiento` DATE DEFAULT NULL,
  PRIMARY KEY (`id_anonimo`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `DNI` (`DNI`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla tutor_legal
CREATE TABLE `tutor_legal` (
  `id_tutor` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellidos` VARCHAR(100) NOT NULL,
  `telefono` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`id_tutor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla profesor
CREATE TABLE `profesor` (
  `id_usuario` INT(11) NOT NULL,
  `sueldo` VARCHAR(50) DEFAULT NULL,
  `jornada` VARCHAR(50) DEFAULT NULL,
  `fecha_inicio_contrato` DATE DEFAULT NULL,
  `fecha_fin_contrato` DATE DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla asignatura
CREATE TABLE `asignatura` (
  `id_asignatura` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `nivel` VARCHAR(100) NOT NULL,
  `descripcion` TEXT DEFAULT NULL,
  `id_usuario` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id_asignatura`),
  FOREIGN KEY (`id_usuario`) REFERENCES `profesor` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla grupo
CREATE TABLE `grupo` (
  `id_grupo` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `capacidad_maxima` INT(11) NOT NULL,
  `id_asignatura` INT(11) NOT NULL,
  PRIMARY KEY (`id_grupo`),
  FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla aula
CREATE TABLE `aula` (
  `id_aula` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `capacidad` INT(11) NOT NULL,
  `estado` ENUM('libre', 'reservada') DEFAULT 'libre',
  `equipamiento` TEXT DEFAULT NULL,
  PRIMARY KEY (`id_aula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla alumno
CREATE TABLE `alumno` (
  `id_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla alumno_grupo
CREATE TABLE `alumno_grupo` (
  `id_usuario` INT(11) NOT NULL,
  `id_grupo` INT(11) NOT NULL,
  PRIMARY KEY (`id_usuario`, `id_grupo`),
  FOREIGN KEY (`id_usuario`) REFERENCES `alumno` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla alumno_tutor
CREATE TABLE `alumno_tutor` (
  `id_usuario` INT(11) NOT NULL,
  `id_tutor` INT(11) NOT NULL,
  PRIMARY KEY (`id_usuario`, `id_tutor`),
  FOREIGN KEY (`id_usuario`) REFERENCES `alumno` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_tutor`) REFERENCES `tutor_legal` (`id_tutor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla solicitud
CREATE TABLE `solicitud` (
  `id_solicitud` INT(11) NOT NULL AUTO_INCREMENT,
  `id_anonimo` INT(11) NOT NULL,
  `estado` ENUM('pendiente', 'aceptado', 'rechazado') NOT NULL DEFAULT 'pendiente',
  `fecha_realizacion` DATE NOT NULL,
  `rol_propuesto` ENUM('profesor', 'alumno') NOT NULL,
  PRIMARY KEY (`id_solicitud`),
  FOREIGN KEY (`id_anonimo`) REFERENCES `anonimo` (`id_anonimo`) ON DELETE CASCADE ON UPDATE CASCADE,
  INDEX `idx_estado` (`estado`),
  INDEX `idx_rol_propuesto` (`rol_propuesto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla solicitud_asignatura
CREATE TABLE `solicitud_asignatura` (
  `id_solicitud` INT(11) NOT NULL,
  `id_asignatura` INT(11) NOT NULL,
  PRIMARY KEY (`id_solicitud`, `id_asignatura`),
  FOREIGN KEY (`id_solicitud`) REFERENCES `solicitud` (`id_solicitud`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla tarea
CREATE TABLE `tarea` (
  `id_tarea` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `fecha_creacion` DATETIME NOT NULL,
  `fecha_entrega` DATETIME NOT NULL,
  PRIMARY KEY (`id_tarea`),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla tarea_asignatura
CREATE TABLE `tarea_asignatura` (
  `id_tarea` INT(11) NOT NULL,
  `id_asignatura` INT(11) NOT NULL,
  PRIMARY KEY (`id_tarea`, `id_asignatura`),
  FOREIGN KEY (`id_tarea`) REFERENCES `tarea` (`id_tarea`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla tarea_grupo
CREATE TABLE `tarea_grupo` (
  `id_tarea` INT(11) NOT NULL,
  `id_grupo` INT(11) NOT NULL,
  PRIMARY KEY (`id_tarea`, `id_grupo`),
  FOREIGN KEY (`id_tarea`) REFERENCES `tarea` (`id_tarea`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla reserva
CREATE TABLE `reserva` (
  `id_reserva` INT(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` INT(11) NOT NULL,
  `id_aula` INT(11) NOT NULL,
  `id_asignatura` INT(11) NOT NULL,
  `id_grupo` INT(11) NOT NULL,
  `fecha` DATE NOT NULL,
  `hora_inicio` TIME NOT NULL,
  `hora_fin` TIME NOT NULL,
  `estado` ENUM('activa', 'pasada') NOT NULL,
  PRIMARY KEY (`id_reserva`),
  FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_aula`) REFERENCES `aula` (`id_aula`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_asignatura`) REFERENCES `asignatura` (`id_asignatura`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`id_grupo`) REFERENCES `grupo` (`id_grupo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos iniciales (basados en studyflow)
-- Usuarios
INSERT INTO `usuario` (`id_usuario`, `nombre`, `apellidos`, `DNI`, `telefono`, `correo`, `contrasenia`, `fecha_nacimiento`, `rol`) VALUES
(1, 'admin', 'admin', '213132312T', '1312312312', 'admin@gmail.com', 'admin', '2005-01-14', 'administrador'),
(7, 'Carlos', 'López García', '11111111A', '111222333', 'carlos.lopez@example.com', 'contrasenia123', '2005-03-15', 'alumno'),
(8, 'Lucía', 'Fernández Díaz', '22222222B', '222333444', 'lucia.fernandez@example.com', 'contrasenia123', '2006-07-22', 'alumno'),
(9, 'Pedro', 'Sánchez Ruiz', '33333333C', '333444555', 'pedro.sanchez@example.com', 'contrasenia123', '2004-11-30', 'alumno'),
(10, 'Sofía', 'Martínez Gómez', '44444444D', '444555666', 'sofia.martinez@example.com', 'contrasenia123', '2005-09-10', 'alumno'),
(36, 'admin', 'admin', '213132313T', '1312312313', 'admin2@gmail.com', 'admin', '2005-01-14', 'administrador'),
(37, 'profe', 'profe', NULL, '1123123123', 'profesor@gmail.com', 'profe', NULL, 'profesor'),
(38, 'Elena', 'Gómez Torres', '55555555E', '555666777', 'elena.gomez@example.com', 'contrasenia123', '2006-01-20', 'alumno'),
(39, 'Miguel', 'Pérez Rodríguez', '66666666F', '666777888', 'miguel.perez@example.com', 'contrasenia123', '2005-12-10', 'alumno'),
(44, 'joaquin', 'alias perez', '81181314P', '+34 607579544', 'joaquin@gmail.com', 'Melilla1223423', '2001-02-13', 'alumno');

-- Profesores
INSERT INTO `profesor` (`id_usuario`, `sueldo`, `jornada`, `fecha_inicio_contrato`, `fecha_fin_contrato`) VALUES
(37, NULL, NULL, NULL, NULL);

-- Alumnos
INSERT INTO `alumno` (`id_usuario`) VALUES
(7), (8), (9), (10), (38), (39), (44);

-- Asignaturas
INSERT INTO `asignatura` (`id_asignatura`, `nombre`, `nivel`, `descripcion`, `id_usuario`) VALUES
(1, 'Matemáticas', 'Básico', 'Curso de matemáticas básicas', NULL),
(2, 'Física', 'Intermedio', 'Introducción a la física clásica', 37),
(3, 'Programación', 'Avanzado', 'Fundamentos de programación en Python', NULL),
(4, 'Historia', 'Básico', 'Historia mundial del siglo XX', NULL),
(5, 'Inglés', 'Básico', 'Inglés básico', NULL),
(6, 'Economía', 'Básica', 'Economía', NULL);

-- Grupos
INSERT INTO `grupo` (`id_grupo`, `nombre`, `capacidad_maxima`, `id_asignatura`) VALUES
(1, 'Grupo A - Matemáticas', 30, 1),
(2, 'Grupo B - Matemáticas', 25, 1),
(3, 'Grupo A - Física', 20, 2),
(4, 'Grupo A - Programación', 15, 3),
(5, 'Grupo A - Historia', 35, 4);

-- Alumno_grupo
INSERT INTO `alumno_grupo` (`id_usuario`, `id_grupo`) VALUES
(38, 1), (38, 2), (38, 3), (38, 4), (39, 1), (44, 3);

-- Anonimo
INSERT INTO `anonimo` (`id_anonimo`, `correo`, `contrasenia`, `nombre`, `apellidos`, `telefono`, `DNI`, `fecha_nacimiento`) VALUES
(4, 'profesor2@gmail.com', 'profe', 'profe', 'profe', '1123123123', NULL, NULL),
(7, 'joaquin2@gmail.com', 'Melilla1223423', 'joaquin', 'alias perez', '+34 607579544', '81181315P', '2001-02-13');

-- Solicitud
INSERT INTO `solicitud` (`id_solicitud`, `id_anonimo`, `estado`, `fecha_realizacion`, `rol_propuesto`) VALUES
(4, 4, 'pendiente', '2025-05-05', 'profesor'),
(7, 7, 'pendiente', '2025-05-13', 'alumno');

-- Solicitud_asignatura
INSERT INTO `solicitud_asignatura` (`id_solicitud`, `id_asignatura`) VALUES
(4, 2), (7, 6);

-- Aulas
INSERT INTO `aula` (`id_aula`, `nombre`, `capacidad`, `estado`, `equipamiento`) VALUES
(1, 'Aula 101', 20, 'libre', NULL),
(2, 'Aula 102', 20, 'libre', NULL),
(3, 'Aula 103', 20, 'libre', NULL),
(4, 'Aula 201', 20, 'libre', NULL),
(5, 'Aula 202', 20, 'libre', NULL);

-- Tutores legales
INSERT INTO `tutor_legal` (`id_tutor`, `nombre`, `apellidos`, `telefono`) VALUES
(2, 'Joaquín', 'Alias Perez', '123123123123123'),
(3, 'María', 'Gómez Pérez', '123456789'),
(4, 'Juan', 'Rodríguez López', '987654321'),
(5, 'Ana', 'Martínez Sánchez', '555666777');

-- Alumno_tutor
INSERT INTO `alumno_tutor` (`id_usuario`, `id_tutor`) VALUES
(8, 3), (10, 5);

COMMIT;
