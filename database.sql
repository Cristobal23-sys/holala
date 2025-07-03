CREATE schema IF NOT EXISTS carreras;
CREATE TABLE carreras.atletas (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE carreras.carreras (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    tiempo INTEGER NOT NULL CHECK (tiempo > 0),
    atleta_id INTEGER NOT NULL REFERENCES carreras.atletas(id),
    avance INTEGER NOT NULL CHECK (avance >= 0 AND avance <= 100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(nombre, atleta_id)
);

-- Insertar atletas de ejemplo
INSERT INTO carreras.atletas(nombre) VALUES ('JUAN PEREZ'), ('ANA LOPEZ'), ('MARIO DIAZ');
