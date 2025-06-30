<?php
header('Content-Type: application/json');
require_once 'conexion.php';

// Habilitar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Obtener todas las regiones
    $queryRegiones = "SELECT id, nombre FROM ajax.regiones ORDER BY nombre";
    $resultRegiones = pg_query($conn, $queryRegiones);
    if (!$resultRegiones) throw new Exception('Error al obtener regiones: ' . pg_last_error($conn));
    
    $regiones = [];
    while ($row = pg_fetch_assoc($resultRegiones)) {
        $regiones[] = $row;
    }

    // Obtener todas las comunas (luego se filtran por región en el frontend)
    $queryComunas = "SELECT id, nombre, region_id FROM ajax.comunas ORDER BY nombre";
    $resultComunas = pg_query($conn, $queryComunas);
    if (!$resultComunas) throw new Exception('Error al obtener comunas: ' . pg_last_error($conn));
    
    $comunas = [];
    while ($row = pg_fetch_assoc($resultComunas)) {
        $comunas[] = $row;
    }

    // Obtener todas las profesiones
    $queryProfesiones = "SELECT id, nombre FROM ajax.profesiones ORDER BY nombre";
    $resultProfesiones = pg_query($conn, $queryProfesiones);
    if (!$resultProfesiones) throw new Exception('Error al obtener profesiones: ' . pg_last_error($conn));
    
    $profesiones = [];
    while ($row = pg_fetch_assoc($resultProfesiones)) {
        $profesiones[] = $row;
    }

    // Obtener todas las personas con sus relaciones
    $queryPersonas = "SELECT p.id, p.nombre, p.apellidos, 
                             r.id as region_id, r.nombre as region_nombre,
                             c.id as comuna_id, c.nombre as comuna_nombre,
                             pr.id as profesion_id, pr.nombre as profesion_nombre
                      FROM ajax.personas p
                      JOIN ajax.regiones r ON p.region_id = r.id
                      JOIN ajax.comunas c ON p.comuna_id = c.id
                      JOIN ajax.profesiones pr ON p.profesion_id = pr.id
                      ORDER BY p.apellidos, p.nombre";
    $resultPersonas = pg_query($conn, $queryPersonas);
    if (!$resultPersonas) throw new Exception('Error al obtener personas: ' . pg_last_error($conn));
    
    $personas = [];
    while ($row = pg_fetch_assoc($resultPersonas)) {
        $personas[] = $row;
    }

    // Devolver todos los datos juntos
    echo json_encode([
        'success' => true,
        'regiones' => $regiones,
        'comunas' => $comunas,
        'profesiones' => $profesiones,
        'personas' => $personas
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
} finally {
    // Cerrar conexión
    if (isset($conn)) pg_close($conn);
}
?>