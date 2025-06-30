  // 1. Verificación de Parámetros
        if (!isset($_GET['idpersona']) || empty($_GET['idpersona'])) {
            echo json_encode(['status' => 'error', 'message' => 'El parámetro idpersona es requerido.']);
            break;
        }

        // Obtener el id de la persona
        $idpersona = $_GET['idpersona'];
        
        // Inicializar la conexión usando la función de tu archivo de conexión
        $conn = conectar_pg();

        // Verificar si la conexión fue exitosa
        if (!$conn) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Error al conectar con la base de datos.']);
            break;
        }

        try {
            // 2. Ejecutar la Consulta (usando sentencias preparadas para seguridad)
            // Nota: PostgreSQL usa $1, $2, etc. como marcadores de posición
            $sql = "SELECT * FROM persona WHERE idpersona = $1";
            
            // Asignamos un nombre único a nuestra consulta preparada
            $query_name = "buscar_persona_query";

            // Preparar la consulta
            if (!pg_prepare($conn, $query_name, $sql)) {
                 throw new Exception("Error al preparar la consulta: " . pg_last_error($conn));
            }
           
            // Ejecutar la consulta preparada
            $result = pg_execute($conn, $query_name, [$idpersona]);

            if (!$result) {
                throw new Exception("Error al ejecutar la consulta: " . pg_last_error($conn));
            }

            // 4. Respuesta
            // Obtener la fila de resultados como un array asociativo
            $persona = pg_fetch_assoc($result);

            if ($persona) {
                // Si se encontró la persona, devolver sus datos
                echo json_encode(['status' => 'success', 'data' => $persona]);
            } else {
                // Si la consulta no devolvió filas
                echo json_encode(['status' => 'error', 'message' => 'Persona no encontrada con el ID proporcionado.']);
            }

        } catch (Exception $e) {
            // 3. Manejo de Errores
            http_response_code(500); 
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        } finally {
            // Cerrar la conexión a la base de datos si está abierta
            if ($conn) {
                pg_close($conn);
            }
        }

        // --- FIN DEL CASE buscarPersona ---
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Comando no válido.']);
        break;
}