<?php
header('Content-Type: application/json');
include 'conexion.php';

$tabla = $_GET['tabla'] ?? '';
$id = $_GET['id'] ?? null;

try {
    switch ($tabla) {
        case 'regiones':
            $sql = "SELECT id, nombre FROM ajax.regiones ORDER BY nombre";
            $result = pg_query($conn, $sql);
            break;
            
        case 'comunas':
            if (!isset($_GET['region_id'])) {
                throw new Exception("Parámetro region_id es requerido");
            }
            $regionId = $_GET['region_id'];
            $sql = "SELECT id, nombre FROM ajax.comunas WHERE region_id = $1 ORDER BY nombre";
            $result = pg_query_params($conn, $sql, array($regionId));
            break;
            
        case 'profesiones':
            $sql = "SELECT id, nombre FROM ajax.profesiones ORDER BY nombre";
            $result = pg_query($conn, $sql);
            break;
            
        case 'personas':
            if ($id) {
                $sql = "SELECT p.*, r.nombre as region_nombre, c.nombre as comuna_nombre, pr.nombre as profesion_nombre 
                        FROM ajax.personas p
                        JOIN ajax.regiones r ON p.region_id = r.id
                        JOIN ajax.comunas c ON p.comuna_id = c.id
                        JOIN ajax.profesiones pr ON p.profesion_id = pr.id
                        WHERE p.id = $1";
                $result = pg_query_params($conn, $sql, array($id));
            } else {
                $sql = "SELECT p.id, p.nombre, p.apellidos, r.nombre as region, c.nombre as comuna, pr.nombre as profesion 
                        FROM ajax.personas p
                        JOIN ajax.regiones r ON p.region_id = r.id
                        JOIN ajax.comunas c ON p.comuna_id = c.id
                        JOIN ajax.profesiones pr ON p.profesion_id = pr.id
                        ORDER BY p.apellidos, p.nombre";
                $result = pg_query($conn, $sql);
            }
            break;
            
        default:
            throw new Exception("Tabla no válida");
    }
    
    if (!$result) {
        throw new Exception(pg_last_error($conn));
    }
    
    $data = [];
    while ($row = pg_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    echo json_encode(['success' => true, 'data' => $data]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>