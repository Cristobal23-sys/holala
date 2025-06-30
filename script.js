function initForm() {
    const form = document.getElementById('personaForm');
    const nombre = form.nombre;
    const apellidos = form.apellidos;
    const region = form.region;
    const comuna = form.comuna;
    const profesion = form.profesion;
    const id = form.id;
    const button = form.querySelector('button');
    const tbody = document.querySelector('#personasTable tbody');

    region.innerHTML = '<option value="">Seleccione región</option>' +
        appData.regiones.map(r => `<option value="${r.id}">${r.nombre}</option>`).join('');
    profesion.innerHTML = '<option value="">Seleccione profesión</option>' +
        appData.profesiones.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');

    region.addEventListener('change', () => {
        const regionId = region.value;
        comuna.innerHTML = '<option value="">Seleccione comuna</option>';
        comuna.disabled = !regionId;
        if (regionId) {
            const comunas = appData.comunas.filter(c => c.region_id == regionId);
            comuna.innerHTML += comunas.map(c => `<option value="${c.id}">${c.nombre}</option>`).join('');
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        if (!nombre.value || !apellidos.value || !region.value || !comuna.value || !profesion.value) {
            alert("Todos los campos son obligatorios");
            return;
        }

        fetch('command.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                cmd: 'guardar_persona',
                id: id.value,
                nombre: nombre.value,
                apellidos: apellidos.value,
                region_id: region.value,
                comuna_id: comuna.value,
                profesion_id: profesion.value
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert("Guardado exitoso");
                loadData();
                form.reset();
                id.value = '';
                comuna.disabled = true;
                button.textContent = 'Enviar';
            } else {
                alert("Error: " + res.error);
            }
        });
    });

    function loadData() {
        fetch('obtener_datos.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appData = data;
                    renderTable();
                }
            });
    }

    function renderTable() {
        tbody.innerHTML = '';
        appData.personas.forEach(p => {
            const row = tbody.insertRow();
            row.innerHTML = `
                <td>${p.nombre}</td>
                <td>${p.apellidos}</td>
                <td>${p.region_nombre}</td>
                <td>${p.comuna_nombre}</td>
                <td>${p.profesion_nombre}</td>
                <td><button data-id="${p.id}" class="edit-btn">Modificar</button></td>
            `;
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const persona = appData.personas.find(p => p.id == this.dataset.id);
                if (!persona) return;

                nombre.value = persona.nombre;
                apellidos.value = persona.apellidos;
                id.value = persona.id;
                region.value = persona.region_id;
                region.dispatchEvent(new Event('change'));

                setTimeout(() => {
                    comuna.value = persona.comuna_id;
                    profesion.value = persona.profesion_id;
                    button.textContent = 'Actualizar';
                }, 100);
            });
        });
    }

    renderTable();
}
