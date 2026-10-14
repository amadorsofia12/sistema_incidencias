CREATE DATABASE IF NOT EXISTS db_incidencias;
USE db_incidencias;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

INSERT INTO usuarios (usuario, password) VALUES ('admin', '123');

CREATE TABLE IF NOT EXISTS incidencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reportado_por VARCHAR(100) NOT NULL,
    laboratorio VARCHAR(50) NOT NULL,
    equipo VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    prioridad ENUM('Baja', 'Media', 'Alta') NOT NULL,
    estado ENUM('Pendiente', 'En proceso', 'Resuelta') DEFAULT 'Pendiente',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);