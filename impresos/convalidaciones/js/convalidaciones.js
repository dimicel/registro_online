var id_nie = "";
var nombre="";
var apellidos="";
var nif_nie="";
var curso = "";
var drawing = false;
var mouseX, mouseY;
var matrizMods=[];

var formData = new FormData();
var subidoDocIdent=false;

var ciclos_gb=new Array();
var ciclos_gm=new Array();  
var ciclos_gs=new Array();
var cursos_espcializacion=new Array();

$(document).ready(function() {

    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function() {}, "json"));
    dat2= dat1.then((resp) => {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            nif_nie=resp["id_nif"];
            nombre=resp["nombre"];
            apellidos=resp["apellidos"];
            document.getElementById("nif_nie").value = nif_nie;
            document.getElementById("nombre").value = nombre;
            document.getElementById("apellidos").value = apellidos;
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            document.getElementById("email").value = email;

            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }

        }
        return ($.post("../../php/usu_recdatospers.php", { id_nie: id_nie }, () => {}, "json"));
    });
    dat3 = dat2.then((resp) => {
            if (resp.error == "ok") {
                for (e in resp.datos) {
                    if (typeof(resp.datos[e]) == "undefined" || resp.datos[e] == null) resp.datos[e] = "";
                }
                document.getElementById("tlf_movil").value = resp.datos.telef_alumno;
                document.getElementById("email").value = resp.datos.email;
                document.getElementById("direccion").value = resp.datos.direccion;
                document.getElementById("cp").value = resp.datos.cp;
                document.getElementById("localidad").value = resp.datos.localidad;
                document.getElementById("provincia").value = resp.datos.provincia;
            } else {
                document.getElementById("tlf_movil").value = '';
                document.getElementById("email").value = '';
                document.getElementById("direccion").value = '';
                document.getElementById("cp").value = '';
                document.getElementById("localidad").value = '';
                document.getElementById("provincia").value = '';
            }
            return ($.post('../exencion_fct/php/ciclos.php',{},()=>{},"json")); 
        
    });
    dat3.then((resp) => {
        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i].grado == "BÁSICO") {
                ciclos_gb.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
            if (resp.datos[i].grado == "MEDIO") {
                ciclos_gm.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
            if (resp.datos[i].grado == "SUPERIOR") {
                ciclos_gs.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
            if (resp.datos[i].grado == "CURSO DE ESPECIALIZACIÓN") {
                cursos_espcializacion.push(new Array(resp.datos[i].denominacion,resp.datos[i].cursos,resp.datos[i].diurno,resp.datos[i].vespertino,resp.datos[i].nocturno,resp.datos[i]["e-learning"]));
            }
        }
    }); 

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)



});



function seleccion(obj) {
    if (obj.id == "instrucciones") {
        open("instrucciones/instrucciones.pdf", "_blank");
    } else{
        document.getElementById("nif_nie").value = nif_nie;
        document.getElementById("nombre").value = nombre;
        document.getElementById("apellidos").value = apellidos;
        $.post("../../php/usu_recdatospers.php", { id_nie: id_nie }, (resp) => {
            if (resp.error == "ok") {
                for (e in resp.datos) {
                    if (typeof(resp.datos[e]) == "undefined" || resp.datos[e] == null) resp.datos[e] = "";
                }
                document.getElementById("tlf_movil").value = resp.datos.telef_alumno;
                document.getElementById("email").value = resp.datos.email;
                document.getElementById("direccion").value = resp.datos.direccion;
                document.getElementById("cp").value = resp.datos.cp;
                document.getElementById("localidad").value = resp.datos.localidad;
                document.getElementById("provincia").value = resp.datos.provincia;
            } else {
                document.getElementById("tlf_movil").value = '';
                document.getElementById("email").value = '';
                document.getElementById("direccion").value = '';
                document.getElementById("cp").value = '';
                document.getElementById("localidad").value = '';
                document.getElementById("provincia").value = '';
            }
            
            $("#seccion-intro").hide();
            $("#seccion-formulario").show();
            creaValidador();
            //document.getElementById("rotulo").innerHTML="SOLICITUD CONVALIDACIONES";
            document.getElementById("label_estudios_aportados").innerHTML="Estudios que aporta (<a style='color:#00C' href='#' onclick='anadeDoc(event)'>Clic AQUÍ para añadir documentos</a>)";
            
        }, "json");
    } 
}

function creaValidador() {
    $("#form_convalidaciones").validate({
        rules: {
            apellidos: {
                required: true
            },
            nombre: {
                required: true
            },
            nif_nie: {
                required: true
            },
            cp: {
                required: true
            },
            direccion: {
                required: true
            },
            localidad: {
                required: true
            },
            provincia: {
                required: true
            },
            tlf_movil: {
                required: true
            },
            email: {
                required: true,
                email: true
            },
            grado: {
                required: true
            },
            modalidad: {
                required: true
            },
            ciclos: {
                required: true
            },
            modulos: {
                required: true
            },
            estudios_superados:{
                required: true
            }
        },
        messages: {
            apellidos: {
                required: "Vacío"
            },
            nombre: {
                required: "Vacío"
            },
            nif_nie: {
                required: "Vacío"
            },
            cp: {
                required: "Vacío"
            },
            direccion: {
                required: "Vacío"
            },
            localidad: {
                required: "Vacío"
            },
            provincia: {
                required: "Vacío"
            },
            tlf_movil: {
                required: "Vacío"
            },
            email: {
                required: "Vacío",
                email: "Inválido"
            },
            grado: {
                required: "Seleccione uno"
            },
            modalidad: {
                required: "Seleccione uno"
            },
            ciclos: {
                required: "Seleccione uno"
            },
            modulos: {
                required: "Seleccione módulos"
            },
            estudios_superados:{
                required: "Vacío"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == "modulos" || $(element).attr('name') == "estudios_superados") $("label[for='" + $(element).attr('name') + "']").next($('.errorTxt')).html(error);
            //$("label[for='"+$(element).attr('name')+"']").next($('.errorTxt')).html(error);
        }
    });
}

function vuelve() {
    $("#seccion-intro").show();
    $("#seccion-formulario").hide();
    document.getElementById("array_input_type_file").innerHTML="";
    document.getElementById("tab_lista_docs").innerHTML="<tr><td style='text-align:center'>LISTA DE DOCUMENTOS VACÍA</td></tr>";
    document.getElementById("form_convalidaciones").reset();
}

function selGrado(obj) {
    sel = document.getElementById("ciclos");
    if (obj.value == "") {
        sel.innerHTML = "";
        option = document.createElement('option');
        option.value = "";
        option.text = "Selecciona grado ...";
        sel.appendChild(option);
        return;
    }
    sel.innerHTML = "";
    option = document.createElement('option');
    option.value = "";
    if (obj.value == "") option.text = "Selecciona grado ...";
    else option.text = "Selecciona ciclo ...";
    sel.appendChild(option);
    if (obj.value == "Básico") {arr=ciclos_gb;}
    else if (obj.value == "Medio") {arr=ciclos_gm;}
    else if (obj.value == "Superior") {arr=ciclos_gs;}
    else if (obj.value == "Curso de Especialización") {arr=cursos_espcializacion;}
    for (i = 0; i < arr.length; i++) {
        const option = document.createElement('option');
        option.value = arr[i][0];
        option.text = arr[i][0];
        sel.appendChild(option);
    }
    sel.selectedIndex = 0;
        
}


function selCiclo(obj){
    grado=document.getElementById("grado").value;
    ciclo=obj.value;
    if (grado == "Básico") {arr=ciclos_gb;}
    else if (grado == "Medio") {arr=ciclos_gm;}
    else if (grado == "Superior") {arr=ciclos_gs;}
    else if (grado == "Curso de Especialización") {arr=cursos_espcializacion;}
    for(i=0; i<arr.length; i++){
        if (arr[i][0]==ciclo){
            cicloArr=arr[i];
            break;
        } 
    }
    optCurso="<option value=''>Seleccione uno...</option>";
    optCurso += "<option value='1º'>1º</option>";
    if (cicloArr[1]>1) optCurso += "<option value='2º'>2º</option>";
    if (cicloArr[1]>2) optCurso += "<option value='3º'>3º</option>";
    if (cicloArr[5]==1) optCurso += "<option value='Virtual_Modular'>Virtual (E-Learning)/Modular</option>";
    document.getElementById("curso").innerHTML=optCurso;
    document.getElementById("curso").selectedIndex=0;
    optTurno="<option value=''>Seleccione uno...</option>";
    if (cicloArr[2]==1) optTurno += "<option value='Diurno'>Diurno</option>";
    if (cicloArr[3]==1) optTurno += "<option value='Vespertino'>Vespertino</option>";
    if (cicloArr[4]==1) optTurno += "<option value='Nocturno'>Nocturno</option>";
    if (cicloArr[5]==1) optTurno += "<option value='Virtual'>Virtual (E-Learning)</option>";
    document.getElementById("turno").innerHTML=optTurno;
    document.getElementById("turno").selectedIndex=0;
    optModalidad="<option value=''>Seleccione uno...</option>";
    optModalidad+="<option value='Presencial'>Presencial</option>";
    optModalidad+="<option value='Semipresencial'>Semipresencial</option>";
    if (cicloArr[5]==0)optModalidad+="<option value='Modular'>Modular</option>";
    if (cicloArr[5]==1) optModalidad+="<option value='Virtual'>Virtual (E-Learning)</option>";
    document.getElementById("modalidad").innerHTML=optModalidad;

}

function selModulos(e) {
    e.preventDefault();
    if (document.getElementById("ciclos").selectedIndex == 0) {
        alerta("Seleccione antes un ciclo formativo.", "CICLO SIN SELECCIÓN");
        return;
    }
    else if(document.getElementById("curso").selectedIndex == 0){
        alerta("Seleccione antes el curso.", "CURSO SIN SELECCIÓN");
        return;
    }
    else if(document.getElementById("curso").selectedIndex == 0 && document.getElementById("ciclos").selectedIndex == 0){
        alerta("Seleccione antes el ciclo formativo y el curso.", "CICLO Y CURSO SIN SELECCIÓN");
        return;
    }
    _ciclo=document.getElementById("ciclos").value;
    _grado=document.getElementById("grado").value;
    _curso=document.getElementById("curso").value;
    $.post("php/listamodulos.php", { ciclo: _ciclo, grado: _grado, curso: _curso}, (resp) => {
        if (resp["error"] == "servidor") {
            alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
        } else if (resp["error"].indexOf("error_consulta") > -1) {
            alerta("Hay un problema con la base de datos. Inténtelo más tarde.", resp["error"]);
        } else if (resp["error"] == "no_materias") {
            alerta("No se encuentran módulos registrados para el ciclo formativo seleccionado.", "SELECT SIN MÓDULOS");
        } else if (resp["error"] == "ok") {
            var existeDiv = document.getElementById("sMod") !== null;
            if (existeDiv) {
                document.getElementById("sMod").innerHTML = "";
            } else {
                marco = document.createElement('div');
                marco.id = "sMod";
                document.body.appendChild(marco);
            }
            t="<center><label><small>Haz clic sobre los módulos que desees seleccionar. Si quieres quitar uno, tanbién haz clic sobre él</small></label></center><br>";
            t += "<center><table id='tab_lista_modulos'><tr><td><b>Código</b></td><td><b>Módulo</b></td></tr>";
            for (i = 0; i < resp["datos"].length; i++) {
                t += "<tr onclick='selTablaListaMod(this)'><td>" + resp["datos"][i]["codigo"] + "</td><td>" + resp["datos"][i]["materia"] + "</td></tr>";
            }
            t += "</table></center>";
            document.getElementById("sMod").innerHTML = t;
            $("#sMod").dialog({
                autoOpen: true,
                dialogClass: "alert no-close",
                modal: true,
                hide: { effect: "fade", duration: 0 },
                resizable: false,
                show: { effect: "fade", duration: 0 },
                title: "SELECCIÓN DE MÓDULOS A CONVALIDAR",
                width: 700,
                buttons: [{
                    class: "btn btn-success textoboton",
                    text: "Cerrar",
                    click: function() {
                        elementos = document.getElementById("tab_lista_modulos").querySelectorAll("tr.selected");
                        textModulos = "";
                        matrizMods=[];
                        for (i = 0; i < elementos.length; i++) {
                            textModulos += elementos[i].cells[0].innerHTML + "-" + elementos[i].cells[1].innerHTML + ";"
                            matrizMods.push(elementos[i].cells[0].innerHTML + "-" + elementos[i].cells[1].innerHTML);
                        }
                        document.getElementById("modulos").value = textModulos;
                        $("#sMod").dialog("close");
                        //$("#sMod").dialog("destroy");
                    }
                }]
            });
            resaltarFilas();
        }
    }, "json");
}

function resaltarFilas() {
    var textarea = document.getElementById("modulos");
    var tabla = document.getElementById("tab_lista_modulos");
    var elementos = textarea.value.split(";");
  
    for (var i = 0; i < tabla.rows.length; i++) {
      var codigo = tabla.rows[i].cells[0].innerText;
      var descripcion = tabla.rows[i].cells[1].innerText;
  
      if (elementos.includes(codigo + "-" + descripcion)) {
        tabla.rows[i].classList.add("selected");
      }
    }
}


function selTablaListaMod(obj) {
    if (obj.classList.contains("selected")) {
        obj.classList.remove("selected");
        obj.classList.add("deselected");
    } else {
        obj.classList.remove("deselected");
        obj.classList.add("selected");
    }
}

function anadeDoc(e) {
    e.preventDefault();
    creaInputs();
        
        if (subidoDocIdent) $("#div_doc_identificacion").hide();
        else $("#div_doc_identificacion").show();
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
        if (this.multiple && this.files.length!=2){
            alerta("Debe seleccionar dos archivos de imagen: el anverso y reverso del documento de identificación.", "Nº INCORRECTO DE ARCHIVOS SELECCIONADOS");
            return;
        }
        _extension1="pdf";
        _extension2="pdf";
        mensaje_alerta="Por favor, seleccione un archivo PDF.","ERROR TIPO ARCHIVO";
        if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value=="Documento de identificación (Pasaporte)"){
            _extension1="jpeg";
            _extension2="jpg";
            mensaje_alerta="Por favor, seleccione un archivo de imagen JPEG.","ERROR TIPO ARCHIVO";
        }
        else if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value=="Documento de identificación (DNI/NIE)"){
            _extension1="jpeg";
            _extension2="jpg";
            mensaje_alerta="Los dos archivos de imagen correspondientes al anverso y reverso del documento de identificación deben ser del tipo JPEG.","ERROR TIPO ARCHIVO";
        }
        if (this.files.length > 0) {
            for(i=0;i<this.files.length;i++){
                var extension = this.files[i].name.split('.').pop().toLowerCase();
                // Verificar si la extensión del archivo es _extension1 o 2
                if (extension !== _extension1 && extension!==_extension2) {
                    alerta(mensaje_alerta,"ERROR TIPO ARCHIVO");
                    return;
                }
            }
        }

        if (!this.multiple) document.getElementById('archivo_con').value = this.files[0].name;
        else {
            document.getElementById('archivo_con').value = this.files[0].name+", "+this.files[1].name;
        }
        if (document.querySelectorAll("input[name=tipo_con]:checked")[0].value.indexOf("Documento de identificación")>-1){
            muestraEditor(event);
        } 
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



function registraForm() {
    if ($("#form_convalidaciones").valid()) {
        formData.append("id_nie", id_nie);
        formData.append("anno_curso", curso);
        formData.append("nombre", document.getElementById("nombre").value);
        formData.append("apellidos", document.getElementById("apellidos").value);
        formData.append("id_nif", document.getElementById("nif_nie").value);
        formData.append("direccion", document.getElementById("direccion").value);
        formData.append("cp", document.getElementById("cp").value);
        formData.append("localidad",document.getElementById("localidad").value);
        formData.append("provincia", document.getElementById("provincia").value);
        formData.append("tlf_fijo", document.getElementById("tlf_fijo").value);
        formData.append("tlf_movil", document.getElementById("tlf_movil").value);
        formData.append("email", document.getElementById("email").value);
        formData.append("estudios_superados", document.getElementById("estudios_superados").value);
        formData.append("grado", document.getElementById("grado").value);
        formData.append("ciclo", document.getElementById("ciclos").value);
        formData.append("modalidad", document.getElementById("modalidad").value);
        formData.append("turno", document.getElementById("turno").value);
        formData.append("curso", document.getElementById("curso").value);
        formData.append("modulos", document.getElementById("modulos").value);
        formData.append("matrizMods", JSON.stringify(matrizMods));

        

        datosHidden = document.querySelectorAll('input[name="desc[]"]');
        datosFiles = document.querySelectorAll('input[name="docs[]"]');
        for (var i = 0; i < datosHidden.length; i++) {
            if (datosHidden[i].value!="Documento de identificación (DNI/NIE)" && datosHidden[i].value!="Documento de identificación (Pasaporte)"){
                formData.append("desc[]", datosHidden[i].value);
                formData.append("docs[]", datosFiles[i].files[0]);
            }
        }
        
        
        urlPHP="php/registraformulario.php";
        mostrarPantallaEspera();
        $.post({
            url:urlPHP ,
            data: formData,
            contentType: false,
            processData: false,
            success: function(resp) {
                ocultarPantallaEspera();
                if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                else if (resp.substring(0, 8) == "database") alerta("Hay un problema en la base de datos. Inténtelo más tarde.", "ERROR DB");
                else if (resp == "error_subida") alerta("El resgistro ha fallado porque no se ha podido subir correctamente alguno de los documentos. Debe intentarlo en otro momento o revisar el formato de los documentos subidos.", "ERROR UPLOAD");
                else if (resp == "ok") {
                    alerta("Solicitud de convalidación registrada correctamente. Puede revisarla en 'Mis Gestiones'", "PROCESO OK", true, 500);
                }
            },
            error: function(xhr, status, error) {
                ocultarPantallaEspera();
                alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
            }
        });
    } else {
        alerta("Revisa los campos que se han marcado en rojo. Revisa que hayas seleccionado al menos un módulo a convalidar.", "DATOS INVÁLIDOS O AUSENTES");
    }
}





///////////////////FUNCIONES ESPECÍFICAS FORMULARIO PARA CONSEJERÍA
function selecOtraDoc(obj){
    if (obj.checked){
        $("#otra_desc").show();
    } 
    else {
        $("#otra_desc").hide();
        document.getElementById("otra_desc").value="";
    }
}


function selTipoDoc(v){
    if(v=="Documento de identificación (DNI/NIE)") selUltimoFile().multiple=true;
    else selUltimoFile().multiple=false;
    if (v.indexOf("Documento de identificación")>-1) selUltimoFile().accept="image/jpeg, image/jpg";
    else selUltimoFile().accept="application/pdf";
}


function selArchConsej(){
    if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked").length==0){
        alerta("Debe seleccionar antes un tipo de documento.","FALTA SELECCIÓN TIPO");
        return;
    }

    selUltimoFile().click();

    if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value=="Documento de identificación (Pasaporte)"){
        $("#doc_ident_reverso").show();
    }
    else if (document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value=="Documento de identificación (DNI/NIE)"){
        $("#doc_ident_reverso").hide();
    }

}

function muestraEditor(_ev){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/convalidaciones.htm", "div_edita_imagen","EDICIÓN IMAGEN",550,2000,"","",
        [
            {
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    
                   if (_tipoSelecc=="Documento de identificación (DNI/NIE)"){
                        _fname_ajax="dni_anverso";
                        _f_ajax="dni_anverso.jpg";
                   }
                   else {
                        _fname_ajax="pasaporte";
                        _f_ajax="pasaporte.jpg";
                   }
                    _crop1.result({
                        type: 'blob'
                    }).then(function (blob) {
                        return fetch(window.URL.createObjectURL(blob))
                    }).then(function (response) {
                        return response.blob();
                    }).then(function (blob) {
                        formData.append(_fname_ajax, blob, _f_ajax);
                    });
                   _crop1.destroy();
                   if(__ancho==1000){
                        _crop2.result({
                            type: 'blob'
                        }).then(function (blob) {
                            return fetch(window.URL.createObjectURL(blob))
                        }).then(function (response) {
                            return response.blob();
                        }).then(function (blob) {
                            formData.append('dni_reverso', blob,'dni_anverso.jpg');
                        });
                        _crop2.destroy();
                   }
                    $("#div_edita_imagen").dialog("close");
                    $("#div_edita_imagen").dialog("destroy");
                }
            }
        ]
    )
    .then ((dialogo)=>{
            ocultarPantallaEspera();
            _tipoSelecc=document.querySelectorAll("#anade_documento input[name=tipo_con]:checked")[0].value;
            _crop1=new Croppie(document.getElementById("div_imagen_anverso"), {
                viewport: { width: 300, height: 190 },
                boundary: { width: 450, height: 255 },
                showZoomer: false,
                enableOrientation: true
            });
            _crop1.bind({
                url: URL.createObjectURL(_ev.target.files[0]),
                orientation: 1
            });
        
            if (_tipoSelecc=="Documento de identificación (Pasaporte)"){
                $(dialogo).dialog("options","width",500);
            } 
            else{
                $(dialogo).dialog("options","width",1000);
                _crop2=new Croppie(document.getElementById("div_imagen_reverso"), {
                    viewport: { width: 300, height: 190 },
                    boundary: { width: 450, height: 255 },
                    showZoomer: false,
                    enableOrientation: true
                });
                _crop2.bind({
                    url: URL.createObjectURL(_ev.target.files[1]),
                    orientation: 1
                });
            } 
            if (_tipoSelecc=="Documento de identificación (Pasaporte)"){
                $("#doc_ident_reverso").hide();
            }
            else if (_tipoSelecc=="Documento de identificación (DNI/NIE)"){
                $("#doc_ident_reverso").show();
            }             
        }
    )
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });

}



function selCurso(obj){
    if (obj.value=="Virtual_Modular"){
        document.getElementById("turno").value="Virtual";
        document.getElementById("turno").disabled=true;  
        document.getElementById("modalidad").value="Virtual";
        document.getElementById("modalidad").disabled=true;  
    }
    else{
        document.getElementById("turno").disabled=false;
        document.getElementById("modalidad").disabled=false;
    }  
}
