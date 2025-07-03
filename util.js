function validarCampos(data) {
    if (!data.nombre || data.nombre.length < 5)
        return "El campo 'Nombre Carrera' es obligatorio y debe tener al menos 5 caracteres.";

    if (!data.descripcion)
        return "El campo 'Descripción' es obligatorio.";

    if (!data.tiempo || !/^\d+$/.test(data.tiempo) || parseInt(data.tiempo) <= 0)
        return "El campo 'Tiempo Carrera' es obligatorio y debe ser un número entero positivo.";

    if (!data.atleta)
        return "Debe seleccionar un atleta.";

    if (!data.avance)
        return "El campo 'Porcentaje de Avance' es obligatorio.";

    if (!/^\d+$/.test(data.avance) || parseInt(data.avance) < 0 || parseInt(data.avance) > 100)
        return "El campo 'Porcentaje de Avance' debe estar entre 0 y 100.";

    return null;
}
