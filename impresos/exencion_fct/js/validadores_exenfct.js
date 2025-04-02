$("#exenc").validate({
    rules: {
        lista_don: { required: true },
        nombre: { required: true },
        nif_nie: { required: true },
        formacion: { required: true },
        ciclos_f: { required: true },
        firma: { required: true },
        validar_tabla: { tabla: true } // Aplicamos la regla personalizada
    },
    messages: {
        lista_don: { required: "Seleccione" },
        nombre: { required: "Complete el campo" },
        nif_nie: { required: "Complete el campo" },
        formacion: { required: "Seleccione" },
        ciclos_f: { required: "Falta selección de ciclo" },
        firma: { required: "No se ha firmado la solicitud" },
        tab_lista_docs: { tabla: "No se ha adjuntado ningún documento." }
    },
    errorPlacement: function(error, element) {
        error.insertBefore(element);
    }
});

// 🔹 REGLA PERSONALIZADA PARA VALIDAR LA TABLA
$.validator.addMethod("tabla", function(value, element) {
    var tabla = $("#tab_lista_docs");
    var filas = tabla.find("tr");
    alert("vvv")
    // Si solo hay una fila y su única celda contiene el texto de vacío
    if (filas.length === 1) {
        var celdaTexto = filas.first().find("td").text().trim();
        alert(celdaTexto)
        return celdaTexto !== "LISTA DE DOCUMENTOS VACÍA"; // Debe ser falso si está vacía
    }

    return true; // Si hay más de una fila, está bien
}, "No se ha adjuntado ningún documento.");


 









