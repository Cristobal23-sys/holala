<?php
include 'conexion.php';

$cmd = $_REQUEST["cmd"]; //$_POST O $_GET
switch ($cmd){
    // https://dev.facturacion.cl/jrodas202312/semana1/command?cmd=operacionBasica&num1=10&num2=20&operador=-
    //OPERADOR "+" por %2B (codificación)
    // Verifica si la solicitud tiene el comando 'operacionBasica y se tiene la función creada en su base de dato'
    case 'operacionBasica':

        // ... tu código existente para operacionBasica ...
        break;

    case "vistauno":

        // ... tu código existente para vistauno ...
        break;

    // ========= INICIO DEL NUEVO CASE 'buscarPersona' =========
    case 'buscarPersona':
        // 1. Verificación de Parámetros
        // Nos aseguramos de que el parámetro 'idpersona' exista en la solicitud.
        if (!isset($_REQUEST["idpersona"]) || empty($_REQUEST["idpersona"])) {
            // Si no existe, devolvemos un JSON con un mensaje de error y terminamos.
            // Se usa un array dentro de otro para mantener el formato [ {"error": "..."} ]
            echo json_encode([["error" => "El parámetro idpersona es requerido."]]);
            break;
        }

        // Guardamos el idpersona en una variable.
        $idpersona = $_REQUEST["idpersona"];

        // 2. Ejecutar la Consulta
        // Preparamos el SQL. PostgreSQL usa $1, $2, etc., como marcadores de posición.
        $sql = "SELECT * FROM persona WHERE idpersona = $1";

        // Creamos el array de parámetros que reemplazará a los marcadores de posición.
        $params = array($idpersona);

        // Ejecutamos la consulta usando pg_query_params para más seguridad.
        $result = pg_query_params($conn, $sql, $params);

        // Verificamos si la base de datos arrojó algún error durante la consulta.
        $lastError = pg_last_error($conn);

        // Creamos el array que contendrá la respuesta final.
        $obj = array();

        // 3. Manejo de Errores y Respuesta
        if ($lastError) {
            // Si hubo un error en la consulta SQL, lo agregamos a la respuesta.
            array_push($obj, array(
                "error" => $lastError
            ));
        } else {
            // Si la consulta se ejecutó bien, verificamos si se encontró a la persona.
            if (pg_num_rows($result) > 0) {
                // Si se encontró, obtenemos la fila como un objeto.
                $row = pg_fetch_object($result);
                // Agregamos el objeto completo de la persona al array de respuesta.
                array_push($obj, $row);
            } else {
                // Si no se encontraron filas, significa que no existe una persona con ese ID.
                array_push($obj, array(
                    "error" => "Persona no encontrada con el ID proporcionado."
                ));
            }
        }

        // 4. Devolver la respuesta final en formato JSON.
        echo json_encode($obj);
        break;
    // ========= FIN DEL NUEVO CASE 'buscarPersona' =========

    // ========= INICIO DEL NUEVO CASE 'listarProfesiones' =========
    case 'listarProfesiones':
        // Ejecutar la Consulta: Realiza una consulta a la tabla profesion para obtener todas las profesiones.
        $sql = "SELECT * FROM profesion";
        $result = pg_query($conn, $sql);

        // Creamos el array que contendrá la respuesta final.
        $obj = array();

        // Manejo de Errores: Captura y maneja posibles errores en la ejecución de la consulta.
        $lastError = pg_last_error($conn);

        if ($lastError) {
            // Si hubo un error en la consulta SQL, lo agregamos a la respuesta.
            array_push($obj, array(
                "error" => $lastError
            ));
        } else {
            // Si la consulta se ejecutó bien, obtenemos todas las filas.
            while ($row = pg_fetch_object($result)) {
                array_push($obj, $row);
            }
            if (empty($obj)) {
                array_push($obj, array(
                    "message" => "No se encontraron profesiones."
                ));
            }
        }

        // Respuesta: Devuelve la lista de profesiones en formato JSON.
        echo json_encode($obj);
        break;
    // ========= FIN DEL NUEVO CASE 'listarProfesiones' =========

    // ========= INICIO DEL NUEVO CASE 'eliminarPersona' =========
    case 'eliminarPersona':
        // Verificación de Parámetros: Asegúrate de que el parámetro idpersona esté presente.
        if (!isset($_REQUEST["idpersona"]) || empty($_REQUEST["idpersona"])) {
            echo json_encode([["error" => "El parámetro idpersona es requerido para eliminar."]]);
            break;
        }

        $idpersona = $_REQUEST["idpersona"];

        // Ejecutar la Consulta: Realiza una consulta para eliminar la persona.
        $sql = "DELETE FROM persona WHERE idpersona = $1";
        $params = array($idpersona);
        $result = pg_query_params($conn, $sql, $params);

        $obj = array();
        $lastError = pg_last_error($conn);

        // Manejo de Errores y Respuesta
        if ($lastError) {
            array_push($obj, array(
                "error" => "Error al eliminar la persona: " . $lastError
            ));
        } else {
            if (pg_affected_rows($result) > 0) {
                array_push($obj, array(
                    "message" => "Persona eliminada exitosamente."
                ));
            } else {
                array_push($obj, array(
                    "error" => "No se encontró la persona con el ID proporcionado para eliminar."
                ));
            }
        }

        echo json_encode($obj);
        break;
    // ========= FIN DEL NUEVO CASE 'eliminarPersona' =========

    // ========= INICIO DEL NUEVO CASE 'actualizarProfesion' =========
    case 'actualizarProfesion':
        // Verificación de Parámetros: Asegúrate de que los parámetros idprofesion y nombre estén presentes.
        if (!isset($_REQUEST["idprofesion"]) || empty($_REQUEST["idprofesion"]) || !isset($_REQUEST["nombre"]) || empty($_REQUEST["nombre"])) {
            echo json_encode([["error" => "Los parámetros idprofesion y nombre son requeridos para actualizar."]]);
            break;
        }

        $idprofesion = $_REQUEST["idprofesion"];
        $nombre = $_REQUEST["nombre"];

        // Ejecutar la Consulta: Realiza una consulta para actualizar el nombre de la profesión.
        $sql = "UPDATE profesion SET nombre = $1 WHERE idprofesion = $2";
        $params = array($nombre, $idprofesion);
        $result = pg_query_params($conn, $sql, $params);

        $obj = array();
        $lastError = pg_last_error($conn);

        // Manejo de Errores y Respuesta
        if ($lastError) {
            array_push($obj, array(
                "error" => "Error al actualizar la profesión: " . $lastError
            ));
        } else {
            if (pg_affected_rows($result) > 0) {
                array_push($obj, array(
                    "message" => "Profesión actualizada exitosamente."
                ));
            } else {
                array_push($obj, array(
                    "error" => "No se encontró la profesión con el ID proporcionado o no hubo cambios."
                ));
            }
        }

        echo json_encode($obj);
        break;
    // ========= FIN DEL NUEVO CASE 'actualizarProfesion' =========

    // ========= INICIO DEL NUEVO CASE 'fn_persona_iu' =========
    case 'fn_persona_iu':
        // Verificación de Parámetros: Asegúrate de que todos los parámetros necesarios estén presentes.
        if (!isset($_REQUEST["idpersona"]) || !isset($_REQUEST["nombre"]) || empty($_REQUEST["nombre"]) || !isset($_REQUEST["apellidos"]) || empty($_REQUEST["apellidos"]) || !isset($_REQUEST["idprofesion"]) || empty($_REQUEST["idprofesion"])) {
            echo json_encode([["error" => "Todos los parámetros (idpersona, nombre, apellidos, idprofesion) son requeridos."]]);
            break;
        }

        $idpersona = $_REQUEST["idpersona"];
        $nombre = $_REQUEST["nombre"];
        $apellidos = $_REQUEST["apellidos"];
        $idprofesion = $_REQUEST["idprofesion"];

        // Ejecutar la Función: Llama a la función PostgreSQL fn_persona_iu.
        // Asumiendo que la función PostgreSQL se llama 'fn_persona_iu' y devuelve un TEXT
        $sql = "SELECT fn_persona_iu($1, $2, $3, $4) as message";
        $params = array($idpersona, $nombre, $apellidos, $idprofesion);
        $result = pg_query_params($conn, $sql, $params);

        $obj = array();
        $lastError = pg_last_error($conn);

        // Manejo de Errores y Respuesta
        if ($lastError) {
            array_push($obj, array(
                "error" => "Error al ejecutar la función fn_persona_iu: " . $lastError
            ));
        } else {
            $row = pg_fetch_object($result);
            if ($row) {
                // La función PostgreSQL debería devolver un mensaje de éxito o error
                array_push($obj, array(
                    "message" => $row->message
                ));
            } else {
                array_push($obj, array(
                    "error" => "La función fn_persona_iu no devolvió un resultado esperado."
                ));
            }
        }

        echo json_encode($obj);
        break;
    // ========= FIN DEL NUEVO CASE 'fn_persona_iu' =========

    default:
        die("cmd incorrecto");
        break;
}
?>