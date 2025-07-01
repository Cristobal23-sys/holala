document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('personaForm');
    const regionSelect = form.region;
    const comunaSelect = form.comuna;
    const profesionSelect = form.profesion;
    const btnSubmit = form.querySelector('button');
    const inputId = form.querySelector('input[name="id"]');
    const tableBody = document.querySelector('#personasTable tbody');

    // Cargar regiones y profesiones con AJAX
    fetch('command.php?cmd=get_regiones')
        .then(res => res.json())
        .then(data => {
            regionSelect.innerHTML = '<option value="">Seleccione región</option>';
            data.forEach(reg => {
                regionSelect.innerHTML += `<option value="${reg.id}">${reg.nombre}</option>`;
            });
        });

    fetch('command.php?cmd=get_profesiones')
        .then(res => res.json())
        .then(data => {
            profesionSelect.innerHTML = '<option value="">Seleccione profesión</option>';
            data.forEach(p => {
                profesionSelect.innerHTML += `<option value="${p.id}">${p.nombre}</option>`;
            });
        });

    // Al cambiar región, cargar comunas
    regionSelect.addEventListener('change', () => {
        comunaSelect.innerHTML = '<option value="">Cargando...</option>';
        comunaSelect.disabled = true;
        fetch(`command.php?cmd=get_comunas&region_id=${regionSelect.value}`)
            .then(res => res.json())
            .then(data => {
                comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
                data.forEach(c => {
                    comunaSelect.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                });
                comunaSelect.disabled = false;
            });
    });

    // Enviar formulario
    form.addEventListener('submit', e => {
        e.preventDefault();

        const formData = new FormData(form);
        formData.append('cmd', 'guardar_persona');

        // Validación de duplicados
        fetch('command.php?cmd=verificar_duplicado', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(dup => {
            if (dup.duplicado) {
                alert("Ya existe una persona con ese nombre y apellido.");
                return;
            }

            // Enviar datos para guardar o actualizar
            fetch('command.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    alert('Datos guardados correctamente.');
                    form.reset();
                    inputId.value = '';
                    btnSubmit.textContent = 'Enviar';
                    comunaSelect.disabled = true;
                    cargarTabla();
                } else {
                    alert("Error: " + data.error);
                }
            });
        });
    });

    // Cargar tabla
    function cargarTabla() {
        fetch('command.php?cmd=get_personas')
            .then(res => res.json())
            .then(data => {
                tableBody.innerHTML = '';
                data.forEach(p => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${p.nombre}</td>
                        <td>${p.apellidos}</td>
                        <td>${p.region}</td>
                        <td>${p.comuna}</td>
                        <td>${p.profesion}</td>
                        <td><button data-id="${p.id}" class="modificar">Modificar</button></td>
                    `;
                    tableBody.appendChild(row);
                });
            });
    }

    // Al hacer clic en "Modificar"
    tableBody.addEventListener('click', e => {
        if (e.target.classList.contains('modificar')) {
            const id = e.target.dataset.id;

            fetch(`command.php?cmd=get_persona&id=${id}`)
                .then(res => res.json())
                .then(p => {
                    form.nombre.value = p.nombre;
                    form.apellidos.value = p.apellidos;
                    form.region.value = p.region_id;
                    inputId.value = p.id;

                    // cargar comunas para esa región
                    fetch(`command.php?cmd=get_comunas&region_id=${p.region_id}`)
                        .then(res => res.json())
                        .then(comunas => {
                            comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
                            comunas.forEach(c => {
                                comunaSelect.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
                            });
                            comunaSelect.disabled = false;
                            form.comuna.value = p.comuna_id;
                        });

                    form.profesion.value = p.profesion_id;
                    btnSubmit.textContent = 'Actualizar';
                });
        }
    });

    cargarTabla();
});




function cargaHorario() {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', 'Command.php?cmd=obtener_horarios', true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            var select = document.getElementById('idhorario');
            
            if (xhr.status !== 200) {
                select.innerHTML = '<option value="">Error al cargar horarios</option>';
                console.error('Error HTTP:', xhr.status);
                return;
            }
            
            try {
                var response = xhr.responseText;
                var data = JSON.parse(response);
                
                // Validar que sea un array
                if (!Array.isArray(data)) {
                    throw new Error('La respuesta no es un array');
                }
                
                // Limpiar y agregar opción por defecto
                select.innerHTML = '<option value="">Seleccione horario</option>';
                
                // Llenar select con horarios
                data.forEach(function(horario) {
                    // Validar estructura esperada
                    if (horario && horario.idhorario !== undefined && horario.descripcion !== undefined) {
                        var option = document.createElement('option');
                        option.value = horario.idhorario;
                        option.textContent = horario.descripcion;
                        select.appendChild(option);
                    }
                });
                
                // Si no se agregaron opciones
                if (select.options.length === 1) {
                    select.innerHTML = '<option value="">No hay horarios disponibles</option>';
                }
                
            } catch (e) {
                console.error('Error al procesar horarios:', e, 'Respuesta:', response);
                select.innerHTML = '<option value="">Error cargando horarios</option>';
            }
        }
    };
    
    xhr.onerror = function() {
        console.error('Error de red al cargar horarios');
        document.getElementById('idhorario').innerHTML = '<option value="">Error de conexión</option>';
    };
    
    xhr.send();
}