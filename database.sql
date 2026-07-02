CREATE DATABASE IF NOT EXISTS gestion_turnos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_turnos;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS turnos;
DROP TABLE IF EXISTS pacientes;
DROP TABLE IF EXISTS usuarios;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    email VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('admin', 'recepcionista', 'medico') NOT NULL,
    especialidad VARCHAR(100) NULL,
    matricula VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_email (email),
    KEY idx_usuarios_rol (rol),
    KEY idx_usuarios_medico (rol, especialidad, matricula)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE pacientes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    dni VARCHAR(20) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_pacientes_dni (dni),
    KEY idx_pacientes_nombre (apellido, nombre),
    KEY idx_pacientes_busqueda (dni, apellido, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE turnos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_medico INT UNSIGNED NOT NULL,
    id_paciente INT UNSIGNED NOT NULL,
    fecha_hora DATETIME NOT NULL,
    motivo VARCHAR(255) NULL,
    estado ENUM('pendiente', 'confirmado', 'cancelado', 'atendido') NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_turnos_medico_fecha (id_medico, fecha_hora),
    KEY idx_turnos_paciente (id_paciente),
    KEY idx_turnos_fecha (fecha_hora),
    KEY idx_turnos_estado (estado),
    CONSTRAINT fk_turnos_medico
        FOREIGN KEY (id_medico) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
    CONSTRAINT fk_turnos_paciente
        FOREIGN KEY (id_paciente) REFERENCES pacientes (id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO usuarios (id, nombre, email, password, rol, especialidad, matricula) VALUES
(1, 'Administrador', 'admin@gestionturnos.com', '$2y$10$UXMx6p4g5ROxIFRBRIsmD.axerupUK52N18Ql4NZAJXHIbMQ4AX1u', 'admin', NULL, NULL),
(2, 'Recepcion', 'recepcion@gestionturnos.com', '$2y$10$LAKJdPehqNOLFfx/DQb45.KCytvTVLynlyPrp0QWBmFpV7m8wduga', 'recepcionista', NULL, NULL),
(3, 'Dr. Juan Perez', 'jperez@gestionturnos.com', '$2y$10$b1mcYS3.1SJaEC97lR93S.IfTVteFS.8ApfUWlOYUGoEyfDHEdhi6', 'medico', 'Clinica medica', 'MN 12345'),
(4, 'Dr. Ana Gomez', 'agomez@gestionturnos.com', '$2y$10$b1mcYS3.1SJaEC97lR93S.IfTVteFS.8ApfUWlOYUGoEyfDHEdhi6', 'medico', 'Pediatria', 'MN 67890');

INSERT INTO pacientes (id, dni, nombre, apellido, telefono) VALUES
(1, '30111222', 'Maria', 'Lopez', '1122334455'),
(2, '28999888', 'Carlos', 'Fernandez', '1166778899'),
(3, '33777444', 'Lucia', 'Martinez', '1144556677'),
(4, '25666333', 'Roberto', 'Sanchez', NULL);

INSERT INTO turnos (id_medico, id_paciente, fecha_hora, motivo, estado) VALUES
(3, 1, TIMESTAMP(CURDATE(), '09:00:00'), 'Control general', 'pendiente'),
(3, 2, TIMESTAMP(CURDATE(), '10:30:00'), 'Dolor de garganta', 'confirmado'),
(4, 3, TIMESTAMP(CURDATE(), '11:00:00'), 'Consulta pediatrica', 'pendiente'),
(3, 4, DATE_ADD(TIMESTAMP(CURDATE(), '15:00:00'), INTERVAL 1 DAY), 'Chequeo anual', 'pendiente'),
(4, 1, DATE_SUB(TIMESTAMP(CURDATE(), '14:00:00'), INTERVAL 7 DAY), 'Consulta previa', 'atendido');
