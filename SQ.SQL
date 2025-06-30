-- Crear el esquema ajax si no existe
CREATE SCHEMA IF NOT EXISTS ajax;

-- Establecer el esquema por defecto
SET search_path TO ajax, public;

-- Crear tabla de regiones
CREATE TABLE IF NOT EXISTS ajax.regiones (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Crear tabla de comunas
CREATE TABLE IF NOT EXISTS ajax.comunas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    region_id INTEGER NOT NULL,
    FOREIGN KEY (region_id) REFERENCES ajax.regiones(id)
);

-- Crear tabla de profesiones
CREATE TABLE IF NOT EXISTS ajax.profesiones (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Crear tabla de personas
CREATE TABLE IF NOT EXISTS ajax.personas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    region_id INTEGER NOT NULL,
    comuna_id INTEGER NOT NULL,
    profesion_id INTEGER NOT NULL,
    fecha_creacion TIMESTAMP DEFAULT NOW(),
    fecha_actualizacion TIMESTAMP,
    FOREIGN KEY (region_id) REFERENCES ajax.regiones(id),
    FOREIGN KEY (comuna_id) REFERENCES ajax.comunas(id),
    FOREIGN KEY (profesion_id) REFERENCES ajax.profesiones(id),
    CONSTRAINT nombre_apellidos_unique UNIQUE (nombre, apellidos)
);

-- Crear función fn_persona_iu
CREATE OR REPLACE FUNCTION ajax.fn_persona_iu(
    p_idpersona INTEGER,
    p_nombre VARCHAR,
    p_apellidos VARCHAR,
    p_idprofesion INTEGER,
    p_idregion INTEGER,
    p_idcomuna INTEGER
) RETURNS TEXT AS $$
DECLARE
    v_count INTEGER;
    v_message TEXT;
BEGIN
    -- Verificar si la persona ya existe
    IF p_idpersona IS NOT NULL AND p_idpersona > 0 THEN
        -- Modo actualización
        -- Verificar que no exista otra persona con el mismo nombre y apellido
        SELECT COUNT(*) INTO v_count 
        FROM ajax.personas 
        WHERE nombre = p_nombre AND apellidos = p_apellidos AND id != p_idpersona;
        
        IF v_count > 0 THEN
            RETURN 'Error: Ya existe una persona con ese nombre y apellido.';
        END IF;
        
        -- Actualizar la persona
        UPDATE ajax.personas 
        SET nombre = p_nombre, 
            apellidos = p_apellidos, 
            profesion_id = p_idprofesion,
            region_id = p_idregion,
            comuna_id = p_idcomuna,
            fecha_actualizacion = NOW()
        WHERE id = p_idpersona;
        
        v_message := 'Persona actualizada correctamente.';
    ELSE
        -- Modo inserción
        -- Verificar que no exista una persona con el mismo nombre y apellido
        SELECT COUNT(*) INTO v_count 
        FROM ajax.personas 
        WHERE nombre = p_nombre AND apellidos = p_apellidos;
        
        IF v_count > 0 THEN
            RETURN 'Error: Ya existe una persona con ese nombre y apellido.';
        END IF;
        
        -- Insertar nueva persona
        INSERT INTO ajax.personas (nombre, apellidos, profesion_id, region_id, comuna_id)
        VALUES (p_nombre, p_apellidos, p_idprofesion, p_idregion, p_idcomuna);
        
        v_message := 'Persona creada correctamente.';
    END IF;
    
    RETURN v_message;
EXCEPTION
    WHEN OTHERS THEN
        RETURN 'Error: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;

-- Insertar datos iniciales
BEGIN;

-- Insertar regiones
INSERT INTO ajax.regiones (id, nombre) VALUES 
(1, 'Arica y Parinacota'),
(2, 'Tarapacá'),
(3, 'Antofagasta'),
(4, 'Atacama')
ON CONFLICT (id) DO NOTHING;

-- Insertar comunas para Arica y Parinacota
INSERT INTO ajax.comunas (id, nombre, region_id) VALUES 
(1, 'Arica', 1),
(2, 'Camarones', 1),
(3, 'Putre', 1),
(4, 'General Lagos', 1)
ON CONFLICT (id) DO NOTHING;

-- Insertar comunas para Tarapacá
INSERT INTO ajax.comunas (id, nombre, region_id) VALUES 
(5, 'Iquique', 2),
(6, 'Alto Hospicio', 2),
(7, 'Pozo Almonte', 2),
(8, 'Camiña', 2)
ON CONFLICT (id) DO NOTHING;

-- Insertar comunas para Antofagasta
INSERT INTO ajax.comunas (id, nombre, region_id) VALUES 
(9, 'Antofagasta', 3),
(10, 'Mejillones', 3),
(11, 'Sierra Gorda', 3),
(12, 'Taltal', 3)
ON CONFLICT (id) DO NOTHING;

-- Insertar comunas para Atacama
INSERT INTO ajax.comunas (id, nombre, region_id) VALUES 
(13, 'Copiapó', 4),
(14, 'Caldera', 4),
(15, 'Tierra Amarilla', 4),
(16, 'Chañaral', 4)
ON CONFLICT (id) DO NOTHING;

-- Insertar profesiones
INSERT INTO ajax.profesiones (id, nombre) VALUES 
(1, 'Ingeniero'),
(2, 'Médico'),
(3, 'Profesor'),
(4, 'Arquitecto'),
(5, 'Programador')
ON CONFLICT (id) DO NOTHING;

COMMIT;

-- Crear índices para mejorar el rendimiento
CREATE INDEX IF NOT EXISTS idx_personas_nombre_apellidos ON ajax.personas (nombre, apellidos);
CREATE INDEX IF NOT EXISTS idx_comunas_region_id ON ajax.comunas (region_id);