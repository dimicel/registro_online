let backup_nombre = "";
let anno_ini_curso="", curso="";
let id_nie="", nombre="", apellidos="", nif_nie="";
let ciclos_basico={};
let ciclos_medio={};
let ciclos_superior={};

var formData = new FormData();

$(document).ready(function() {
    document.body.style.overflowY = "scroll";
    document.getElementById("cargando").style.display = '';
    

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            nif_nie=resp["id_nif"];
            nombre=resp["nombre"];
            apellidos=resp["apellidos"];
            document.getElementById("id_nie").value = id_nie;
            document.getElementById("nif_nie").value = nif_nie;
            document.getElementById("nombre").value = nombre;
            document.getElementById("apellidos").value = apellidos;
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            document.getElementById("curso").value=curso;

            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }

        }
    }, "json"));
    dat2 = dat1.then(() => {
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
                        ciclos_basico[cont_bas]["cursos"]=resp["datos"][i]["cursos"];
                        cont_bas++;
                    }
                    else if (resp.datos[i].grado=="MEDIO"){
                        ciclos_medio[cont_med]={};
                        ciclos_medio[cont_med]["ciclo"]=resp["datos"][i]["denominacion"];
                        ciclos_medio[cont_med]["departamento"]=resp["datos"][i]["departamento"];
                        ciclos_medio[cont_med]["cursos"]=resp["datos"][i]["cursos"];
                        cont_med++;
                    }
                    else if (resp.datos[i].grado=="SUPERIOR"){
                        ciclos_superior[cont_sup]={};
                        ciclos_superior[cont_sup]["ciclo"]=resp["datos"][i]["denominacion"];
                        ciclos_superior[cont_sup]["departamento"]=resp["datos"][i]["departamento"];
                        ciclos_superior[cont_sup]["cursos"]=resp["datos"][i]["cursos"];
                        cont_sup++;
                    }
                }
            } else {
                alerta("Los datos de Ciclos Formativos no se han podido recuperar. El formulario no se podrá cumplimentar.","ERROR RECUPERACIÓN DATOS");
            }
            document.getElementById("cargando").style.display = 'none';
        }, "json");
    });
    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
});


function confirmar() {
confirmarAccion("El proceso de registro será cancelado y se borrarán los datos del formulario.", "CANCELACIÓN DE PROCESO")
.then(function(confirmacion) {
    if (confirmacion) {
        window.history.back();
    }
});
/*    document.getElementById('mensaje_div').innerHTML = "El proceso de registro será cancelado y se borrarán los datos del formulario.";
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

    $("#mensaje_div").dialog('open');*/
}


function cambiaTipoForm(sel){
    var formacion=document.getElementById("formacion").value;
    var ciclo=document.getElementById("ciclos_f").value;
    var option = document.createElement("option");
    if (formacion=="basico") lista_cic=ciclos_basico;
    else if(formacion=="medio")lista_cic=ciclos_medio;
    else if(formacion=="superior") lista_cic=ciclos_superior;
    if (sel=='formacion'){
        document.getElementById("ciclos_f").innerHTML="";
        document.getElementById("curso_ciclo").innerHTML="";
        option.value="";
        if (formacion!=""){
            option.textContent="Seleccione un ciclo ....";
            document.getElementById("ciclos_f").appendChild(option);
            option = document.createElement("option");
            option.value="";
            option.textContent="Sel.Cic.";
            document.getElementById("curso_ciclo").appendChild(option);
            for (i=0; i<Object.keys(lista_cic).length;i++){
                option = document.createElement("option");
                option.value = lista_cic[i]["ciclo"];
                option.textContent = lista_cic[i]["ciclo"];
                document.getElementById("ciclos_f").appendChild(option);
            }
        }
        else {
            option.value="";
            option.textContent="Seleccione Formación en el desplegable anterior.";
            document.getElementById("ciclos_f").appendChild(option);
            option = document.createElement("option");
            option.value="";
            option.textContent="Sel.Form.";
            document.getElementById("curso_ciclo").appendChild(option);
        }
    }
    else if(sel=='ciclo'){
        document.getElementById("curso_ciclo").innerHTML="";
        option.value="";
        if (ciclo!=""){
            option.textContent="Curso...";
            document.getElementById("curso_ciclo").appendChild(option);
            var indice=Object.keys(lista_cic).find(key => lista_cic[key].ciclo === ciclo)
            document.getElementById("departamento").value=lista_cic[indice]["departamento"];
            for (i=1; i<=parseInt(lista_cic[indice]["cursos"]);i++){
                option = document.createElement("option");
                option.value = i+"º";
                option.textContent = i+"º";
                document.getElementById("curso_ciclo").appendChild(option);
            }
        }
        else{
            option.textContent="Sel.Form.";
            document.getElementById("curso_ciclo").appendChild(option);
        }
    }
}




function anadeDoc(e) {
    e.preventDefault();
    creaInputs();
    activaErrorEnTabla(false);
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

function selArch(){
    if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked").length==0){
        alerta("Debe seleccionar antes un tipo de documento.","FALTA SELECCIÓN TIPO");
        return;
    }
    selUltimoFile().click();
}



function iniciaGeneraPdf() {
    var validaForm=$("#exenc").valid();
    var validaTabla=validarTabla();
    if (validaForm  && validaTabla){
        generaImpreso();
    }
}

function validarTabla(){
    var tabla = $("#tab_lista_docs");
    var filas = tabla.find("tr");
    var resultado=true;
    if (filas.length === 1) {
        var celdaTexto = filas.first().find("td").text().trim();
        if(celdaTexto == "LISTA DE DOCUMENTOS VACÍA"){
            resultado=false;
            activaErrorEnTabla(true);
        }
    }
    return resultado; 
}

function activaErrorEnTabla(i){
    if (i){
        document.getElementById("error_tabla").innerHTML="No se han adjuntado documentos";
    }
    else {
        document.getElementById("error_tabla").innerHTML="";
    }
}


function generaImpreso() {
    document.getElementById("cargando").style.display = '';
    document.getElementById("subido_por").value="usuario";
    formData.append("id_nie", id_nie);
    formData.append("anno_curso", curso);
    formData.append("lista_don", document.getElementById("lista_don").value);
    formData.append("nombre", document.getElementById("nombre").value);
    formData.append("apellidos", document.getElementById("apellidos").value);
    formData.append("nif_nie", document.getElementById("nif_nie").value);
    formData.append("grado", document.getElementById("formacion").value);
    formData.append("ciclo", document.getElementById("ciclos_f").value);
    formData.append("curso_ciclo", document.getElementById("curso_ciclo").value);
    formData.append("departamento", document.getElementById("departamento").value);
    formData.append("subido_por", document.getElementById("subido_por").value);
    formData.append("firma", encodeURIComponent(canvas_upload));
        
    datosHidden = document.querySelectorAll('input[name="desc[]"]');
    datosFiles = document.querySelectorAll('input[name="docs[]"]');
    for (var i = 0; i < datosHidden.length; i++) {
        formData.append("desc[]", datosHidden[i].value);
        formData.append("docs[]", datosFiles[i].files[0]);
    }

    urlPHP="php/generapdf.php";
    document.getElementById("cargando").style.display = 'inherit';
    $.post({
        url:urlPHP ,
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
            else if (resp.substring(0, 8) == "database") alerta("Hay un problema en la base de datos.Error:"+resp+"<br> Inténtelo más tarde.", "ERROR DB");
            else if (resp == "error_subida") alerta("El resgistro ha fallado porque no se ha podido subir correctamente alguno de los documentos. Debe intentarlo en otro momento o revisar el formato de los documentos subidos.", "ERROR UPLOAD");
            else if (resp == "ok") {
                alerta("Solicitud registrada correctamente. Puede revisarla en 'Mis Gestiones'", "PROCESO OK", true, 500);
            }
            document.getElementById('exenc').reset();
            //window.history.back();
        },
        error: function(xhr, status, error) {
            document.getElementById("cargando").style.display = 'none';
            alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
            document.getElementById('exenc').reset();
            //window.history.back();
        }
    });
            
}



// JavaScript Document