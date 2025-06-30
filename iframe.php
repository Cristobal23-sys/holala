<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Notas</title>
    <style>
        /* styles.css */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            padding: 20px;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 24px;
        }

        .content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .notes-section h2,
        .preview-section h2 {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px;
        }

        #notasTextarea {
            width: 100%;
            height: 300px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            font-family: inherit;
            resize: vertical;
            margin-bottom: 15px;
        }

        #notasTextarea:focus {
            outline: none;
            border-color: #007bff;
        }

        #mostrarNotasBtn {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }

        #mostrarNotasBtn:hover {
            background-color: #0056b3;
        }

        #notasIframe {
            width: 100%;
            height: 350px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        @media (max-width: 768px) {
            .content {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Sistema de Notas</h1>
        
        <div class="content">
            <div class="notes-section">
                <h2>Escribe tus notas</h2>
                <textarea 
                    id="notasTextarea" 
                    placeholder="Escribe aquí tus notas..."
                ></textarea>
                <button id="mostrarNotasBtn">Mostrar Notas</button>
            </div>
            
            <div class="preview-section">
                <h2>Vista previa</h2>
                <iframe 
                    id="notasIframe" 
                    src="data:text/html;charset=utf-8,<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Notas</title><style>body{font-family:Arial,sans-serif;padding:20px;background:#f9f9f9;margin:0;color:#333;}.notas-container{background:white;padding:20px;border-radius:4px;border:1px solid #ddd;}.empty-state{text-align:center;color:#666;padding:40px 20px;}.nota{margin-bottom:15px;padding:10px;background:#f8f9fa;border-left:3px solid #007bff;border-radius:3px;}</style></head><body><div class='notas-container'><div id='contenidoNotas'><div class='empty-state'><p><strong>Tus notas aparecerán aquí</strong></p><p>Escribe algo y haz clic en 'Mostrar Notas'</p></div></div></div></body></html>"
                ></iframe>
            </div>
        </div>
    </div>

    <script>
        // script.js
        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('notasTextarea');
            const boton = document.getElementById('mostrarNotasBtn');
            const iframe = document.getElementById('notasIframe');

            function mostrarNotas() {
                const contenidoNotas = textarea.value.trim();
                
                if (contenidoNotas === '') {
                    alert('Por favor, escribe algo antes de mostrar las notas.');
                    return;
                }

                try {
                    // Acceder al documento del iframe usando contentWindow
                    const iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
                    const contenidoDiv = iframeDoc.getElementById('contenidoNotas');
                    
                    if (contenidoDiv) {
                        // Dividir el texto en párrafos
                        const lineas = contenidoNotas.split('\n').filter(linea => linea.trim() !== '');
                        let htmlNotas = '';
                        
                        lineas.forEach(linea => {
                            htmlNotas += `<div class="nota"><p style="margin:0;">${escapeHtml(linea)}</p></div>`;
                        });
                        
                        // Agregar fecha actual
                        const fecha = new Date().toLocaleString('es-ES');
                        
                        contenidoDiv.innerHTML = `
                            <h3 style="color:#007bff;margin-bottom:15px;">Mis Notas</h3>
                            <small style="color:#666;">Actualizado: ${fecha}</small>
                            <div style="margin-top:15px;">${htmlNotas}</div>
                        `;
                    }
                } catch (error) {
                    alert('Error al mostrar las notas.');
                }
            }

            // Función para prevenir XSS
            function escapeHtml(texto) {
                const div = document.createElement('div');
                div.textContent = texto;
                return div.innerHTML;
            }

            // Event listener del botón
            boton.addEventListener('click', mostrarNotas);
        });
    </script>
</body>
</html>