$("#exenc").validate({
    rules: {
        lista_don: { required: true },
        nombre: { required: true },
        apellidos: { required: true },
        nif_nie: { required: true },
        formacion: { required: true },
        ciclos_f: { required: true },
        curso_ciclo: {required: true},
        firma: { required: true }
    },
    messages: {
        lista_don: { required: "Seleccione" },
        nombre: { required: "Complete el campo" },
        apellidos: { required: "Complete el campo" },
        nif_nie: { required: "Complete el campo" },
        formacion: { required: "Seleccione" },
        ciclos_f: { required: "Falta selección de ciclo" },
        curso_ciclo: { required: "Falta" },
        firma: { required: "No se ha firmado la solicitud" }
    },
    errorPlacement: function(error, element) {
        error.insertBefore(element);
    }
});




 









