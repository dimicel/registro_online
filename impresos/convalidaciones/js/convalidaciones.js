var curso;
var id_nie = "";
var nombre = "";
var apellidos = "";
var email = "";
var anno_ini_curso;
var telef_alumno,email_alumno;
var formulario="";



$(document).ready(function() {

    document.body.style.overflowY = "scroll";

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, function(resp) {
        if (resp["error"] != "ok") document.write(resp["error"]);
        else {
            id_nie = resp["id_nie"];
            document.getElementById("id_nie").value = resp["id_nie"];
            nombre = resp["nombre"];
            apellidos = resp["apellidos"];
            email = resp["email"];
            anno_ini_curso = resp["anno_ini_curso"];
            document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            document.getElementById("anno_curso").value = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
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
                telef_alumno=resp.datos.telef_alumno;
                email_alumno=resp.datos.email;
            }
            else{
                telef_alumno='';
                email_alumno='';
            }
        },"json");
    });
    dat3 = dat2.then(() => {
        curso = anno_ini_curso + "-" + (anno_ini_curso + 1);

    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

});



function seleccion(obj){
    formulario=obj.id;
    if (obj.id=="instrucciones"){
        open("instrucciones/instrucciones.pdf","_blank");
    }
    else if (obj.id=="consejeria"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").show();
        $("#seccion-centro_ministerio").hide();
    }
    else if(obj.id=="centro_ministerio"){
        $("#seccion-intro").hide();
        $("#seccion-consejeria").hide();
        $("#seccion-centro_ministerio").show();
        creaValidadorCentroMin()
    }
}

function creaValidadorCentroMin(){

    $("#form_centro_ministerio").validate({
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
            }
        },
        messages: {
            apellidos: {
                required: "Complete el campo"
            },
            nombre: {
                required: "Complete el campo"
            },
            nif_nie: {
                required: "Complete el campo"
            },
            cp: {
                required: "Falta"
            },
            direccion: {
                required: "Complete el campo"
            },
            localidad: {
                required: "Complete el campo"
            },
            provincia: {
                required: "Complete el campo"
            },
            tlf_movil: {
                required: "Complete el campo"
            },
            email: {
                required: "Complete el campo",
                email:"Inválido"
            },
            t_firm: {
                required: "Falta archivo"
            }
        },
        errorPlacement: function(error, element) {
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
    tipoFile=document.createElement("input");
    tipoFile.type="file";
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
    if (formulario=="centro_ministerio"){
       if ($("#form_centro_ministerio").valid()) {

       }
    } 
}

