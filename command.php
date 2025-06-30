<?php
include 'conexion.php';

$cmd = $_REQUEST["cmd"]; //$_POST O $_GET
switch ($cmd) {
    case 'listarRegiones':
        $sql = "SELECT id, nombre FROM regiones ORDER BY nombre";
        $result = pg_query($conn, $sql);
        
        $obj = array();
        $lastError = pg_last_error($conn);
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            while ($row = pg_fetch_assoc($result)) {
                array_push($obj, $row);
            }
        }
        
        echo json_encode($obj);
        break;
        
    case 'listarComunas':
        if (!isset($_REQUEST["region_id"]) || empty($_REQUEST["region_id"])) {
            echo json_encode([["error" => "El parámetro region_id es requerido."]]);
            break;
        }
        
        $regionId = $_REQUEST["region_id"];
        $sql = "SELECT id, nombre FROM comunas WHERE region_id = $1 ORDER BY nombre";
        $result = pg_query_params($conn, $sql, array($regionId));
        
        $obj = array();
        $lastError = pg_last_error($conn);
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            while ($row = pg_fetch_assoc($result)) {
                array_push($obj, $row);
            }
        }
        
        echo json_encode($obj);
        break;
        
    case 'listarProfesiones':
        $sql = "SELECT id, nombre FROM profesiones ORDER BY nombre";
        $result = pg_query($conn, $sql);
        
        $obj = array();
        $lastError = pg_last_error($conn);
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            while ($row = pg_fetch_assoc($result)) {
                array_push($obj, $row);
            }
        }
        
        echo json_encode($obj);
        break;
        
    case 'listarPersonas':
        $sql = "SELECT p.id, p.nombre, p.apellidos, r.nombre as region, c.nombre as comuna, pr.nombre as profesion 
                FROM personas p
                JOIN regiones r ON p.region_id = r.id
                JOIN comunas c ON p.comuna_id = c.id
                JOIN profesiones pr ON p.profesion_id = pr.id
                ORDER BY p.apellidos, p.nombre";
        $result = pg_query($conn, $sql);
        
        $obj = array();
        $lastError = pg_last_error($conn);
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            while ($row = pg_fetch_assoc($result)) {
                array_push($obj, $row);
            }
        }
        
        echo json_encode($obj);
        break;
        
    case 'obtenerPersona':
        if (!isset($_REQUEST["id"]) || empty($_REQUEST["id"])) {
            echo json_encode([["error" => "El parámetro id es requerido."]]);
            break;
        }
        
        $id = $_REQUEST["id"];
        $sql = "SELECT p.id, p.nombre, p.apellidos, p.region_id, p.comuna_id, p.profesion_id 
                FROM personas p
                WHERE p.id = $1";
        $result = pg_query_params($conn, $sql, array($id));
        
        $obj = array();
        $lastError = pg_last_error($conn);
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            if (pg_num_rows($result) > 0) {
                $row = pg_fetch_assoc($result);
                array_push($obj, $row);
            } else {
                array_push($obj, array("error" => "Persona no encontrada."));
            }
        }
        
        echo json_encode($obj);
        break;
        
    case 'guardarPersona':
        $requiredParams = ['nombre', 'apellidos', 'region_id', 'comuna_id', 'profesion_id'];
        $missingParams = [];
        
        foreach ($requiredParams as $param) {
            if (!isset($_REQUEST[$param]) || empty($_REQUEST[$param])) {
                $missingParams[] = $param;
            }
        }
        
        if (!empty($missingParams)) {
            echo json_encode([["error" => "Faltan parámetros: " . implode(', ', $missingParams)]]);
            break;
        }
        
        $nombre = $_REQUEST["nombre"];
        $apellidos = $_REQUEST["apellidos"];
        $region_id = $_REQUEST["region_id"];
        $comuna_id = $_REQUEST["comuna_id"];
        $profesion_id = $_REQUEST["profesion_id"];
        $id = isset($_REQUEST["id"]) ? $_REQUEST["id"] : null;
        
        // Verificar si ya existe una persona con el mismo nombre y apellido
        $sqlCheck = "SELECT id FROM personas WHERE nombre = $1 AND apellidos = $2";
        $paramsCheck = array($nombre, $apellidos);
        
        if ($id) {
            $sqlCheck .= " AND id != $3";
            $paramsCheck[] = $id;
        }
        
        $resultCheck = pg_query_params($conn, $sqlCheck, $paramsCheck);
        
        if (pg_num_rows($resultCheck) > 0) {
            echo json_encode([["error" => "Ya existe una persona con ese nombre y apellido."]]);
            break;
        }
        
        if ($id) {
            // Actualizar persona existente
            $sql = "UPDATE personas SET nombre = $1, apellidos = $2, region_id = $3, comuna_id = $4, profesion_id = $5 
                    WHERE id = $6 
                    RETURNING id";
            $params = array($nombre, $apellidos, $region_id, $comuna_id, $profesion_id, $id);
        } else {
            // Insertar nueva persona
            $sql = "INSERT INTO personas (nombre, apellidos, region_id, comuna_id, profesion_id) 
                    VALUES ($1, $2, $3, $4, $5) 
                    RETURNING id";
            $params = array($nombre, $apellidos, $region_id, $comuna_id, $profesion_id);
        }
        
        $result = pg_query_params($conn, $sql, $params);
        $lastError = pg_last_error($conn);
        
        $obj = array();
        
        if ($lastError) {
            array_push($obj, array("error" => $lastError));
        } else {
            $row = pg_fetch_assoc($result);
            array_push($obj, array(
                "success" => true,
                "id" => $row['id'],
                "message" => $id ? "Persona actualizada correctamente." : "Persona creada correctamente."
            ));
        }
        
        echo json_encode($obj);
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
        echo json_encode([["error" => "Comando no reconocido."]]);
        break;
}
?>