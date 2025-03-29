let backup_nombre = "";
let anno_ini_curso="", curso="";
let id_nie="", nombre="", nif_nie="";
let ciclos_basico={};
let ciclos_medio={};
let ciclos_superior={};


$(document).ready(function() {
    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            nif_nie=resp["id_nif"];
            nombre=resp["nombre"];
            apellidos=resp["apellidos"];
            document.getElementById("nif_nie").value = nif_nie;
            document.getElementById("nombre").value = nombre + " " + apellidos;
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            curso = anno_ini_curso + "-" + (anno_ini_curso + 1);

            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }

        }
    }, "json"));
    dat2 = dat1.then(() => {
        let ciclos_basico={};
        let ciclos_medio={};
        let ciclos_superior={};
        $.post("php/ciclos.php", { }, (resp) => {
            if (resp.error == "ok") {
                let cont_bas=0;
                let cont_med=0;
                let cont_sup=0;
                for (i=0; i<resp.datos.length; i++){
                    if (resp.datos[i].grado=="BÁSICO"){
                        ciclos_basico[cont_bas]={};
                        ciclos_basico[cont_bas]["ciclo"]=resp["datos"][i]["denominacion"];
                        ciclos_basico[cont_bas]["departamento"]=resp["datos"][i]["departamento"];
                        cont_bas++;
                    }
                    else if (resp.datos[i].grado=="MEDIO"){
                        ciclos_medio[cont_med]={};
                        ciclos_medio[cont_med]["ciclo"]=resp["datos"][i]["denominacion"];
                        ciclos_medio[cont_med]["departamento"]=resp["datos"][i]["departamento"];
                        cont_med++;
                    }
                    else if (resp.datos[i].grado=="SUPERIOR"){
                        ciclos_superior[cont_sup]={};
                        ciclos_superior[cont_sup]["ciclo"]=resp["datos"][i]["denominacion"];
                        ciclos_superior[cont_sup]["departamento"]=resp["datos"][i]["departamento"];
                        cont_sup++;
                    }
                }
                alert(ciclos_basico.length+"  "+ciclos_medio.length+"   "+ciclos_superior.length)
            } else {
                alerta("Los datos de Ciclos Formativos no se han podido recuperar. El formulario no se podrá cumplimentar.","ERROR RECUPERACIÓN DATOS");
            }
        }, "json");
    });
    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
});


function confirmar() {
    document.getElementById('mensaje_div').innerHTML = "El proceso de registro será cancelado y se borrarán los datos del formulario.";
    $("#mensaje_div").dialog({
        title: "CANCELACIÓN DE PROCESO",
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    $(this).dialog("close"); 
                    window.history.back();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                    return false;
                }
            }
        ]
    });

    $("#mensaje_div").dialog('open');
}

function seleccionListaDon() {
    if (document.getElementById("lista_don").value != "") {
        document.getElementById("nombre").readOnly = false;
        document.getElementById("nombre").value = backup_nombre;
    } else {
        document.getElementById("nombre").readOnly = true;
        backup_nombre = document.getElementById("nombre").value;
        document.getElementById("nombre").value = "Seleccione D. o Dña. en el desplegable anterior.";
    }
}

function cambiaTipoForm(v){
    if (v=="basico") lista_cic=ciclos_basico;
    else if(v=="medio")lista_cic=ciclos_medio;
    else if(v=="superior") lista_cic=ciclos_superior;
    
    for (i=0; i<lista_cic.length;i++){
        let option = document.createElement("option");
        option.value = lista_cic[i];
        option.textContent = lista_cic[i];
        document.getElementById("ciclos_f").appendChild(option);
    }

}


function anadeDoc(e) {
    e.preventDefault();
    creaInputs();
    $("#anade_documento").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "AÑADIR DOCUMENTO ADJUNTO",
        width: 700,
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked").length == 0 || 
                        document.getElementById("archivo_con").value.trim().length == 0){
                            alerta("Debe seleccionar un tipo de documento y un archivo.", "FALTAN DATOS");
                            return;
                    }
                    else if(document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value=="Otro" &&
                            document.getElementById("den_otro_con").value.trim().length == 0){
                                alerta("Debe especificar qué tipo de documento va a adjuntar.", "FALTAN DATOS");
                                return;
                    }
                    actualizaTablaListaDocs();
                    if (document.querySelectorAll("input[name=tipo_con]:checked")[0].value.indexOf("Documento de identificación")>-1)subidoDocIdent=true;
                    document.getElementById("form_anade_documento").reset();
                    $('#div_den_otro_con').hide();
                    $("#anade_documento").dialog("close");
                    $("#anade_documento").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    document.getElementById("form_anade_documento").reset();
                    $('#div_den_otro_con').hide();
                    selUltimoFile().remove();
                    selUltimoHidden().remove();
                    $("#anade_documento").dialog("close");
                    $("#anade_documento").dialog("destroy");
                }
            }
        ]
    });
}


function creaInputs() {
    divArray = document.getElementById("array_input_type_file");
    tipoHidden = document.createElement("input");
    tipoHidden.type = "hidden";
    tipoHidden.name = "desc[]";
    tipoFile = document.createElement("input");
    tipoFile.type = "file";
    tipoFile.name = "docs[]";
    tipoFile.multiple = false;
    divArray.appendChild(tipoHidden);
    divArray.appendChild(tipoFile);
    tipoFile.accept="application/pdf"
    tipoFile.addEventListener("change", function(event) {
        if (this.files.length > 0) {
            for(i=0;i<this.files.length;i++){
                var extension = this.files[i].name.split('.').pop().toLowerCase();
                // Verificar si la extensión del archivo es pdf
                if (extension !== "pdf") {
                    alerta(mensaje_alerta,"ERROR TIPO ARCHIVO");
                    return;
                }
            }
        }
    document.getElementById('archivo_con').value = this.files[0].name;
    });
}

function selUltimoFile() {
    _a = document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
    return _a[_a.length - 1];
}

function selUltimoHidden() {
    _a = document.getElementById("array_input_type_file").querySelectorAll("input[type=hidden]");
    return _a[_a.length - 1];
}

function actualizaTablaListaDocs() {
    _t = document.getElementById("tab_lista_docs");
    
    _tipoSel=document.querySelectorAll("#anade_documento input[name=tipo_con]:checked");
    _d = document.getElementById("array_input_type_file").querySelectorAll("input[type=hidden]");
    if (_tipoSel[0].value=="Otro"){
        //_arch = selUltimoFile().files[0].name;
        _d[_d.length - 1].value = document.getElementById("den_otro_con").value;
    }
    else {
        _d[_d.length - 1].value = _tipoSel[0].value;
    }
        
    if (_t.rows[0].cells.length == 1) {
        _t.deleteRow(0);
    }
    var nuevaFila = _t.insertRow();

    // Insertar una celda en la nueva fila (primera columna)
    var celda1 = nuevaFila.insertCell();
    celda1.textContent =  _d[_d.length - 1].value;
    celda1.style.width = "50%";

    // Insertar una celda en la nueva fila (segunda columna)
    var celda2 = nuevaFila.insertCell();
    celda2.textContent = selUltimoFile().files[0].name;
    if(_tipoSel[0].value=="Documento de identificación (DNI/NIE)") celda2.textContent+=", "+selUltimoFile().files[1].name;
    celda2.style.width = "45%";
    var celda3 = nuevaFila.insertCell();
    celda3.innerHTML = "<a href='#' style='color:brown;font-weight:bold' onclick='borraFila(this,event)' title='Elimina el documento'>X</a>";
    celda3.style.width = "5%";
    celda3.style.textAlign = "center";

}


function borraFila(obj, e) {
    e.preventDefault();
    _t = document.getElementById("tab_lista_docs");
    num_fila = obj.parentNode.parentNode.rowIndex;
    if(obj.parentNode.parentNode.cells[0].innerText.indexOf("Documento de identificación")>-1){
        subidoDocIdent=false;
        if (obj.parentNode.parentNode.cells[0].innerText.indexOf("(Pasaporte)")>-1){
            formData.delete('pasaporte');
        }
        else{
            formData.delete('dni_anverso');
            formData.delete('dni_reverso');
        }
    } 
    if (_t.rows.length == 1) {
        _t.innerHTML = "<tr><td style='text-align:center'>LISTA DE DOCUMENTOS VACÍA</td></tr>";
    } else {
        _t.deleteRow(num_fila);
    }

    inputsHidden = document.getElementById("array_input_type_file").querySelectorAll('input[type="hidden"]');
    inputsHidden[num_fila].remove();
    inputsFiles = document.getElementById("array_input_type_file").querySelectorAll('input[type="file"]');
    inputsFiles[num_fila].remove();
}


function generaImpreso() {
    document.getElementById("cargando").style.display = 'inline-block';
    let pet = $.ajax({
        url: "php/generapdf.php",
        type: "POST",
        data: $("#exenc").serialize()
    });
    $.when(pet).done(function(resp) {
        document.getElementById("cargando").style.display = 'none';
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp == "registro_erroneo") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de su solicitud.<br>Por favor, vuelva a intentarlo más tarde.<br>";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error de servidor");
        } else if (resp.indexOf("envio2_fallido") != -1) {
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.<br>Por favor, revise si el email introducido es correcto y vuelva a intentarlo.<br>";
            mensaje += "Si el email es correcto, puede deberse a un fallo en el servidor. En este caso, vuelva a intentar más tarde el proceso.";
            mensaje += "Si ve que el problema persiste, puede cumplimentar el formulario y presentarlo en ventanilla accediendo por Secretaría->Impresos de Secretaría->Para presentar en Ventanilla.";
            alerta(mensaje, "Error en envío");
        } else if (resp.indexOf("envio1_fallido") != -1) {
            num_reg = resp.slice(14);
            mensaje = "No se ha podido enviar a su correo el impreso y número de registro.";
            mensaje += "Aún así, parece que su impreso se ha sido enviado correctamente a la Secretaría del Cento, con el nº de registro:<br><b>";
            mensaje += num_reg + "</b><br>";
            mensaje += "Puede ponerse en contacto con el personal de secretaría para verificarlo, en el teléfono 925 22 34 00 EXTENSIONES 272 y 236";
        } else if (resp == "no_file") {
            alerta("Ha habido un error y no se ha podido generar el fichero con el formulario registrado.", "Error en servidor");
        } else if (resp.indexOf("envio_ok") != -1) {
            num_reg = resp.slice(8);
            mensaje = "Proceso finalizado correctamente. Tome nota de su nº de registro:<br><br>";
            mensaje += "<center><b>" + num_reg + "</b></center><br><br>";
            mensaje += "RECUERDE QUE:<br><br>";
            mensaje += "-DEBE ENVIAR EL Nº DE REGISTRO POR <strong>PAPAS 2.0</strong>, AL GRUPO <strong>'Coordinadores de mi centro'</strong> (PUEDE SELECCIONAR Y COPIAR CON EL RATÓN EL Nº DE REGISTRO, Y PEGARLO EN EL MENSAJE DE PAPAS). DE LO CONTRARIO, EL FORMULARIO NO SE CONSIDERARÁ FIRMADO Y NO SERÁ VÁLIDO.<br>"
            mensaje += "-SI VE QUE NO RECIBE EL CORREO ELECTRÓNICO, DEBE REVISAR LA CARPETA SPAM O CORREO NO DESEADO.<br><br>";
            mensaje += "-SI EN EL PLAZO DE 24/48 HORAS EL PERSONAL DE SECRETARÍA NO HA RECIBIDO SU SOLICITUD, DEBE PONERSE EN CONTACTO CON ELLOS EN EL TELÉFONO 925 22 34 00 EXTENSIONES 272 Y 236";
            alerta(mensaje, "Registro correcto");
        }
        document.getElementById('exenc').reset();
        document.getElementById("email2").value = "";
        document.getElementById("email3").value = "";
    });
}





function iniciaGeneraPdf() {
    
}
// JavaScript Document