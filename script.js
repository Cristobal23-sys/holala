document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
    const form = document.getElementById('personaForm');
    const nombreInput = document.getElementById('nombre');
    const apellidosInput = document.getElementById('apellidos');
    const regionSelect = document.getElementById('region');
    const comunaSelect = document.getElementById('comuna');
    const profesionSelect = document.getElementById('profesion');
    const submitBtn = document.getElementById('submitBtn');
    const personaIdInput = document.getElementById('personaId');
    const personasGrid = document.getElementById('personasGrid').getElementsByTagName('tbody')[0];

    // Variables para datos
    let comunas = [];
    let isUpdating = false;

    // Cargar datos iniciales
    loadData();

    // Eventos
    regionSelect.addEventListener('change', function() {
        updateComunas();
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            savePersona();
        }
    });

    // Funciones básicas
    function loadData() {
        // Cargar regiones
        fetch('obtener_datos.php?tabla=regiones')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fillSelect(regionSelect, data.data, 'Seleccione región');
                }
            });

        // Cargar profesiones
        fetch('obtener_datos.php?tabla=profesiones')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fillSelect(profesionSelect, data.data, 'Seleccione profesión');
                }
            });

        // Cargar todas las comunas
        fetch('obtener_datos.php?tabla=comunas')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    comunas = data.data;
                }
            });

        // Cargar personas
        fetch('obtener_datos.php?tabla=personas')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderPersonas(data.data);
                }
            });
    }

    function fillSelect(select, items, defaultText) {
        select.innerHTML = `<option value="">${defaultText}</option>`;
        items.forEach(item => {
            select.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
        });
    }

    function updateComunas() {
        const regionId = regionSelect.value;
        comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
        comunaSelect.disabled = !regionId;

        if (regionId) {
            const filtered = comunas.filter(c => c.region_id == regionId);
            filtered.forEach(c => {
                comunaSelect.innerHTML += `<option value="${c.id}">${c.nombre}</option>`;
            });
        }
    }

    function renderPersonas(personas) {
        personasGrid.innerHTML = '';
        personas.forEach(p => {
            const row = personasGrid.insertRow();
            row.innerHTML = `
                <td>${p.nombre}</td>
                <td>${p.apellidos}</td>
                <td>${p.region_nombre}</td>
                <td>${p.comuna_nombre}</td>
                <td>${p.profesion_nombre}</td>
                <td><button class="action-btn" data-id="${p.id}">Modificar</button></td>
            `;
        });

        // Eventos para botones de modificar
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                loadPersona(this.getAttribute('data-id'));
            });
        });
    }

    function loadPersona(id) {
        fetch(`obtener_datos.php?tabla=personas&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.length > 0) {
                    const p = data.data[0];
                    nombreInput.value = p.nombre;
                    apellidosInput.value = p.apellidos;
                    personaIdInput.value = p.id;
                    regionSelect.value = p.region_id;
                    updateComunas();
                    setTimeout(() => {
                        comunaSelect.value = p.comuna_id;
                        profesionSelect.value = p.profesion_id;
                    }, 100);
                    isUpdating = true;
                    submitBtn.textContent = 'Actualizar';
                }
            });
    }

    function validateForm() {
        if (!nombreInput.value.trim()) {
            alert('Ingrese un nombre');
            return false;
        }
        if (!apellidosInput.value.trim()) {
            alert('Ingrese apellidos');
            return false;
        }
        if (!regionSelect.value) {
            alert('Seleccione una región');
            return false;
        }
        if (!comunaSelect.value) {
            alert('Seleccione una comuna');
            return false;
        }
        if (!profesionSelect.value) {
            alert('Seleccione una profesión');
            return false;
        }
        return true;
    }

    function savePersona() {
        const formData = new FormData();
        formData.append('cmd', 'guardar_persona');
        formData.append('nombre', nombreInput.value.trim());
        formData.append('apellidos', apellidosInput.value.trim());
        formData.append('region_id', regionSelect.value);
        formData.append('comuna_id', comunaSelect.value);
        formData.append('profesion_id', profesionSelect.value);
        
        if (isUpdating) {
            formData.append('id', personaIdInput.value);
        }

        fetch('command.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(isUpdating ? 'Actualizado correctamente' : 'Guardado correctamente');
                resetForm();
                loadData();
            } else {
                alert(data.error || 'Error al guardar');
            }
        });
    }

    function resetForm() {
        form.reset();
        personaIdInput.value = '';
        comunaSelect.disabled = true;
        isUpdating = false;
        submitBtn.textContent = 'Enviar';
    }
});