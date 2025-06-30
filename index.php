<?php
require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="ISO-8859-1">
    <title>Formulario Personas</title>
    
</head>
<body>
    <h1>Mi Formulario</h1>
<form id="personaForm">
    <input type="hidden" name="id">
    <div>
        <label>Nombre</label>
        <input type="text" name="nombre" required>
    </div>
    <div>
        <label>Apellidos</label>
        <input type="text" name="apellidos" required>
    </div>
    <div>
        <label>Región</label>
        <select name="region" required></select>
    </div>
    <div>
        <label>Comuna</label>
        <select name="comuna" required disabled></select>
    </div>
    <div>
        <label>Profesión</label>
        <select name="profesion" required></select>
    </div>
    <button type="submit">Enviar</button>
</form>


    <h2>Personas Registradas</h2>
    <table id="personasTable" border="1">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Región</th>
                <th>Comuna</th>
                <th>Profesión</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</body>
<script>
        let appData = {};
        document.addEventListener('DOMContentLoaded', () => {
            fetch('obtener_datos.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        appData = data;
                        initForm();
                    } else {
                        alert("Error al cargar datos iniciales");
                    }
                });
        });
    </script>
    <script src="script.js" defer></script>
</html>
