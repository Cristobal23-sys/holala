
CREATE TABLE regiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE comunas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regiones(id)
);

CREATE TABLE profesiones (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE personas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    region_id INT NOT NULL,
    comuna_id INT NOT NULL,
    profesion_id INT NOT NULL,
    FOREIGN KEY (region_id) REFERENCES regiones(id),
    FOREIGN KEY (comuna_id) REFERENCES comunas(id),
    FOREIGN KEY (profesion_id) REFERENCES profesiones(id),
    UNIQUE KEY (nombre, apellidos)
);-- Crear tablas si no existen


-- Región de Arica y Parinacota
INSERT INTO regiones (id, nombre) VALUES (1, 'Arica y Parinacota') ON CONFLICT (id) DO NOTHING;
INSERT INTO comunas (id, nombre, region_id) VALUES 
(1, 'Arica', 1),
(2, 'Camarones', 1),
(3, 'Putre', 1),
(4, 'General Lagos', 1) ON CONFLICT (id) DO NOTHING;

-- Región de Tarapacá
INSERT INTO regiones (id, nombre) VALUES (2, 'Tarapacá') ON CONFLICT (id) DO NOTHING;
INSERT INTO comunas (id, nombre, region_id) VALUES 
(5, 'Iquique', 2),
(6, 'Alto Hospicio', 2),
(7, 'Pozo Almonte', 2),
(8, 'Camiña', 2) ON CONFLICT (id) DO NOTHING;

-- Región de Antofagasta
INSERT INTO regiones (id, nombre) VALUES (3, 'Antofagasta') ON CONFLICT (id) DO NOTHING;
INSERT INTO comunas (id, nombre, region_id) VALUES 
(9, 'Antofagasta', 3),
(10, 'Mejillones', 3),
(11, 'Sierra Gorda', 3),
(12, 'Taltal', 3) ON CONFLICT (id) DO NOTHING;

-- Región de Atacama
INSERT INTO regiones (id, nombre) VALUES (4, 'Atacama') ON CONFLICT (id) DO NOTHING;
INSERT INTO comunas (id, nombre, region_id) VALUES 
(13, 'Copiapó', 4),
(14, 'Caldera', 4),
(15, 'Tierra Amarilla', 4),
(16, 'Chañaral', 4) ON CONFLICT (id) DO NOTHING;

CREATE OR REPLACE FUNCTION fn_persona_iu(
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
        FROM personas 
        WHERE nombre = p_nombre AND apellidos = p_apellidos AND id != p_idpersona;
        
        IF v_count > 0 THEN
            RETURN 'Error: Ya existe una persona con ese nombre y apellido.';
        END IF;
        
        -- Actualizar la persona
        UPDATE personas 
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
        FROM personas 
        WHERE nombre = p_nombre AND apellidos = p_apellidos;
        
        IF v_count > 0 THEN
            RETURN 'Error: Ya existe una persona con ese nombre y apellido.';
        END IF;
        
        -- Insertar nueva persona
        INSERT INTO personas (nombre, apellidos, profesion_id, region_id, comuna_id, fecha_creacion)
        VALUES (p_nombre, p_apellidos, p_idprofesion, p_idregion, p_idcomuna, NOW());
        
        v_message := 'Persona creada correctamente.';
    END IF;
    
    RETURN v_message;
EXCEPTION
    WHEN OTHERS THEN
        RETURN 'Error: ' || SQLERRM;
END;
$$ LANGUAGE plpgsql;