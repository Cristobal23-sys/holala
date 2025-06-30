<!DOCTYPE html>
<html>
<head>
    <title>Notas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Mis Notas</h1>
    <textarea id="notasInput" placeholder="Escribe tus notas aquí..."></textarea>
    <button onclick="mostrarNotas()">Mostrar Notas</button>
    <iframe src="notas.html" id="visorNotas"></iframe>
    <script src="script.js"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>Vista de Notas</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="contenidoNotas"></div>
</body>
</html>

body {
    font-family: Arial, sans-serif;
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

textarea {
    width: 100%;
    height: 100px;
    margin-bottom: 10px;
}

button {
    padding: 8px 15px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

iframe {
    width: 100%;
    height: 300px;
    border: 1px solid #ddd;
    margin-top: 20px;
}

function mostrarNotas() {
    const texto = document.getElementById('notasInput').value;
    const iframe = document.getElementById('visorNotas');
    iframe.contentDocument.getElementById('contenidoNotas').innerHTML = `<p>${texto}</p>`;
}