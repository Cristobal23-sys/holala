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

    default:
        die("cmd incorrecto");
        break;
}