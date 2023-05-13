var id_nie = "";
var formulario="";
var curso="";



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
    dat2=dat1.then(()=>{
        $.post("../../php/usu_recdatospers.php",{id_nie:id_nie},(resp)=>{
            if (resp.error=="ok"){
                for (e in resp.datos){
                    if(typeof(resp.datos[e])=="undefined" || resp.datos[e]==null) resp.datos[e]="";
                }
                document.getElementById("tlf_movil").value=resp.datos.telef_alumno;
                document.getElementById("email").value=resp.datos.email;
                document.getElementById("direccion").value=resp.datos.direccion;
                document.getElementById("cp").value=resp.datos.cp;
                document.getElementById("localidad").value=resp.datos.localidad;
                document.getElementById("provincia").value=resp.datos.provincia;
            }
            else{
                document.getElementById("tlf_movil").value='';
                document.getElementById("email").value='';
                document.getElementById("direccion").value='';
                document.getElementById("cp").value='';
                document.getElementById("localidad").value='';
                document.getElementById("provincia").value='';
            }
        },"json");
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

});



function seleccion(obj){
    if (obj.id=="instrucciones"){
        open("instrucciones/instrucciones.pdf","_blank");
    }
    else if (obj.id=="consejeria"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").show();
        $("#seccion-centro_ministerio").hide();
        formulario="Consejería";
    }
    else if(obj.id=="centro_ministerio"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").hide();
        $("#seccion-centro_ministerio").show();
        formulario="Centro-Ministerio";
        creaValidadorCentroMin()
    }
}

function creaValidadorCentroMin(){
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
                required:true,
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
                email:"Inválido"
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
            if ($(element).attr('name')=="modulos") $("label[for='"+$(element).attr('name')+"']").next($('.errorTxt')).html(error);
            //$("label[for='"+$(element).attr('name')+"']").next($('.errorTxt')).html(error);
        }
    });
}

function vuelve(){
    $("#seccion-intro").show();
    $("#seccion-consejeria").hide();
    $("#seccion-centro_ministerio").hide();
}

function selGrado(obj){
    sel=document.getElementById("ciclos");
    if (obj.value==""){
        sel.innerHTML="";
        option = document.createElement('option');
        option.value = "";
        option.text = "Selecciona grado ...";
        sel.appendChild(option);
        return;
    }
    $.post("php/listaciclos.php",{grado:obj.value},(resp)=>{
        if (resp["error"]=="servidor"){
            alerta("Hay un problema con el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
        }
        else if(resp["error"]=="error_consulta"){
            alerta("Hay un problema con la base de datos. Inténtelo más tarde.","ERROR DB");
        }
        else if(resp["error"]=="no_ciclos"){
            alerta("No se encuentran ciclos formativos registrados.","SELECT SIN CICLOS");
        }
        else if(resp["error"]=="ok"){
            sel.innerHTML="";
            option = document.createElement('option');
            option.value = "";
            if (obj.value=="") option.text = "Selecciona grado ...";
            else option.text = "Selecciona ciclo ...";
            sel.appendChild(option);
            for (i=0;i<resp["datos"].length;i++){
                const option = document.createElement('option');
                option.value = resp["datos"][i];
                option.text = resp["datos"][i];
                sel.appendChild(option);
            }
            sel.selectedIndex=0;
        }
    },"json");
}


function selModulos(e){
    e.preventDefault();
    if (document.getElementById("ciclos").selectedIndex==0){
        alerta("Seleccione antes un ciclo formativo.","CICLO SIN SELECCIÓN");
        return;
    }
    $.post("php/listamodulos.php",{ciclo:document.getElementById("ciclos").value,grado:document.getElementById("grado").value},(resp)=>{
        if (resp["error"]=="servidor"){
            alerta("Hay un problema con el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
        }
        else if(resp["error"].indexOf("error_consulta")>-1){
            alerta("Hay un problema con la base de datos. Inténtelo más tarde.",resp["error"]);
        }
        else if(resp["error"]=="no_materias"){
            alerta("No se encuentran módulos registrados para el ciclo formativo seleccionado.","SELECT SIN MÓDULOS");
        }
        else if(resp["error"]=="ok"){
            var existeDiv = document.getElementById("sMod") !== null;
            if (existeDiv){
                document.getElementById("sMod").innerHTML="";
            }
            else {
                marco=document.createElement('div');
                marco.id="sMod";
                document.body.appendChild(marco);
            }
            t="<center><table id='tab_lista_modulos'><tr><td><b>Código</b></td><td><b>Módulo</b></td></tr>";
            for (i=0; i<resp["datos"].length;i++){
                t+="<tr onclick='selTablaListaMod(this)'><td>"+resp["datos"][i]["codigo"]+"</td><td>"+resp["datos"][i]["materia"]+"</td></tr>";
            }
            t+="</table></center>";
            document.getElementById("sMod").innerHTML=t;
            
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
                        textModulos="";
                        for (i=0;i<elementos.length;i++){
                            textModulos+=elementos[i].cells[0].innerHTML+"-"+elementos[i].cells[1].innerHTML+";"
                        }
                        document.getElementById("modulos").value=textModulos;
                        $("#sMod").dialog("close");
                        //$("#sMod").dialog("destroy");
                    }
                }]
            });       
        }
    },"json");
}


function selTablaListaMod(obj){
    if (obj.classList.contains("selected")) {
        obj.classList.remove("selected");
        obj.classList.add("deselected");
      } else {
        obj.classList.remove("deselected");
        obj.classList.add("selected");
      }
}

function anadeDoc(e){
    e.preventDefault();
    creaInputs();
    $("#anade_documento").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "AÑADIR ESTUDIO A APORTAR",
        width: 700,
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    if (document.querySelectorAll("input[name=tipo]:checked").length==0 || 
                        document.getElementById("den_estudios").value.trim().length==0 || 
                        document.getElementById("archivo").value.trim().length==0){
                            alerta("Debe seleccionar un tipo, un documento y poner una breve descripción del documento que adjunta.","FALTAN DATOS");
                            return;
                    }
                    actualizaTablaListaDocs();
                    $("#anade_documento").dialog("close");
                    $("#anade_documento").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $("#anade_documento").dialog("close");
                    $("#anade_documento").dialog("destroy");
                }
            }]
    });       
}



function creaInputs(){
    divArray=document.getElementById("array_input_type_file");
    tipoHidden=document.createElement("input");
    tipoHidden.type="hidden";
    tipoHidden.name="desc[]";
    tipoFile=document.createElement("input");
    tipoFile.type="file";
    tipoFile.name="docs[]";
    tipoFile.multiple=false;
    divArray.appendChild(tipoHidden);
    divArray.appendChild(tipoFile);
    tipoFile.addEventListener("change", function() {
        _a=document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
        document.getElementById('archivo').value=_a[_a.length-1].files[0].name;
    });
}

function selUltimoFile(){
    _a=document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
    _a[_a.length-1].click();
}

function actualizaTablaListaDocs(){
    _a=document.getElementById("array_input_type_file").querySelectorAll("input[type=file]");
    _arch=_a[_a.length-1].files[0].name;
    _d=document.getElementById("array_input_type_file").querySelectorAll("input[type=hidden]");
    _d[_d.length-1].value=document.getElementById("den_estudios").value;
    _t=document.getElementById("tab_lista_docs");
    if (_t.rows[0].cells.length==1){
        _t.deleteRow(0);
    }
    var nuevaFila = _t.insertRow();

    // Insertar una celda en la nueva fila (primera columna)
    var celda1 = nuevaFila.insertCell();
    celda1.textContent = "("+document.querySelectorAll("input[name=tipo]:checked")[0].value+") "+document.getElementById("den_estudios").value;
    celda1.style.width="50%";

    // Insertar una celda en la nueva fila (segunda columna)
    var celda2 = nuevaFila.insertCell();
    celda2.textContent = _arch;
    celda2.style.width="45%";

    var celda3=nuevaFila.insertCell();
    celda3.innerHTML="<a href='#' style='color:brown;font-weight:bold' onclick='borraFila(this,event)' title='Elimina el documento'>X</a>";
    celda3.style.width="5%";
    celda3.style.textAlign="center";

    document.getElementById("form_anade_documento").reset();
}


function borraFila(obj,e){
    e.preventDefault();
    _t=document.getElementById("tab_lista_docs");
    num_fila=obj.parentNode.parentNode.rowIndex;
    if (_t.rows.length==1){
        _t.innerHTML="<tr><td style='text-align:center'>LISTA DE DOCUMENTOS VACÍA</td></tr>";
    }
    else{
        _t.deleteRow(num_fila);
    }
    
    inputsHidden=document.getElementById("array_input_type_file").querySelectorAll('input[type="hidden"]');
    inputsHidden[num_fila].remove();
    inputsFiles=document.getElementById("array_input_type_file").querySelectorAll('input[type="file"]');
    inputsFiles[num_fila].remove();
}


function ayudaFirma(e){
    e.preventDefault();
    mensaje="- Haz tu firma con bolígrafo negro o azul en un papel en blanco.<br>";
    mensaje+="- Haz una foto de la firma en formato JPEG o JPG.<br>";
    mensaje+="- MUY IMPORTANTE: LA FOTO DEBE HACERSE CON EL MÓVIL EN POSICIÓN HORIZONTAL Y ENCUADRANDO LA FIRMA EN LA FOTOGRAFÍA.<br>";
    mensaje+="- PRESTA ATENCIÓN PARA QUE LA FIRMA QUEDE AJUSTADA AL TAMAÑO DE LA FOTO.<br>";
    mensaje+="- No hagas una fotografía en la que la firma quede muy pequeña en ella.<br>";
    mensaje+="- Si incumples lo anterior, la firma no será válida.<br>"
    mensaje+="- Si crees que la foto de la firma que has subido no es buena, puedes volver a subirla las veces que quieras. Sólo valdrá la última foto de firma añadida.<br>";
    mensaje+="- La fotografía de la firma se incrustará en el fichero PDF que se va a generar en el registro de la solicitud, y después será eliminada del servidor."
    alerta(mensaje,"INSTRUCCIONES FOTOGRAFÍA DE FIRMA",false,700);
}


function registraForm(){
    if (formulario=="Centro-Ministerio"){
        if ($("#form_convalidaciones").validate()) {
            var formData = new FormData();
            formData.append("id_nie",encodeURIComponent(id_nie));
            formData.append("curso",encodeURIComponent(curso));
            formData.append("formulario",encodeURIComponent(formulario));
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
            formData.append("firma",document.getElementById("firma").files[0]);
            datosHidden = document.querySelectorAll('input[name="desc[]"]');
            for (var i = 0; i < datosHidden.length; i++) {
                formData.append("desc[]", encodeURIComponent(datosHidden[i].value));
            }
            datosFiles = document.querySelectorAll('input[name="docs[]"]');
            for (var i = 0; i < datosFiles.length; i++) {
                formData.append("docs[]", datosFiles[i].files[0]);
            }
            $.post({
                url: "php/registracentroministerio.php", 
                data: formData,
                contentType: false,
                processData: false,
                success: function(resp) {
                    if (resp=="servidor") alerta("Hay un problema con el servidor. Inténtelo más tarde.","ERROR SERVIDOR");
                    else if (resp=="database") alerta("Hay un problema en la base de datos. Inténtelo más tarde.","ERROR DB");
                    else if (resp=="error_subida") alerta("El resgistro ha fallado porque no se ha podido subir correctamente alguno de los documentos. Debe intentarlo en otro momento o revisar el formato de los documentos subidos.","ERROR UPLOAD");
                    else if(resp=="ok"){
                        alerta("Solicitud de convalidación registrada correctamente. Puede revisarla en 'Mis Gestiones'", "PROCESO OK", true, 500);
                    } 
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alerta("Error en servidor. Código "+error+"<br>Inténtelo más tarde.","ERROR DE SERVIDOR");
                }
            });
        }
        else {
                alerta ("Revisa los campos que se han marcado en rojo.","DATOS INVÁLIDOS O AUSENTES");
        }
    } 
}

