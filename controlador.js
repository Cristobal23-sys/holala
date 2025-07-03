document.addEventListener("DOMContentLoaded", () => {
    cargarAtletas();
    cargarRegistros();

    document.getElementById("guardarBtn").addEventListener("click", guardarRegistro);
});

let editId = null;

function cargarAtletas() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "command.php?accion=atletas", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const data = JSON.parse(xhr.responseText);
            const select = document.getElementById("atleta");
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

function guardarRegistro() {
    const data = {
        id: editId,
        nombre: document.getElementById("nombre_carrera").value.trim(),
        descripcion: document.getElementById("descripcion").value.trim(),
        tiempo: document.getElementById("tiempo_carrera").value.trim(),
        atleta: document.getElementById("atleta").value,
        avance: document.getElementById("avance").value.trim()
    };

    const error = validarCampos(data);
    if (error) return alert(error);

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "command.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const resp = JSON.parse(xhr.responseText);
            alert(resp.mensaje);
            if (resp.exito) {
                limpiarFormulario();
                cargarRegistros();
            }
        }
    };

    data.accion = editId ? "editar" : "insertar";
    xhr.send(JSON.stringify(data));
}

function cargarRegistros() {
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "command.php?accion=listar", true);
    xhr.onload = function () {
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
                    <td>${r.comienzo}</td>
                    <td>${r.avance}</td>
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

function editar(datos) {
    document.getElementById("nombre_carrera").value = datos.nombre;
    document.getElementById("descripcion").value = datos.descripcion;
    document.getElementById("tiempo_carrera").value = datos.tiempo;
    document.getElementById("atleta").value = datos.atleta_id;
    document.getElementById("avance").value = datos.avance;
    editId = datos.id;
}

function eliminar(id) {
    if (!confirm("¿Está seguro que desea eliminar este registro?")) return;

    const xhr = new XMLHttpRequest();
    xhr.open("POST", "command.php", true);
    xhr.setRequestHeader("Content-Type", "application/json");

    xhr.onload = function () {
        if (xhr.status === 200) {
            const resp = JSON.parse(xhr.responseText);
            alert(resp.mensaje);
            if (resp.exito) {
                limpiarFormulario();
                cargarRegistros();
            }
        }
    };

    xhr.send(JSON.stringify({ accion: "eliminar", id }));
}

function limpiarFormulario() {
    document.getElementById("marathonForm").reset();
    editId = null;
}
