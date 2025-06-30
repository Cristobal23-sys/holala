<?php
header('Content-Type: application/json');
include 'conexion.php';

$cmd = $_REQUEST['cmd'] ?? '';

try {
    switch ($cmd) {
        case 'guardar_persona':
            $required = ['nombre', 'apellidos', 'region_id', 'comuna_id', 'profesion_id'];
            foreach ($required as $field) {
                if (empty($_REQUEST[$field])) {
                    throw new Exception("El campo $field es requerido");
                }
            }

            $id = $_REQUEST['id'] ?? null;
            $nombre = $_REQUEST['nombre'];
            $apellidos = $_REQUEST['apellidos'];
            $regionId = $_REQUEST['region_id'];
            $comunaId = $_REQUEST['comuna_id'];
            $profesionId = $_REQUEST['profesion_id'];

            // Verificar comuna pertenece a región
            $sqlCheck = "SELECT 1 FROM ajax.comunas WHERE id = $1 AND region_id = $2";
            $resultCheck = pg_query_params($conn, $sqlCheck, [$comunaId, $regionId]);
            if (pg_num_rows($resultCheck) == 0) {
                throw new Exception("La comuna no pertenece a la región seleccionada");
            }

            // Validar duplicados
            $sqlDup = "SELECT id FROM ajax.personas WHERE LOWER(nombre) = LOWER($1) AND LOWER(apellidos) = LOWER($2)";
            $paramsDup = [$nombre, $apellidos];
            if ($id) {
                $sqlDup .= " AND id != $3";
                $paramsDup[] = $id;
            }
            $dupResult = pg_query_params($conn, $sqlDup, $paramsDup);
            if (pg_num_rows($dupResult) > 0) {
                throw new Exception("Ya existe una persona con ese nombre y apellidos.");
            }

            if ($id) {
                $sql = "UPDATE ajax.personas SET nombre=$1, apellidos=$2, region_id=$3, comuna_id=$4, profesion_id=$5, fecha_actualizacion=NOW()
                        WHERE id=$6 RETURNING id";
                $params = [$nombre, $apellidos, $regionId, $comunaId, $profesionId, $id];
            } else {
                $sql = "INSERT INTO ajax.personas (nombre, apellidos, region_id, comuna_id, profesion_id)
                        VALUES ($1, $2, $3, $4, $5) RETURNING id";
                $params = [$nombre, $apellidos, $regionId, $comunaId, $profesionId];
            }

            $result = pg_query_params($conn, $sql, $params);
            if (!$result) throw new Exception(pg_last_error($conn));
            $row = pg_fetch_assoc($result);

            echo json_encode(['success' => true, 'id' => $row['id']]);
            break;
            // obtener regiones
case 'get_regiones':
    $res = pg_query($conn, "SELECT id, nombre FROM ajax.regiones ORDER BY nombre");
    echo json_encode(pg_fetch_all($res));
    break;

// obtener comunas por region
case 'get_comunas':
    $regionId = $_GET['region_id'] ?? 0;
    $res = pg_query_params($conn, "SELECT id, nombre FROM ajax.comunas WHERE region_id = $1 ORDER BY nombre", [$regionId]);
    echo json_encode(pg_fetch_all($res));
    break;

// obtener profesiones
case 'get_profesiones':
    $res = pg_query($conn, "SELECT id, nombre FROM ajax.profesiones ORDER BY nombre");
    echo json_encode(pg_fetch_all($res));
    break;

// obtener personas
case 'get_personas':
    $sql = "SELECT p.id, p.nombre, p.apellidos, r.nombre as region, c.nombre as comuna, pr.nombre as profesion 
            FROM ajax.personas p
            JOIN ajax.regiones r ON r.id = p.region_id
            JOIN ajax.comunas c ON c.id = p.comuna_id
            JOIN ajax.profesiones pr ON pr.id = p.profesion_id
            ORDER BY p.apellidos, p.nombre";
    $res = pg_query($conn, $sql);
    echo json_encode(pg_fetch_all($res));
    break;

// obtener persona por ID
case 'get_persona':
    $id = $_GET['id'] ?? 0;
    $res = pg_query_params($conn, "SELECT * FROM ajax.personas WHERE id = $1", [$id]);
    echo json_encode(pg_fetch_assoc($res));
    break;

// verificar duplicado
case 'verificar_duplicado':
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $id = $_POST['id'] ?? null;

    $sql = "SELECT 1 FROM ajax.personas WHERE LOWER(nombre) = LOWER($1) AND LOWER(apellidos) = LOWER($2)";
    $params = [$nombre, $apellidos];

    if ($id) {
        $sql .= " AND id != $3";
        $params[] = $id;
    }

    $res = pg_query_params($conn, $sql, $params);
    echo json_encode(['duplicado' => pg_num_rows($res) > 0]);
    break;


        default:
            throw new Exception("Comando no reconocido");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
