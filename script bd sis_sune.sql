CREATE DATABASE sis_sune;

USE sis_sune;

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo VARCHAR(255) NOT NULL UNIQUE, -- Índice único
    contrasena VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('Agricultor', 'Agrónomo', 'Biólogo', 'Productor de semillas') NOT NULL
);

-- Tabla de solicitudes
CREATE TABLE solicitudes (
    id INT AUTO_INCREMENT PRIMARY KEY, -- ID global de la solicitud
    usuario_id INT, -- ID del usuario relacionado
    estado ENUM('Activa', 'Completada') NOT NULL DEFAULT 'Activa',
    solicitud_usuario_id INT NOT NULL DEFAULT 0, -- ID secuencial de solicitud por usuario
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Fecha de creación
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- Tabla de condiciones edáficas
CREATE TABLE condiciones_edaficas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NULL, -- ID global de la solicitud
    solicitud_usuario_id INT NULL, -- ID secuencial de solicitud por usuario
    caracterizacion_edafica BOOLEAN NOT NULL DEFAULT 0, -- 1 si solicita caracterización, 0 si no
    ph DECIMAL(5,2),
    contenido_agua DECIMAL(5,2),
    capa_organica DECIMAL(5,2),
    porosidad DECIMAL(5,2),
    textura VARCHAR(255),
    nivel_freatico VARCHAR(255),
    microbiota VARCHAR(255),
    fauna_edafica VARCHAR(255),
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id)
);

-- Tabla de condiciones topográficas
CREATE TABLE condiciones_topograficas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NULL, -- ID global de la solicitud
    solicitud_usuario_id INT NULL, -- ID secuencial de solicitud por usuario
    caracterizacion_topografica BOOLEAN NOT NULL DEFAULT 0, -- 1 si solicita caracterización, 0 si no
    direccion_terreno VARCHAR(255),
    ubicacion_terreno VARCHAR(255),
    latitud_terreno DECIMAL(10,8),
    hemisferio_latitud VARCHAR(10), -- N o S
    longitud_terreno DECIMAL(11,8),
    hemisferio_longitud VARCHAR(10), -- E o W
    altitud_terreno DECIMAL(10,2),
    pendiente_terreno DECIMAL(5,2),
    area_terreno DECIMAL(10,2),
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id)
);

-- Tabla de condiciones meteorológicas
CREATE TABLE condiciones_meteorologicas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NULL, -- ID global de la solicitud
    solicitud_usuario_id INT NULL, -- ID secuencial de solicitud por usuario
    caracterizacion_meteorologica BOOLEAN NOT NULL DEFAULT 0, -- 1 si solicita caracterización, 0 si no
    temperatura DECIMAL(5,2),
    humedad_relativa DECIMAL(5,2),
    presion_atmosferica DECIMAL(5,2),
    precipitacion DECIMAL(5,2),
    radiacion_solar DECIMAL(5,2),
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id)
);

-- Tabla de condiciones de cultivo
CREATE TABLE condiciones_cultivo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    solicitud_id INT NULL, -- ID global de la solicitud
    solicitud_usuario_id INT NULL, -- ID secuencial de solicitud por usuario
    preferencia_sis_sune BOOLEAN NOT NULL DEFAULT 0, -- 1 si abierto a preferencias SIS Sune
    tipo_cultivo VARCHAR(255),
    tiempo_cosecha INT, -- En días
    posibilidad_riego BOOLEAN,
    tipo_riego VARCHAR(255),
    posibilidad_manejo BOOLEAN,
    tipo_manejo VARCHAR(255),
    FOREIGN KEY (solicitud_id) REFERENCES solicitudes(id)
);

