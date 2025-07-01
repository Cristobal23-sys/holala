<?php
include 'Conexion.php';

$cmd = isset($_POST['cmd']) ? $_POST['cmd'] : '';

switch ($cmd) {
    case 'obtener_ciudades':
        $result = pg_query($conexion, "SELECT * FROM ciudad");
        $ciudades = array();
        while ($row = pg_fetch_assoc($result)) {
            $ciudades[] = $row;
        }
        echo json_encode($ciudades);
        break;
        
    case 'obtener_horarios':
        $result = pg_query($conexion, "SELECT * FROM horario");
        $horarios = array();
        while ($row = pg_fetch_assoc($result)) {
            $horarios[] = $row;
        }
        echo json_encode($horarios);
        break;
        
    case 'obtener_comunas':
        $idciudad = $_POST['idciudad'];
        $result = pg_query($conexion, "SELECT * FROM comuna WHERE idciudad = $idciudad");
        $comunas = array();
        while ($row = pg_fetch_assoc($result)) {
            $comunas[] = $row;
        }
        echo json_encode($comunas);
        break;
        
    case 'insertar_reserva':
        $nombre = pg_escape_string($_POST['nombre']);
        $email = pg_escape_string($_POST['email']);
        $idciudad = $_POST['idciudad'];
        $idcomuna = $_POST['idcomuna'];
        $idhorario = $_POST['idhorario'];
        $fecha = pg_escape_string($_POST['fecha']);
        $recordar = $_POST['recordar'] ? 'true' : 'false';
        
        $query = "INSERT INTO reserva (nombre, email, idciudad, idcomuna, idhorario, fecha, recordar) 
                 VALUES ('$nombre', '$email', $idciudad, $idcomuna, $idhorario, '$fecha', $recordar)";
        
        if (pg_query($conexion, $query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => pg_last_error()));
        }
        break;
        
    case 'obtener_reservas':
        $result = pg_query($conexion, "SELECT r.*, c.nombre as ciudad, co.nombre as comuna, h.hora as horario 
                                      FROM reserva r
                                      JOIN ciudad c ON r.idciudad = c.id
                                      JOIN comuna co ON r.idcomuna = co.id
                                      JOIN horario h ON r.idhorario = h.id");
        $reservas = array();
        while ($row = pg_fetch_assoc($result)) {
            $reservas[] = $row;
        }
        echo json_encode($reservas);
        break;
        
    case 'obtener_porcentajes':
        $result = pg_query($conexion, "SELECT h.hora, COUNT(*) as cantidad, 
                                      (COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reserva)) as porcentaje
                                      FROM reserva r
                                      JOIN horario h ON r.idhorario = h.id
                                      GROUP BY h.hora
                                      ORDER BY cantidad DESC");
        $porcentajes = array();
        while ($row = pg_fetch_assoc($result)) {
            $porcentajes[] = $row;
        }
        echo json_encode($porcentajes);
        break;
        
    case 'eliminar_reserva':
        $idreserva = $_POST['idreserva'];
        $query = "DELETE FROM reserva WHERE id = $idreserva";
        if (pg_query($conexion, $query)) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('error' => pg_last_error()));
        }
        break;
        
    case 'obtener_reserva':
        $idreserva = $_POST['idreserva'];
        $result = pg_query($conexion, "SELECT * FROM reserva WHERE id = $idreserva");
        $reserva = pg_fetch_assoc($result);
        echo json_encode($reserva);
        break;
        
    default:
        echo json_encode(array('error' => 'Comando no reconocido'));
        break;
}
?>