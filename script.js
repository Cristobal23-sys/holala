document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const form = document.getElementById('personaForm');
    const nombreInput = form.querySelector('input[name="nombre"]');
    const apellidosInput = form.querySelector('input[name="apellidos"]');
    const regionSelect = form.querySelector('select[name="region"]');
    const comunaSelect = form.querySelector('select[name="comuna"]');
    const profesionSelect = form.querySelector('select[name="profesion"]');
    const idInput = form.querySelector('input[name="id"]');
    const submitBtn = form.querySelector('button[type="submit"]');
    const personasTbody = document.querySelector('#personasTable tbody');

    // Inicializar la aplicación
    initSelects();
    renderPersonas();

    // Eventos
    regionSelect.addEventListener('change', updateComunas);
    form.addEventListener('submit', handleSubmit);

    function initSelects() {
        // Llenar regiones
        regionSelect.innerHTML = '<option value="">Seleccione región</option>' +
            appData.regiones.map(r => `<option value="${r.id}">${r.nombre}</option>`).join('');
        
        // Llenar profesiones
        profesionSelect.innerHTML = '<option value="">Seleccione profesión</option>' +
            appData.profesiones.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
    }

    function updateComunas() {
        const regionId = regionSelect.value;
        comunaSelect.innerHTML = '<option value="">Seleccione comuna</option>';
        comunaSelect.disabled = !regionId;

        if (regionId) {
            const comunasFiltradas = appData.comunas.filter(c => c.region_id == regionId);
            comunaSelect.innerHTML += comunasFiltradas.map(c => 
                `<option value="${c.id}">${c.nombre}</option>`
            ).join('');
        }
    }

    function renderPersonas() {
        personasTbody.innerHTML = '';
        
        appData.personas.forEach(persona => {
            const row = personasTbody.insertRow();
            row.innerHTML = `
                <td>${persona.nombre}</td>
                <td>${persona.apellidos}</td>
                <td>${persona.region}</td>
                <td>${persona.comuna}</td>
                <td>${persona.profesion}</td>
                <td><button class="edit-btn" data-id="${persona.id}">Modificar</button></td>
            `;
        });

        // Agregar eventos a los botones de editar
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                editPersona(this.getAttribute('data-id'));
            });
        });
    }

    function editPersona(id) {
        const persona = appData.personas.find(p => p.id == id);
        if (!persona) return;

        // Buscar los IDs reales (por si hay diferencias entre nombres e IDs)
        const region = appData.regiones.find(r => r.nombre === persona.region);
        const comuna = appData.comunas.find(c => c.nombre === persona.comuna);
        const profesion = appData.profesiones.find(p => p.nombre === persona.profesion);

        if (region && comuna && profesion) {
            nombreInput.value = persona.nombre;
            apellidosInput.value = persona.apellidos;
            idInput.value = persona.id;
            regionSelect.value = region.id;
            updateComunas();
            
            setTimeout(() => {
                comunaSelect.value = comuna.id;
                profesionSelect.value = profesion.id;
                submitBtn.textContent = 'Actualizar';
            }, 100);
        }
    }

    function handleSubmit(e) {
        e.preventDefault();

        // Validación básica
        if (!nombreInput.value || !apellidosInput.value || !regionSelect.value || 
            !comunaSelect.value || !profesionSelect.value) {
            alert('Por favor complete todos los campos');
            return;
        }

        // Aquí iría el código para enviar los datos a command.php
        // Similar al ejemplo anterior, usando fetch()
        
        alert(idInput.value ? 'Actualizando...' : 'Guardando...');
        // Simulación de guardado
        setTimeout(() => {
            alert('Operación completada');
            form.reset();
            idInput.value = '';
            submitBtn.textContent = 'Enviar';
            comunaSelect.disabled = true;
            // Recargar datos (en realidad habría que hacer nueva consulta a la BD)
        }, 500);
    }
});