<?php
while (!file_exists($POSICION."conexion.php")) {
    $POSICION .= "../";
}
require_once($POSICION."conexion.php");

$titulo_formulario = "Mantenedor de Administrador";
require $POSICION."componente/permiso/class.php";
require_once $POSICION."formHeader.php";

// Solo Modificar y Eliminar
$Botonera = new Permiso("admin/mantenedor/", "", "Modificar()", "Eliminar()", "", "", "");
?>
<!-- Importaciones -->
<script type="text/javascript" src="<?=$POSICION?>componente/database/class.php"></script>
<script type="text/javascript" src="<?=$POSICION?>componente/messagebox/class.php?v=1"></script>

<!-- Botones del componente Permiso -->
<div id="botoneraContainer">
    <?=$Botonera->imprimirBotones();?>
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

    // Al cargar, ocultar los botones que no se deben mostrar (Guardar y Volver)
    window.onload = function() {
        const ocultarIds = ['btnGuardar', 'btnVolver'];
        ocultarIds.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.hidden = true;
            }
        });
    }

    function Modificar() {
        document.getElementById('btnModificar').hidden = true;
        document.getElementById('btnEliminar').hidden = true;
        document.getElementById('btnGuardar').hidden = false;
        document.getElementById('btnVolver').hidden = false;
    }

    function Eliminar() {
        MSGBOX.Abrir({
            texto: "Presionó el botón Eliminar"
        });
    }

    function Guardar() {
        MSGBOX.Abrir({
            texto: "Los cambios se guardaron exitosamente"
        });
        restaurarBotones();
    }

    function Volver() {
        MSGBOX.Abrir({
            texto: "Presionó el botón Volver"
        });
        restaurarBotones();
    }

    function restaurarBotones() {
        document.getElementById('btnModificar').hidden = false;
        document.getElementById('btnEliminar').hidden = false;
        document.getElementById('btnGuardar').hidden = true;
        document.getElementById('btnVolver').hidden = true;
    }
</script>
