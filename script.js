document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos del formulario (asumiendo estructura mostrada en la imagen)
    const form = document.querySelector('form');
    const nombreInput = document.querySelector('input[type="text"]:nth-of-type(1)');
    const apellidosInput = document.querySelector('input[type="text"]:nth-of-type(2)');
    const regionSelect = document.querySelector('select:nth-of-type(1)');
    const comunaSelect = document.querySelector('select:nth-of-type(2)');
    const profesionSelect = document.querySelector('select:nth-of-type(3)');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    // Variables para almacenar datos
    let comunas = [];
    let personas = [];
    let editId = null;

    // Inicializar
    loadInitialData();

    // Event listeners
    regionSelect.addEventListener('change', loadComunas);
    form.addEventListener('submit', handleSubmit);

    // Funciones básicas
    function loadInitialData() {
        // Cargar regiones
        fetch('obtener_datos.php?tabla=regiones')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    regionSelect.innerHTML = '<option value="">Seleccione región</option>' + 
                        data.data.map(r => `<option value="${r.id}">${r.nombre}</option>`).join('');
                }
            });

        // Cargar profesiones
        fetch('obtener_datos.php?tabla=profesiones')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    profesionSelect.innerHTML = '<option value="">Seleccione profesión</option>' + 
                        data.data.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
                }
            });

        // Cargar comunas (todas)
        fetch('obtener_datos.php?tabla=comunas')
            .then(r => r.json())
            .then(data => data.success && (comunas = data.data));

        // Cargar personas para la tabla
        loadPersonas();
    }

    function loadComunas() {
        const regionId = regionSelect.value;
        comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
        comunaSelect.disabled = !regionId;

        if (regionId) {
            const comunasRegion = comunas.filter(c => c.region_id == regionId);
            comunaSelect.innerHTML += comunasRegion.map(c => 
                `<option value="${c.id}">${c.nombre}</option>`
            ).join('');
        }
    }

    function loadPersonas() {
        fetch('obtener_datos.php?tabla=personas')
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    personas = data.data;
                    renderPersonasTable();
                }
            });
    }

    function renderPersonasTable() {
        // Asumiendo que hay una tabla después del formulario
        const table = document.querySelector('table');
        if (!table) return;
        
        // Limpiar tabla (excepto cabecera)
        const tbody = table.querySelector('tbody') || table.createTBody();
        tbody.innerHTML = '';

        personas.forEach(p => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${p.nombre}</td>
                <td>${p.apellidos}</td>
                <td>${p.region_nombre}</td>
                <td>${p.comuna_nombre}</td>
                <td>${p.profesion_nombre}</td>
                <td><button class="edit-btn" data-id="${p.id}">Modificar</button></td>
            `;
        });

        // Agregar eventos a botones de modificar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                editPersona(this.getAttribute('data-id'));
            });
        });
    }

    function editPersona(id) {
        const persona = personas.find(p => p.id == id);
        if (!persona) return;

        nombreInput.value = persona.nombre;
        apellidosInput.value = persona.apellidos;
        regionSelect.value = persona.region_id;
        loadComunas();
        setTimeout(() => {
            comunaSelect.value = persona.comuna_id;
            profesionSelect.value = persona.profesion_id;
        }, 100);
        
        editId = id;
        submitBtn.textContent = 'Actualizar';
    }

    function handleSubmit(e) {
        e.preventDefault();

        // Validación básica
        if (!nombreInput.value || !apellidosInput.value || !regionSelect.value || 
            !comunaSelect.value || !profesionSelect.value) {
            alert('Complete todos los campos');
            return;
        }

        const formData = new FormData();
        formData.append('cmd', 'guardar_persona');
        formData.append('nombre', nombreInput.value);
        formData.append('apellidos', apellidosInput.value);
        formData.append('region_id', regionSelect.value);
        formData.append('comuna_id', comunaSelect.value);
        formData.append('profesion_id', profesionSelect.value);
        
        if (editId) formData.append('id', editId);

        fetch('command.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert(editId ? 'Actualizado correctamente' : 'Guardado correctamente');
                resetForm();
                loadPersonas();
            } else {
                alert(data.error || 'Error al guardar');
            }
        });
    }

    function resetForm() {
        form.reset();
        editId = null;
        submitBtn.textContent = 'Enviar';
        comunaSelect.disabled = true;
    }
});