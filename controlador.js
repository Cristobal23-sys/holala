// Función para limpiar el formulario
function limpiarFormulario() {
    document.getElementById('formReserva').reset();
    document.getElementById('idcomuna').innerHTML = '<option value="">Seleccione comuna</option>';
}

// Función para realizar la reserva
function reservar() {
    const formData = new FormData(document.getElementById('formReserva'));
    formData.append('cmd', 'insertar_reserva');

    fetch('Command.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Reserva guardada correctamente');
            limpiarFormulario();
            cargaGrilla();
            cargaGrillaPorcentaje();
        } else {
            alert('Error: ' + data.error);
        }
    })
    .catch(error => alert('Error: ' + error));
}

// Función para cargar la grilla de reservas
function cargaGrilla() {
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cmd=obtener_reservas'
    })
    .then(response => response.json())
    .then(data => {
        let html = '<table><tr><th>ID</th><th>Nombre</th><th>Email</th><th>Ciudad</th><th>Comuna</th><th>Fecha</th><th>Horario</th><th>Acciones</th></tr>';
        
        data.forEach(reserva => {
            html += `<tr>
                <td>${reserva.id}</td>
                <td>${reserva.nombre}</td>
                <td>${reserva.email}</td>
                <td>${reserva.ciudad}</td>
                <td>${reserva.comuna}</td>
                <td>${reserva.fecha}</td>
                <td>${reserva.horario}</td>
                <td>
                    <button onclick="eliminaReserva(${reserva.id})">Eliminar</button>
                    ${reserva.recordar === 't' ? `<button onclick="recordarReserva(${reserva.id})">Recordar</button>` : ''}
                </td>
            </tr>`;
        });
        
        html += '</table>';
        document.getElementById('grillaReservas').innerHTML = html;
    });
}

// Función para cargar la grilla de porcentajes
function cargaGrillaPorcentaje() {
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cmd=obtener_porcentajes'
    })
    .then(response => response.json())
    .then(data => {
        let html = '<table><tr><th>Horario</th><th>Cantidad</th><th>Porcentaje</th></tr>';
        
        data.forEach(item => {
            html += `<tr>
                <td>${item.hora}</td>
                <td>${item.cantidad}</td>
                <td>${item.porcentaje.toFixed(2)}%</td>
            </tr>`;
        });
        
        html += '</table>';
        document.getElementById('grillaPorcentajes').innerHTML = html;
    });
}

// Función para eliminar una reserva
function eliminaReserva(idreserva) {
    if (confirm('¿Está seguro de eliminar esta reserva?')) {
        fetch('Command.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `cmd=eliminar_reserva&idreserva=${idreserva}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cargaGrilla();
                cargaGrillaPorcentaje();
            } else {
                alert('Error: ' + data.error);
            }
        });
    }
}

// Función para cargar las ciudades
function cargaCiudad() {
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cmd=obtener_ciudades'
    })
    .then(response => response.json())
    .then(data => {
        let select = document.getElementById('idciudad');
        select.innerHTML = '<option value="">Seleccione ciudad</option>';
        
        data.forEach(ciudad => {
            select.innerHTML += `<option value="${ciudad.id}">${ciudad.nombre}</option>`;
        });
    });
}

// Función para recargar las comunas
function recargaComuna() {
    const idciudad = document.getElementById('idciudad').value;
    
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cmd=obtener_comunas&idciudad=${idciudad}`
    })
    .then(response => response.json())
    .then(data => {
        let select = document.getElementById('idcomuna');
        select.innerHTML = '<option value="">Seleccione comuna</option>';
        
        data.forEach(comuna => {
            select.innerHTML += `<option value="${comuna.id}">${comuna.nombre}</option>`;
        });
    });
}

// Función para cargar los horarios
function cargaHorario() {
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'cmd=obtener_horarios'
    })
    .then(response => response.json())
    .then(data => {
        let select = document.getElementById('idhorario');
        select.innerHTML = '<option value="">Seleccione horario</option>';
        
        data.forEach(horario => {
            select.innerHTML += `<option value="${horario.id}">${horario.hora}</option>`;
        });
    });
}

// Función para recordar reserva
function recordarReserva(idreserva) {
    fetch('Command.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `cmd=obtener_reserva&idreserva=${idreserva}`
    })
    .then(response => response.json())
    .then(reserva => {
        const fechaReserva = new Date(reserva.fecha);
        const hoy = new Date();
        const mañana = new Date();
        mañana.setDate(hoy.getDate() + 1);
        
        if (fechaReserva.toDateString() === mañana.toDateString()) {
            alert(`Se debe recordar reserva a email ${reserva.email}`);
        } else if (fechaReserva < hoy) {
            alert('Ya es muy tarde para recordar reserva');
        } else {
            alert('Aún no es tiempo de recordar la reserva');
        }
    });
}

// Cargar datos iniciales al cargar la página
window.onload = function() {
    cargaCiudad();
    cargaHorario();
    cargaGrilla();
    cargaGrillaPorcentaje();
    
    // Evento para cargar comunas cuando cambia la ciudad
    document.getElementById('idciudad').addEventListener('change', recargaComuna);
};