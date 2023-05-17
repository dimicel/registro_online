var id_nie = "";
var formulario = "";
var curso = "";
var drawing = false;
var mouseX, mouseY;

var canvas, context, tool, canvas_upload;


$(document).ready(function() {

    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            document.getElementById("nif_nie").value = resp["id_nif"];
            document.getElementById("nombre").value = resp["nombre"];
            document.getElementById("apellidos").value = resp["apellidos"];
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            curso = anno_ini_curso + "-" + (anno_ini_curso + 1);
            document.getElementById("email").value = email;

            if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
                document.write("Error datos. Por favor, inténtelo más tarde.");
            }

        }
    }, "json"));
    dat2 = dat1.then(() => {
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
        }, "json");
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

    canvas = document.getElementById('firmaCanvas');
    context = canvas.getContext('2d');
    canvas.addEventListener('mousedown', ev_canvas, false);
    canvas.addEventListener('mousemove', ev_canvas, false);
    canvas.addEventListener('mouseup', ev_canvas, false);
    canvas.addEventListener("mouseout", ev_canvas, false);
    canvas.addEventListener('touchstart', ev_canvas, false);
    canvas.addEventListener('touchmove', ev_canvas, false);
    canvas.addEventListener('touchend', ev_canvas, false);

});



function seleccion(obj) {
    if (obj.id == "instrucciones") {
        open("instrucciones/instrucciones.pdf", "_blank");
    } else if (obj.id == "consejeria") {
        $("#seccion-intro").hide();
        $("#seccion-formulario").show();
        formulario = "Consejería";
        creaValidador();
        document.getElementById("rotulo").innerHTML="SOLICITUD CONVALIDACIONES PARA CONSEJERÍA DE EDUCACIÓN";
        document.getElementById("label_estudios_aportados").innerHTML="Estudios que aporta (<a style='color:#00C' href='#' onclick='anadeDoc(event)'>Clic AQUÍ para añadir documentos</a>)";
    } else if (obj.id == "centro_ministerio") {
        $("#seccion-intro").hide();
        $("#seccion-formulario").show();
        formulario = "Centro-Ministerio";
        creaValidador();
        document.getElementById("rotulo").innerHTML="SOLICITUD CONVALIDACIONES PARA EL CENTRO EDUCATIVO O EL MINISTERIO";
        document.getElementById("label_estudios_aportados").innerHTML="Documentos que adjunta (<a style='color:#00C' href='#' onclick='anadeDoc(event)'>Clic AQUÍ para añadir documentos</a>)";
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
            t_firm: {
                required: true
            },
            grado: {
                required: true
            },
            ciclos: {
                required: true
            },
            modulos: {
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
            t_firm: {
                required: "Vacío"
            },
            grado: {
                required: "Seleccione uno"
            },
            ciclos: {
                required: "Seleccione uno"
            },
            modulos: {
                required: "Seleccione módulos"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == "modulos") $("label[for='" + $(element).attr('name') + "']").next($('.errorTxt')).html(error);
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
    $.post("php/listaciclos.php", { grado: obj.value }, (resp) => {
        if (resp["error"] == "servidor") {
            alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
        } else if (resp["error"] == "error_consulta") {
            alerta("Hay un problema con la base de datos. Inténtelo más tarde.", "ERROR DB");
        } else if (resp["error"] == "no_ciclos") {
            alerta("No se encuentran ciclos formativos registrados.", "SELECT SIN CICLOS");
        } else if (resp["error"] == "ok") {
            sel.innerHTML = "";
            option = document.createElement('option');
            option.value = "";
            if (obj.value == "") option.text = "Selecciona grado ...";
            else option.text = "Selecciona ciclo ...";
            sel.appendChild(option);
            for (i = 0; i < resp["datos"].length; i++) {
                const option = document.createElement('option');
                option.value = resp["datos"][i];
                option.text = resp["datos"][i];
                sel.appendChild(option);
            }
            sel.selectedIndex = 0;
        }
    }, "json");
}


function selModulos(e) {
    e.preventDefault();
    if (document.getElementById("ciclos").selectedIndex == 0) {
        alerta("Seleccione antes un ciclo formativo.", "CICLO SIN SELECCIÓN");
        return;
    }
    $.post("php/listamodulos.php", { ciclo: document.getElementById("ciclos").value, grado: document.getElementById("grado").value }, (resp) => {
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
                        for (i = 0; i < elementos.length; i++) {
                            textModulos += elementos[i].cells[0].innerHTML + "-" + elementos[i].cells[1].innerHTML + ";"
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
    if (formulario=="Centro-Ministerio"){
        $("#anade_documento_centroministerio").dialog({
            autoOpen: true,
            dialogClass: "alert no-close",
            modal: true,
            hide: { effect: "fade", duration: 0 },
            resizable: false,
            show: { effect: "fade", duration: 0 },
            title: "AÑADIR ESTUDIO A APORTAR",
            width: 700,
            buttons: [{
                    class: "btn btn-success textoboton",
                    text: "Aceptar",
                    click: function() {
                        if (document.querySelectorAll("#anade_documento_centroministerio input[name=tipo]:checked").length == 0 ||
                            document.getElementById("den_estudios").value.trim().length == 0 ||
                            document.getElementById("archivo").value.trim().length == 0) {
                            alerta("Debe seleccionar un tipo, un documento y poner una breve descripción del documento que adjunta.", "FALTAN DATOS");
                            return;
                        }
                        actualizaTablaListaDocs();
                        document.getElementById("form_anade_documento_cenminis").reset();
                        $("#anade_documento_centroministerio").dialog("close");
                        $("#anade_documento_centroministerio").dialog("destroy");
                    }
                },
                {
                    class: "btn btn-success textoboton",
                    text: "Cancelar",
                    click: function() {
                        document.getElementById("form_anade_documento_cenminis").reset();
                        $("#anade_documento_centroministerio").dialog("close");
                        $("#anade_documento_centroministerio").dialog("destroy");
                    }
                }
            ]
        });
    }
    else if(formulario=="Consejería"){
        $("#anade_documento_consejeria").dialog({
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
                        if (document.querySelectorAll("#anade_documento_consejeria input[name=tipo_con]:checked").length == 0 || 
                            document.getElementById("archivo_con").value.trim().length == 0){
                                alerta("Debe seleccionar un tipo de documento y un archivo.", "FALTAN DATOS");
                                return;
                        }
                        else if(document.querySelectorAll("#anade_documento_consejeria input[name=tipo_con]:checked")[0].value=="Otro" &&
                                document.getElementById("den_otro_con").value.trim().length == 0){
                                    alerta("Debe especificar qué tipo de documento va a adjuntar.", "FALTAN DATOS");
                                    return;
                        }
                        actualizaTablaListaDocs();
                        document.getElementById("form_anade_documento_con").reset();
                        $("#anade_documento_consejeria").dialog("close");
                        $("#anade_documento_consejeria").dialog("destroy");
                    }
                },
                {
                    class: "btn btn-success textoboton",
                    text: "Cancelar",
                    click: function() {
                        document.getElementById("form_anade_documento_con").reset();
                        $("#anade_documento_consejeria").dialog("close");
                        $("#anade_documento_consejeria").dialog("destroy");
                    }
                }
            ]
        });
    }

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
    tipoFile.addEventListener("change", function() {
        if (this.multiple && this.files.length!=2){
            alerta("Debe seleccionar dos archivos de imagen: el anverso y reverso del documento de identificación.", "Nº INCORRECTO DE ARCHIVOS SELECCIONADOS")
        }
        if (formulario=="Centro-Ministerio"){
            document.getElementById('archivo').value = this.files[0].name;
        }
        else{
            if (!this.multiple) document.getElementById('archivo_con').value = this.files[0].name;
            else {
                document.getElementById('archivo_con').value = this.files[0].name+", "+this.files[1].name;
            }
        }  
    });
}

function selUltimoFile() {
    _a = document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
    return _a[_a.length - 1];
    //_a[_a.length - 1].click();
}

function actualizaTablaListaDocs() {
    _a = document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
    _arch = _a[_a.length - 1].files[0].name;
    _d = document.getElementById("array_input_type_file").querySelectorAll("input[type=hidden]");
    _d[_d.length - 1].value = "(" + document.querySelectorAll("input[name=tipo]:checked")[0].value + ") " + document.getElementById("den_estudios").value;
    _t = document.getElementById("tab_lista_docs");
    if (_t.rows[0].cells.length == 1) {
        _t.deleteRow(0);
    }
    var nuevaFila = _t.insertRow();

    // Insertar una celda en la nueva fila (primera columna)
    var celda1 = nuevaFila.insertCell();
    if (formulario=="Centro-Ministerio"){
        celda1.textContent = "(" + document.querySelectorAll("input[name=tipo]:checked")[0].value + ") " + document.getElementById("den_estudios").value;

    }
    else {
        celda1.textContent = "(" + document.querySelectorAll("input[name=tipo]:checked")[0].value + ") " + document.getElementById("den_estudios_con").value;

    }
    celda1.style.width = "50%";

    // Insertar una celda en la nueva fila (segunda columna)
    var celda2 = nuevaFila.insertCell();
    celda2.textContent = _arch;
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
    if (formulario == "Centro-Ministerio") {
        if ($("#form_convalidaciones").valid()) {
            var formData = new FormData();
            formData.append("id_nie", encodeURIComponent(id_nie));
            formData.append("curso", encodeURIComponent(curso));
            formData.append("formulario", encodeURIComponent(formulario));
            formData.append("nombre", encodeURIComponent(document.getElementById("nombre").value));
            formData.append("apellidos", encodeURIComponent(document.getElementById("apellidos").value));
            formData.append("id_nif", encodeURIComponent(document.getElementById("nif_nie").value));
            formData.append("direccion", encodeURIComponent(document.getElementById("direccion").value));
            formData.append("cp", encodeURIComponent(document.getElementById("cp").value));
            formData.append("localidad", encodeURIComponent(document.getElementById("localidad").value));
            formData.append("provincia", encodeURIComponent(document.getElementById("provincia").value));
            formData.append("tlf_fijo", encodeURIComponent(document.getElementById("tlf_fijo").value));
            formData.append("tlf_movil", encodeURIComponent(document.getElementById("tlf_movil").value));
            formData.append("email", encodeURIComponent(document.getElementById("email").value));
            formData.append("grado", encodeURIComponent(document.getElementById("grado").value));
            formData.append("ciclo", encodeURIComponent(document.getElementById("ciclos").value));
            formData.append("modulos", encodeURIComponent(document.getElementById("modulos").value));
            //formData.append("firma",document.getElementById("firma").files[0]);
            formData.append("firma", encodeURIComponent(canvas_upload));
            datosHidden = document.querySelectorAll('input[name="desc[]"]');
            for (var i = 0; i < datosHidden.length; i++) {
                formData.append("desc[]", encodeURIComponent(datosHidden[i].value));
            }
            datosFiles = document.querySelectorAll('input[name="docs[]"]');
            for (var i = 0; i < datosFiles.length; i++) {
                formData.append("docs[]", datosFiles[i].files[0]);
            }
            document.getElementById("cargando").style.display = 'inherit';
            $.post({
                url: "php/registracentroministerio.php",
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    document.getElementById("cargando").style.display = 'none';
                    if (resp == "servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.", "ERROR SERVIDOR");
                    else if (resp == "database") alerta("Hay un problema en la base de datos. Inténtelo más tarde.", "ERROR DB");
                    else if (resp == "error_subida") alerta("El resgistro ha fallado porque no se ha podido subir correctamente alguno de los documentos. Debe intentarlo en otro momento o revisar el formato de los documentos subidos.", "ERROR UPLOAD");
                    else if (resp == "ok") {
                        alerta("Solicitud de convalidación registrada correctamente. Puede revisarla en 'Mis Gestiones'", "PROCESO OK", true, 500);
                    }
                },
                error: function(xhr, status, error) {
                    alerta("Error en servidor. Código " + error + "<br>Inténtelo más tarde.", "ERROR DE SERVIDOR");
                }
            });
        } else {
            alerta("Revisa los campos que se han marcado en rojo. Revisa que hayas seleccionado al menos un módulo a convalidar.", "DATOS INVÁLIDOS O AUSENTES");
        }
    }
}



function canvasFirma() {

    tool = new tool_pencil();
    $("#div_canvas_firma").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "FIRMA",
        width: 500,
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    if (!isCanvasEmpty()) {
                        document.getElementById("t_firm").value = "FORMULARIO FIRMADO";
                        canvas_upload = canvas.toDataURL('image/png');
                    } else {
                        document.getElementById("t_firm").value = "";
                    }
                    $("#div_canvas_firma").dialog("close");
                    $("#div_canvas_firma").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Borrar",
                click: function() {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                    document.getElementById("t_firm").value = "";
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    if (!isCanvasEmpty()) {
                        document.getElementById("t_firm").value = "FORMULARIO FIRMADO";
                        canvas_upload = canvas.toDataURL('image/png');
                    } else {
                        document.getElementById("t_firm").value = "";
                    }
                    $("#div_canvas_firma").dialog("close");
                    $("#div_canvas_firma").dialog("destroy");
                }
            }
        ]
    });
}

function tool_pencil() {
    var tool = this;
    this.started = false;

    // This is called when you start holding down the mouse button or touch the screen.
    // This starts the pencil drawing.
    this.startDrawing = function(x, y) {
        context.beginPath();
        context.moveTo(x, y);
        tool.started = true;
    };

    // This function is called every time you move the mouse or touch the screen. 
    // It draws a line if the tool.started state is set to true.
    this.draw = function(x, y) {
        if (!tool.started) return;
        context.lineTo(x, y);
        context.stroke();
    };

    // This is called when you release the mouse button or end touching the screen.
    this.endDrawing = function() {
        tool.started = false;
    };
}

// The general-purpose event handler. This function determines the mouse or touch position relative to the canvas element.
function ev_canvas(ev) {
    var canvasRect = canvas.getBoundingClientRect();
    var clientX, clientY;

    if (ev.touches && ev.touches.length > 0) {
        ev.preventDefault();
        clientX = ev.touches[0].clientX;
        clientY = ev.touches[0].clientY;
    } else {
        clientX = ev.clientX;
        clientY = ev.clientY;
    }

    var x = clientX - canvasRect.left;
    var y = clientY - canvasRect.top;

    var func;
    if (ev.type === 'mousedown' || ev.type === 'touchstart') {
        func = tool.startDrawing;
    } else if (ev.type === 'mousemove' || ev.type === 'touchmove') {
        func = tool.draw;
    } else if (ev.type === 'mouseup' || ev.type === 'touchend') {
        func = tool.endDrawing;
    }

    if (func) {
        func(x, y);
    }
}

// Función para verificar si el canvas contiene algo dibujado
function isCanvasEmpty() {
    var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    var data = imageData.data;

    for (var i = 0; i < data.length; i += 4) {
        // Comprobar si el canal alfa (transparencia) es mayor que 0
        if (data[i + 3] !== 0) {
            return false; // El canvas contiene algo dibujado
        }
    }

    return true; // El canvas está vacío
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
    if (document.querySelectorAll("#anade_documento_consejeria input[name=tipo_con]:checked").length==0){
        alerta("Debe seleccionar antes un tipo de documento.","FALTA SELECCIÓN TIPO");
    }
    else selUltimoFile().click();
}