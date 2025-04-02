 $("#exenc").validate({
    rules: {
        lista_don: {
            required: true
        },
        nombre: {
            required: true
        },
        nif_nie:{
            required: true
        },
        formacion:{
            required: true
        },
        ciclos_f:{
            required: true
        },
        firma: {
            required: true
        },
        tab_lista_docs:{
            tabla:true
        }
    },
    messages: {
        lista_don: {
            required: "Falta"
        },
        nombre: {
            required: "Complete el campo"
        },
        nif_nie:{
            required: "Complete el campo"
        },
        formacion:{
            required: "Falta selección"
        },
        ciclos_f:{
            required: "Falta selección de ciclo"
        },
        firma: {
            required: "No se ha firmado la solicitud"
        },
        tab_lista_docs:{
            tabla: "No se ha adjuntado ningún documento."
        }
    },
    errorPlacement: function(error, element) {
        //$(element).prev($('.errorTxt')).html(error);
        
        if (element.attr("name") === "tab_lista_docs") error.insertBefore(element.prev().prev());
        else error.insertBefore(element);
    }
});

// Definimos una regla personalizada para validar la tabla
$.validator.addMethod("tabla", function(value, element) {
    // Comprobamos si la tabla tiene una fila con una celda que contenga "LISTA DE DOCUMENTOS VACÍA"
    var hasEmptyRow = $("#tab_lista_docs tr").length === 1 && 
                      $("#tab_lista_docs tr td").text().trim() === "LISTA DE DOCUMENTOS VACÍA";
    return !hasEmptyRow;  // Retorna `true` si NO está vacía, `false` si tiene la fila con ese texto
}, "No se ha adjuntado ningún documento.");

 









