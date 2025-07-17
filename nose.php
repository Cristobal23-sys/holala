<?php
while (!file_exists($POSICION."conexion.php")) {
    $POSICION .= "../";
}
require_once($POSICION."conexion.php");

$titulo_formulario = "Mantenedor para Administrador";
require $POSICION."componente/permiso/class.php";
require_once $POSICION."formHeader.php";

// Solo incluir los botones Eliminar y Modificar
$Botonera = new Permiso("admin/mantenedor/", "", "Modificar()", "Eliminar()", "", "", "");
?>
<!-- Importaciones -->
<script type="text/javascript" src="<?=$POSICION?>componente/database/class.php"></script>
<script type="text/javascript" src="<?=$POSICION?>componente/messagebox/class.php?v=1"></script>

<!-- Imprimir la botonera -->
<div id="botoneraContainer">
    <?=$Botonera->imprimirBotones();?>
</div>

<!-- Contenedor oculto para Guardar y Volver -->
<div id="extraBotones" style="display: none; margin-top: 10px;">
    <button id="guardar" onclick="Guardar()">Guardar</button>
    <button id="volver" onclick="Volver()">Volver</button>
</div>

<script>
var MSGBOX = {
    ventana: typeof IU_MSGBOX,
    initialize: function(){
        this.ventana = new IU_MSGBOX ();
    },
    Abrir: function(conf){
        this.ventana.titulo = conf.titulo || 'Aviso';
        var texto = conf.texto || '';
        this.ventana.texto = '<div style="text-align:center">' + texto + '</div>';
        this.ventana.botonera = conf.botonera || 1;
        this.ventana.conCerrar = conf.conCerrar || this.ventana.conCerrar;
        this.ventana.fnretorno = conf.fnretorno || 'MSGBOX.fnretorno';
        this.ventana.url = "<?=$POSICION?>";
        this.ventana.Messagebox();
    },
    fnretorno : function(resp){
        MSGBOX.ventana.HideInfo();
    }
};
MSGBOX.initialize();

// Funciones con lógica personalizada
function Eliminar(){
    MSGBOX.Abrir({
        texto: "Presionó el botón Eliminar"
    });
}

function Modificar(){
    document.querySelector('button[onclick="Eliminar()"]').style.display = 'none';
    document.querySelector('button[onclick="Modificar()"]').style.display = 'none';
    document.getElementById('extraBotones').style.display = 'block';
}

function Guardar(){
    MSGBOX.Abrir({
        texto: "Los cambios se guardaron exitosamente"
    });
    restaurarBotones();
}

function Volver(){
    MSGBOX.Abrir({
        texto: "Presionó el botón Volver"
    });
    restaurarBotones();
}

function restaurarBotones(){
    document.querySelector('button[onclick="Eliminar()"]').style.display = 'inline-block';
    document.querySelector('button[onclick="Modificar()"]').style.display = 'inline-block';
    document.getElementById('extraBotones').style.display = 'none';
}

// Si los botones están ocultos por defecto, mostrar al cargar
window.onload = function() {
    const btns = ['Eliminar()', 'Modificar()'];
    btns.forEach(fn => {
        let btn = document.querySelector('button[onclick="' + fn + '"]');
        if (btn) btn.hidden = false;
    });
};
</script>
