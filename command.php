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
            
            // Verificar que la comuna pertenezca a la región
            $sqlCheck = "SELECT 1 FROM ajax.comunas WHERE id = $1 AND region_id = $2";
            $resultCheck = pg_query_params($conn, $sqlCheck, array($comunaId, $regionId));
            if (pg_num_rows($resultCheck) == 0) {
                throw new Exception("La comuna no pertenece a la región seleccionada");
            }
            
            if ($id) {
                // Actualizar
                $sql = "UPDATE ajax.personas SET 
                        nombre = $1, 
                        apellidos = $2, 
                        region_id = $3, 
                        comuna_id = $4, 
                        profesion_id = $5,
                        fecha_actualizacion = NOW()
                        WHERE id = $6
                        RETURNING id";
                $params = array($nombre, $apellidos, $regionId, $comunaId, $profesionId, $id);
            } else {
                // Insertar
                $sql = "INSERT INTO ajax.personas 
                        (nombre, apellidos, region_id, comuna_id, profesion_id)
                        VALUES ($1, $2, $3, $4, $5)
                        RETURNING id";
                $params = array($nombre, $apellidos, $regionId, $comunaId, $profesionId);
            }
            
            $result = pg_query_params($conn, $sql, $params);
            
            if (!$result) {
                throw new Exception(pg_last_error($conn));
            }
            
            $row = pg_fetch_assoc($result);
            echo json_encode(['success' => true, 'id' => $row['id']]);
            break;
            
        case 'fn_persona_iu':
        // Implementación similar al ejemplo proporcionado
        if (!isset($_REQUEST["idpersona"]) || !isset($_REQUEST["nombre"]) || empty($_REQUEST["nombre"]) || 
            !isset($_REQUEST["apellidos"]) || empty($_REQUEST["apellidos"]) || 
            !isset($_REQUEST["idprofesion"]) || empty($_REQUEST["idprofesion"]) ||
            !isset($_REQUEST["idregion"]) || empty($_REQUEST["idregion"]) ||
            !isset($_REQUEST["idcomuna"]) || empty($_REQUEST["idcomuna"])) {
            echo json_encode([["error" => "Todos los parámetros (idpersona, nombre, apellidos, idprofesion, idregion, idcomuna) son requeridos."]]);
            break;
        }

        $idpersona = $_REQUEST["idpersona"];
        $nombre = $_REQUEST["nombre"];
        $apellidos = $_REQUEST["apellidos"];
        $idprofesion = $_REQUEST["idprofesion"];
        $idregion = $_REQUEST["idregion"];
        $idcomuna = $_REQUEST["idcomuna"];

        $sql = "SELECT fn_persona_iu($1, $2, $3, $4, $5, $6) as message";
        $params = array($idpersona, $nombre, $apellidos, $idprofesion, $idregion, $idcomuna);
        $result = pg_query_params($conn, $sql, $params);

        $obj = array();
        $lastError = pg_last_error($conn);

        if ($lastError) {
            array_push($obj, array("error" => "Error al ejecutar la función fn_persona_iu: " . $lastError));
        } else {
            $row = pg_fetch_assoc($result);
            if ($row) {
                array_push($obj, array("message" => $row['message']));
            } else {
                array_push($obj, array("error" => "La función fn_persona_iu no devolvió un resultado esperado."));
            }
        }

        echo json_encode($obj);
        break;
            
        default:
            throw new Exception("Comando no reconocido");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>