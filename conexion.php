<?php

$conn = pg_connect("host=192.168.1.11 dbname=cluna user=externo password=desis123");

if (!$conn) {
    die("Error de conexión: " . pg_last_error());
}
?>