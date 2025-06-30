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

    // Variables de estado
    let isUpdating = false;

    // Cargar datos iniciales
    loadRegiones();
    loadProfesiones();
    loadPersonas();

    // Evento change para región
    regionSelect.addEventListener('change', function() {
        const regionId = this.value;
        if (regionId) {
            loadComunas(regionId);
            comunaSelect.disabled = false;
        } else {
            comunaSelect.disabled = true;
            comunaSelect.innerHTML = '<option value="">Seleccione una comuna</option>';
        }
    });

    // Evento submit del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            if (isUpdating) {
                updatePersona();
            } else {
                insertPersona();
            }
        }
    });

    // Función para cargar regiones
    function loadRegiones() {
        fetch('obtener_datos.php?tabla=regiones')
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                
                regionSelect.innerHTML = '<option value="">Seleccione una región</option>';
                data.data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.id;
                    option.textContent = region.nombre;
                    regionSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar regiones:', error));
    }

    // Función para cargar comunas
    function loadComunas(regionId) {
        fetch(`obtener_datos.php?tabla=comunas&region_id=${regionId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                
                comunaSelect.innerHTML = '<option value="">Seleccione una comuna</option>';
                data.data.forEach(comuna => {
                    const option = document.createElement('option');
                    option.value = comuna.id;
                    option.textContent = comuna.nombre;
                    comunaSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar comunas:', error));
    }

    // Función para cargar profesiones
    function loadProfesiones() {
        fetch('obtener_datos.php?tabla=profesiones')
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                
                profesionSelect.innerHTML = '<option value="">Seleccione una profesión</option>';
                data.data.forEach(profesion => {
                    const option = document.createElement('option');
                    option.value = profesion.id;
                    option.textContent = profesion.nombre;
                    profesionSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error al cargar profesiones:', error));
    }

    // Función para cargar personas en la grilla
    function loadPersonas() {
        fetch('obtener_datos.php?tabla=personas')
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                
                personasGrid.innerHTML = '';
                data.data.forEach(persona => {
                    const row = document.createElement('tr');
                    
                    row.innerHTML = `
                        <td>${persona.nombre}</td>
                        <td>${persona.apellidos}</td>
                        <td>${persona.region}</td>
                        <td>${persona.comuna}</td>
                        <td>${persona.profesion}</td>
                        <td><button class="action-btn" data-id="${persona.id}">Modificar</button></td>
                    `;
                    
                    personasGrid.appendChild(row);
                });
                
                // Agregar eventos a los botones de modificar
                document.querySelectorAll('.action-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const personaId = this.getAttribute('data-id');
                        editPersona(personaId);
                    });
                });
            })
            .catch(error => console.error('Error al cargar personas:', error));
    }

    // Función para editar una persona
    function editPersona(personaId) {
        fetch(`obtener_datos.php?tabla=personas&id=${personaId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.error);
                if (!data.data.length) throw new Error("Persona no encontrada");
                
                const persona = data.data[0];
                
                // Llenar el formulario con los datos
                nombreInput.value = persona.nombre;
                apellidosInput.value = persona.apellidos;
                personaIdInput.value = persona.id;
                
                // Seleccionar la región y cargar sus comunas
                regionSelect.value = persona.region_id;
                loadComunas(persona.region_id).then(() => {
                    comunaSelect.value = persona.comuna_id;
                });
                
                // Seleccionar la profesión
                profesionSelect.value = persona.profesion_id;
                
                // Cambiar el estado a actualización
                isUpdating = true;
                submitBtn.textContent = 'Actualizar';
                
                // Desplazarse al formulario
                form.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error al cargar persona:', error);
                alert(error.message);
            });
    }

    // Función para insertar una nueva persona
    function insertPersona() {
        const formData = new URLSearchParams();
        formData.append('cmd', 'guardar_persona');
        formData.append('nombre', nombreInput.value);
        formData.append('apellidos', apellidosInput.value);
        formData.append('region_id', regionSelect.value);
        formData.append('comuna_id', comunaSelect.value);
        formData.append('profesion_id', profesionSelect.value);
        
        fetch('command.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.error);
            
            alert('Persona registrada exitosamente');
            resetForm();
            loadPersonas();
        })
        .catch(error => {
            console.error('Error al registrar persona:', error);
            alert(error.message);
        });
    }

    // Función para actualizar una persona
    function updatePersona() {
        const formData = new URLSearchParams();
        formData.append('cmd', 'guardar_persona');
        formData.append('id', personaIdInput.value);
        formData.append('nombre', nombreInput.value);
        formData.append('apellidos', apellidosInput.value);
        formData.append('region_id', regionSelect.value);
        formData.append('comuna_id', comunaSelect.value);
        formData.append('profesion_id', profesionSelect.value);
        
        fetch('command.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) throw new Error(data.error);
            
            alert('Persona actualizada exitosamente');
            resetForm();
            loadPersonas();
        })
        .catch(error => {
            console.error('Error al actualizar persona:', error);
            alert(error.message);
        });
    }

    // Función para validar el formulario
    function validateForm() {
        // Validaciones básicas
        if (!nombreInput.value.trim()) {
            alert('Por favor ingrese un nombre');
            return false;
        }
        
        if (!apellidosInput.value.trim()) {
            alert('Por favor ingrese apellidos');
            return false;
        }
        
        if (!regionSelect.value) {
            alert('Por favor seleccione una región');
            return false;
        }
        
        if (!comunaSelect.value) {
            alert('Por favor seleccione una comuna');
            return false;
        }
        
        if (!profesionSelect.value) {
            alert('Por favor seleccione una profesión');
            return false;
        }
        
        return true;
    }

    // Función para resetear el formulario
    function resetForm() {
        form.reset();
        personaIdInput.value = '';
        comunaSelect.disabled = true;
        comunaSelect.innerHTML = '<option value="">Seleccione una comuna</option>';
        isUpdating = false;
        submitBtn.textContent = 'Enviar';
    }
});