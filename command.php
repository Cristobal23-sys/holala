<?php
include 'Conexion.php';

header('Content-Type: application/json');

try {
    $cmd = $_GET['cmd'] ?? '';
    
    switch ($cmd) {
        // Obtener ciudades
        case 'obtener_ciudades':
            $result = pg_query($conexion, "SELECT id, nombre FROM ciudad ORDER BY nombre");
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_all($result) ?: []);
            break;
            
        // Obtener horarios
        case 'obtener_horarios':
            $result = pg_query($conexion, "SELECT id, hora FROM horario ORDER BY hora");
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_all($result) ?: []);
            break;
            
        // Obtener comunas por ciudad
        case 'obtener_comunas':
            $idciudad = $_GET['idciudad'] ?? 0;
            $result = pg_query_params($conexion, 
                "SELECT id, nombre FROM comuna WHERE idciudad = $1 ORDER BY nombre", 
                [$idciudad]);
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_all($result) ?: []);
            break;
            
        // Insertar reserva (ahora con GET)
        case 'insertar_reserva':
            $params = [
                'nombre' => $_GET['nombre'] ?? '',
                'email' => $_GET['email'] ?? '',
                'idciudad' => $_GET['idciudad'] ?? 0,
                'idcomuna' => $_GET['idcomuna'] ?? 0,
                'idhorario' => $_GET['idhorario'] ?? 0,
                'fecha' => $_GET['fecha'] ?? '',
                'recordar' => $_GET['recordar'] ?? false
            ];
            
            $result = pg_query_params($conexion,
                "INSERT INTO reserva (nombre, email, idciudad, idcomuna, idhorario, fecha, recordar) 
                 VALUES ($1, $2, $3, $4, $5, $6, $7) RETURNING id",
                array_values($params));
                
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(['success' => true, 'id' => pg_fetch_result($result, 0, 0)]);
            break;
            
        // Obtener todas las reservas
        case 'obtener_reservas':
            $result = pg_query($conexion,
                "SELECT r.id, r.nombre, r.email, r.fecha, r.recordar,
                        c.nombre as ciudad, co.nombre as comuna, h.hora as horario
                 FROM reserva r
                 JOIN ciudad c ON r.idciudad = c.id
                 JOIN comuna co ON r.idcomuna = co.id
                 JOIN horario h ON r.idhorario = h.id
                 ORDER BY r.fecha DESC");
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_all($result) ?: []);
            break;
            
        // Obtener estadísticas de horarios
        case 'obtener_porcentajes':
            $result = pg_query($conexion,
                "SELECT h.hora, COUNT(*) as cantidad,
                        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reserva), 2) as porcentaje
                 FROM reserva r
                 JOIN horario h ON r.idhorario = h.id
                 GROUP BY h.hora
                 ORDER BY cantidad DESC");
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_all($result) ?: []);
            break;
            
        // Eliminar reserva
        case 'eliminar_reserva':
            $idreserva = $_GET['idreserva'] ?? 0;
            $result = pg_query_params($conexion,
                "DELETE FROM reserva WHERE id = $1",
                [$idreserva]);
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(['success' => pg_affected_rows($result) > 0]);
            break;
            
        // Obtener datos de una reserva específica
        case 'obtener_reserva':
            $idreserva = $_GET['idreserva'] ?? 0;
            $result = pg_query_params($conexion,
                "SELECT * FROM reserva WHERE id = $1",
                [$idreserva]);
            if (!$result) throw new Exception(pg_last_error($conexion));
            echo json_encode(pg_fetch_assoc($result) ?: []);
            break;
            
        default:
            echo json_encode(['error' => 'Comando no reconocido']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>