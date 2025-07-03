<?php

header("Content-Type: application/json");
require_once 'conexion.php';

$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : null;

switch ($cmd) {
    case "insertar":
        // Verificar si el atleta ya está registrado en la carrera
        $res = pg_query_params($conn, "SELECT COUNT(*) FROM carreras.carreras WHERE nombre=$1 AND atleta_id=$2", 
            [$_GET['nombre'], $_GET['atleta']]);
        if (pg_fetch_result($res, 0, 0) > 0) {
            echo json_encode(["exito" => false, "mensaje" => "Este atleta ya está registrado en esta carrera."]);
            exit;
        }

        $q = pg_query_params($conn, 
            "INSERT INTO carreras.carreras(nombre, descripcion, tiempo, atleta_id, avance) VALUES ($1, $2, $3, $4, $5)", 
            [$_GET['nombre'], $_GET['descripcion'], $_GET['tiempo'], $_GET['atleta'], $_GET['avance']]);
        echo json_encode(["exito" => $q, "mensaje" => $q ? "Registro guardado exitosamente." : "Error al guardar."]);
        break;

    case "editar":
        // Verificar si el atleta ya está registrado en otra carrera (excluyendo la actual)
        $res = pg_query_params($conn, 
            "SELECT COUNT(*) FROM carreras.carreras WHERE nombre=$1 AND atleta_id=$2 AND id<>$3", 
            [$_GET['nombre'], $_GET['atleta'], $_GET['id']]);
        if (pg_fetch_result($res, 0, 0) > 0) {
            echo json_encode(["exito" => false, "mensaje" => "Este atleta ya está registrado en esta carrera."]);
            exit;
        }

        $q = pg_query_params($conn, 
            "UPDATE carreras.carreras SET nombre=$1, descripcion=$2, tiempo=$3, atleta_id=$4, avance=$5 WHERE id=$6", 
            [$_GET['nombre'], $_GET['descripcion'], $_GET['tiempo'], $_GET['atleta'], $_GET['avance'], $_GET['id']]);
        echo json_encode(["exito" => $q, "mensaje" => $q ? "Registro actualizado exitosamente." : "Error al actualizar."]);
        break;

    case "eliminar":
        $q = pg_query_params($conn, "DELETE FROM carreras.carreras WHERE id=$1", [$_GET['id']]);
        echo json_encode(["exito" => $q, "mensaje" => $q ? "Registro eliminado correctamente." : "Error al eliminar."]);
        break;

    case "listar":
        $res = pg_query($conn, "SELECT c.id, c.nombre, c.descripcion, c.tiempo, c.avance, c.fecha_registro as comienzo, a.nombre as atleta, a.id as atleta_id FROM carreras.carreras c INNER JOIN atletas a ON a.id = c.atleta_id ORDER BY c.id DESC");
        $datos = [];
        while ($r = pg_fetch_assoc($res)) {
            $datos[] = $r;
        }
        echo json_encode($datos);
        break;

    case "atletas":
        $res = pg_query($conn, "SELECT id, nombre FROM atletas ORDER BY nombre");
        $datos = [];
        while ($r = pg_fetch_assoc($res)) {
            $datos[] = $r;
        }
        echo json_encode($datos);
        break;

    default:
        echo json_encode(["exito" => false, "mensaje" => "Comando no reconocido"]);
        break;
}