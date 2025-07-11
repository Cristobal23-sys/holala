// Variable global para el ID en edición
let editId = null;

document.addEventListener("DOMContentLoaded", () => {
    cargarAtletas();
    cargarRegistros();
    document.getElementById("guardarBtn").addEventListener("click", guardarRegistro);
});

function cargarAtletas() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "command.php?cmd=atletas", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            const select = document.getElementById("atleta");
            select.innerHTML = '<option value="">-- Seleccione Atleta --</option>';
            data.forEach(a => {
                let option = document.createElement("option");
                option.value = a.id;
                option.textContent = a.nombre;
                select.appendChild(option);
            });
        }
    };
    xhr.send();
}

function cargarRegistros() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "command.php?cmd=listar", true);
    xhr.onload = function() {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            const tabla = document.getElementById("tablaCarreras");
            tabla.innerHTML = "";

            data.forEach(r => {
                let row = document.createElement("tr");
                row.innerHTML = `
                    <td>${r.nombre}</td>
                    <td>${r.tiempo}</td>
                    <td>${r.atleta}</td>
                    <td>${new Date(r.comienzo).toLocaleString()}</td>
                    <td>${r.avance}%</td>
                    <td>
                        <button class="editar" onclick='editar(${JSON.stringify(r)})'>Editar</button>
                        <button class="eliminar" onclick='eliminar(${r.id})'>Eliminar</button>
                    </td>
                `;
                tabla.appendChild(row);
            });
        }
    };
    xhr.send();
}

function guardarRegistro() {
    const params = new URLSearchParams();
    params.append('cmd', editId ? "editar" : "insertar");
    params.append('nombre', document.getElementById("nombre_carrera").value.trim());
    params.append('descripcion', document.getElementById("descripcion").value.trim());
    params.append('tiempo', document.getElementById("tiempo_carrera").value.trim());
    params.append('atleta', document.getElementById("atleta").value);
    params.append('avance', document.getElementById("avance").value.trim());
    
    if (editId) {
        params.append('id', editId);
    }

    // Convertir a objeto para validación
    const data = {
        nombre: params.get('nombre'),
        descripcion: params.get('descripcion'),
        tiempo: params.get('tiempo'),
        atleta: params.get('atleta'),
        avance: params.get('avance')
    };

    const error = validarCampos(data);
    if (error) {
        return alert(error);
    }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", `command.php?${params.toString()}`, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const resp = JSON.parse(xhr.responseText);
                alert(resp.mensaje);
                if (resp.exito) {
                    limpiarFormulario();
                    cargarRegistros();
                }
            } catch (e) {
                console.error("Respuesta inválida:", xhr.responseText);
                alert("Error al procesar respuesta del servidor.");
            }
        }
    };
    xhr.send();
}

function eliminar(id) {
    if (!confirm("¿Está seguro que desea eliminar este registro?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("GET", `command.php?cmd=eliminar&id=${id}`, true);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                const resp = JSON.parse(xhr.responseText);
                alert(resp.mensaje);
                if (resp.exito) {
                    limpiarFormulario();
                    cargarRegistros();
                }
            } catch (e) {
                console.error("Respuesta inválida:", xhr.responseText);
                alert("Error al procesar respuesta del servidor.");
            }
        }
    };
    xhr.send();
}

function editar(datos) {
    document.getElementById("nombre_carrera").value = datos.nombre;
    document.getElementById("descripcion").value = datos.descripcion;
    document.getElementById("tiempo_carrera").value = datos.tiempo;
    document.getElementById("atleta").value = datos.atleta_id;
    document.getElementById("avance").value = datos.avance;
    editId = datos.id;
    document.getElementById("guardarBtn").textContent = "Actualizar";
    window.scrollTo(0, 0);
}

function limpiarFormulario() {
    document.getElementById("marathonForm").reset();
    editId = null;
    document.getElementById("guardarBtn").textContent = "Guardar";
}

function validarCampos(data) {
    if (!data.nombre) return "El nombre de la carrera es requerido";
    if (!data.tiempo) return "El tiempo estimado es requerido";
    if (!data.atleta) return "Debe seleccionar un atleta";
    if (!data.avance || isNaN(data.avance)) return "El avance debe ser un número";
    if (data.avance < 0 || data.avance > 100) return "El avance debe estar entre 0 y 100";
    return null;
}