$(function() {
    if (window.document.addEventListener) {
        window.document.addEventListener("keydown", pulsaTecla, false);
        window.document.addEventListener("keyup", borraTecla, false);
    } else {
        window.document.attachEvent("onkeydown", pulsaTecla);
        window.document.attachEvent("onkeyup", borraTecla);
    }
    String.prototype.miTrim = function() { return this.replace(/^\s+|\s+$/gm, ''); }
});



function alerta(mensaje, titulo, previo, ancho) {
    if (typeof(previo) == 'boolean' && previo == true) {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta(true)'/></div>";
    } else {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta()'/></div>";
    }

    if (typeof(ancho) != 'number') ancho = 300;
    if (typeof(duracion) != 'number') duracion = 0;
    $("#mensaje_div").dialog({
        title: titulo,
        autoOpen: false,
        draggable: false,
        dialogClass: "alertas no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        width: ancho
    });
    $("#mensaje_div").dialog('open');
}


function cierraAlerta(previo) {
    if (typeof(previo) == 'boolean' && previo == true) {
        window.history.back();
    }
    $("#mensaje_div").dialog("close");
}

function pinchaFila(x) {
    if (tecla != 17) {
        var numfilas = document.getElementById('listausus').rows.length;
        for (i = 0; i < numfilas; i++) {
            document.getElementById('listausus').rows[i].className = "filanoseleccionada";
        }
    }
    if (tecla == 17 && x.className == "filaseleccionada") x.className = "filanoseleccionada";
    else x.className = "filaseleccionada";
}

function pulsaTecla(evnt) {
    var ev = (evnt) ? evnt : event;
    tecla = (ev.which) ? ev.which : event.keyCode;
    return;
}

function borraTecla() {
    tecla = 0;
}

//Devuelve un array con las filas seleccionadas de una tabla
function devuelveSeleccionados(obj) {
    var listado = new Array();
    for (i = 0; i < obj.rows.length; i++) {
        if (obj.rows[i].className == "filaseleccionada") {
            listado.push(obj.rows[i]);
        }
    }
    return listado;
}

function numElemSelecc(obj) {
    num_selecc = 0;
    for (i = 0; i < obj.rows.length; i++) {
        if (obj.rows[i].className == "filaseleccionada") {
            num_selecc++;
        }
    }
    return num_selecc;
}


function retornaValRadioButton(obj) {
    for (i = 0; i < obj.length; i++) {
        if (obj[i].checked) return obj[i].value;
    }
}

function validaDNI_NIE(dni) {
    var numero, le, letra;
    var expresion_regular_dni = /^[XYZ]?\d{5,8}[A-Z]$/;

    dni = dni.toUpperCase();

    if (expresion_regular_dni.test(dni) === true) {
        numero = dni.substr(0, dni.length - 1);
        numero = numero.replace('X', 0);
        numero = numero.replace('Y', 1);
        numero = numero.replace('Z', 2);
        le = dni.substr(dni.length - 1, 1);
        numero = numero % 23;
        letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
        letra = letra.substring(numero, numero + 1);
        if (letra != le) {
            //alert('Dni erroneo, la letra del NIF no se corresponde');
            return false;
        } else {
            //alert('Dni correcto');
            return true;
        }
    } else {
        //alert('Dni erroneo, formato no válido');
        return false;
    }
}

jQuery.validator.addMethod("numero_nif", function(value, element) {
    if (value.miTrim() == '') return true;
    return /(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))/.test(value.miTrim());
});


jQuery.validator.addMethod("miFecha", function(value, element) {
    return (/^\d{2}\/\d{2}\/\d{4}$/).test(value);
});

function revisaNIF_Pasaporte(obj){
    var aux="";
    for (i=0; i<obj.value.length;i++){
        if ((obj.value.charAt(i)>="a" && obj.value.charAt(i)<="z") || (obj.value.charAt(i)>="A" && obj.value.charAt(i)<="Z") || (obj.value.charAt(i)>="0" && obj.value.charAt(i)<="9")){
            aux+=obj.value.charAt(i);
        }
    }
    obj.value=aux;
}


//Limita un grupo de checkbox a que puedan activar si hay menos de un número de ellos activo
function limitCheckboxes(selector, maxCount) {
    const checkboxes = document.querySelectorAll(selector);
  
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('click', (e) => {
        const checkedCount = document.querySelectorAll(`${selector}:checked`).length;
  
        if (checkedCount > maxCount) {
          e.preventDefault();
          checkbox.checked = false;
        }
      });
    });
  }
  
