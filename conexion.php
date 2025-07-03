<?php
// Parámetros de conexión
$host = "192.168.1.11";
$dbname = "cluna";
$user = "externo";
$password = "desis123";

// Cadena de conexión
$conn_string = "host=$host dbname=$dbname user=$user password=$password";

// Conexión
$conn = pg_connect($conn_string);

if (!$conn) {
    echo "Error: No se pudo conectar a la base de datos.\n";
    exit;
}
?>