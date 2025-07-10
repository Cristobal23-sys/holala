let BD = new BASE();
BD.commandFile = "form/training/database/ejercicio2/command.php";

// Llenar el combo de regiones
let resultado = BD.send("cmd=obtieneProducto");

let comboRegion = document.getElementById("comboRegion");
comboRegion.innerText = null;

// Opción por defecto
let defaultRegion = document.createElement("option");
defaultRegion.value = "";
defaultRegion.text = "-- Seleccione una región --";
comboRegion.add(defaultRegion);

// Llenar regiones
for (let i = 0; i < resultado.numrows; i++) {
  let option = document.createElement("option");
  option.value = BD.getField(resultado, "idregion", i);
  option.text = BD.getField(resultado, "region", i);
  comboRegion.add(option);
}

// Evento: al seleccionar una región, cargar comunas
comboRegion.addEventListener("change", function () {
  let idregion = this.value;
  let comboComuna = document.getElementById("comboComuna");
  comboComuna.innerText = null;

  // Agrega opción por defecto
  let defaultComuna = document.createElement("option");
  defaultComuna.value = "";
  defaultComuna.text = "-- Seleccione una comuna --";
  comboComuna.add(defaultComuna);

  if (idregion !== "") {
    let resultadoComunas = BD.send("cmd=obtieneComunas&idregion=" + idregion);

    for (let i = 0; i < resultadoComunas.numrows; i++) {
      let option = document.createElement("option");
      option.value = BD.getField(resultadoComunas, "idcomuna", i);
      option.text = BD.getField(resultadoComunas, "comuna", i);
      comboComuna.add(option);
    }
  }
});