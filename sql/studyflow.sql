-- Crear base de datos
--CREATE DATABASE IF NOT EXISTS studyflow;
--USE studyflow;

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
  `id_usuario` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id_anonimo`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `DNI` (`DNI`),
  CONSTRAINT `fk_anonimo_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL ON UPDATE CASCADE
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
(1, 'admin', 'admin', '213132312T', '1312312312', 'admin@gmail.com', 'admin', '2005-01-14', 'administrador');



COMMIT;
