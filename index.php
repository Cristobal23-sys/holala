<?php
require_once 'conexion.php';

// Obtener datos desde PostgreSQL
$queryRegiones = "SELECT id, nombre FROM ajax.regiones ORDER BY nombre";
$regiones = pg_fetch_all(pg_query($conn, $queryRegiones)) ?: [];

$queryComunas = "SELECT id, nombre, region_id FROM ajax.comunas ORDER BY nombre";
$comunas = pg_fetch_all(pg_query($conn, $queryComunas)) ?: [];

$queryProfesiones = "SELECT id, nombre FROM ajax.profesiones ORDER BY nombre";
$profesiones = pg_fetch_all(pg_query($conn, $queryProfesiones)) ?: [];

$queryPersonas = "SELECT p.id, p.nombre, p.apellidos, 
                         r.nombre as region, c.nombre as comuna, pr.nombre as profesion
                  FROM ajax.personas p
                  JOIN ajax.regiones r ON p.region_id = r.id
                  JOIN ajax.comunas c ON p.comuna_id = c.id
                  JOIN ajax.profesiones pr ON p.profesion_id = pr.id
                  ORDER BY p.apellidos, p.nombre";
$personas = pg_fetch_all(pg_query($conn, $queryPersonas)) ?: [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Mi Formulario</title>
    <script>
        // Pasar datos PHP a JavaScript
        const appData = {
            regiones: <?= json_encode($regiones) ?>,
            comunas: <?= json_encode($comunas) ?>,
            profesiones: <?= json_encode($profesiones) ?>,
            personas: <?= json_encode($personas) ?>
        };
    </script>
    <script src="script.js" defer></script>
</head>
<body>
    <h1>Mi Formulario</h1>
    
    <form id="personaForm">
        <div>
            <label>Nombre</label>
            <input type="text" name="nombre" required>
        </div>
        
        <div>
            <label>Apellidos</label>
            <input type="text" name="apellidos" required>
        </div>
        
        <div>
            <label>Región</label>
            <select name="region" required>
                <option value="">Seleccione región</option>
                <!-- Las opciones se llenarán con JavaScript -->
            </select>
        </div>
        
        <div>
            <label>Comuna</label>
            <select name="comuna" required disabled>
                <option value="">Seleccione comuna</option>
            </select>
        </div>
        
        <div>
            <label>Profesión</label>
            <select name="profesion" required>
                <option value="">Seleccione profesión</option>
                <!-- Las opciones se llenarán con JavaScript -->
            </select>
        </div>
        
        <button type="submit">Enviar</button>
        <input type="hidden" name="id" value="">
    </form>
    
    <div>
        <h2>Datos Registrados</h2>
        <table id="personasTable">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Apellidos</th>
                    <th>Región</th>
                    <th>Comuna</th>
                    <th>Profesión</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Los datos se llenarán con JavaScript -->
            </tbody>
        </table>
    </div>
</body>
</html>