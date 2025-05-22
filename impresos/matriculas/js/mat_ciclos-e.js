var _curso;
var pagina = 1;
var id_nie = "";
var id_nif = "";
var nombre = "";
var apellidos = "";
var email = "";
var mes_mat;
var dia_mat;
var anno_ini_curso;
var anno_curso;
var i = 0;
var _paginas = new Array();
var paginas_totales;
var sexo="",fecha_nac="",telef_alumno="",email_alumno="",direccion="",cp="",localidad="",provincia="";
var tutor1="",email_tutor1="",tlf_tutor1="",tutor2="",email_tutor2="",tlf_tutor2="";
var existe_foto=false, existe_dni_A=false, existe_dni_R=false, existe_seguro=false,existe_certificado=false;

var primera_vez_pag_2=true;
var primera_vez_pag_3=true;

let grados = new Array();
let ciclos_gb = new Array();
let ciclos_gm = new Array();
let ciclos_gs = new Array();


$(document).ready(function() {
    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, () => {}, "json"));
    dat2 = dat1.then((res1) => {
        id_nie = res1["id_nie"];
        id_nif = res1["id_nif"];
        nombre = res1["nombre"];
        apellidos = res1["apellidos"];
        email = res1["email"];
        anno_ini_curso = res1["anno_ini_curso"];
        mes_mat = res1["mes"];
        dia_mat = res1["dia"];
        document.getElementById("id_nie").value = id_nie;
        //document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
        if (mes_mat != 6) {
            anno_curso = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            document.getElementById("anno_curso").value = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        } else {
            anno_curso = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
            document.getElementById("anno_curso").value = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
        }
        document.getElementById("email").value = email;
        if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
            document.write("Error datos. Por favor, inténtelo más tarde.");
        }            

        return $.post("../../php/usu_recdatospers.php", {id_nie:id_nie }, () => {}, "json");
    });
    dat3 = dat2.then((resp) => {
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])==="undefined" || resp.datos[e]===null) resp.datos[e]="";
            }
            f_nac=resp.datos.fecha_nac;
            if (f_nac!="")f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
            fecha_nac=f_nac;
            if(sexo=="") sexo=resp.datos.sexo;
            if(telef_alumno=="") telef_alumno=resp.datos.telef_alumno;
            if(email_alumno=="")email_alumno=resp.datos.email;
            if(direccion=="")direccion=resp.datos.direccion;
            if(cp=="")cp=resp.datos.cp;
            if(localidad=="")localidad=resp.datos.localidad;
            if(provincia=="")provincia=resp.datos.provincia;
            if(tutor1=="")tutor1=resp.datos.tutor1;
            if(tlf_tutor1=="")tlf_tutor1=resp.datos.tlf_tutor1;
            if(email_tutor1=="")email_tutor1=resp.datos.email_tutor1;
            if(tutor2=="")tutor2=resp.datos.tutor2;
            if(tlf_tutor2=="")tlf_tutor2=resp.datos.tlf_tutor2;
            if(email_tutor2=="")email_tutor2=resp.datos.email_tutor2;
        }

        return $.post("php/comprueba_docs_matricula.php", { id_nie: id_nie, curso:anno_curso });
    });
    dat4=dat3.then((resp)=>{
        if (resp.indexOf('F')>-1)existe_foto=true;
        else existe_foto=false;
        if (resp.indexOf('A')>-1) existe_dni_A=true;
        else existe_dni_A=false;
        if (resp.indexOf('R')>-1) existe_dni_R=true;
        else existe_dni_R=false;
        if (resp.indexOf('S')>-1) existe_seguro=true;
        else existe_seguro=false;
        if (resp.indexOf('C')>-1) existe_certificado=true;
        else existe_certificado=false;

        $("#pagina_1").load("ciclos-e_html/pagina1.html?q="+Date.now().toString(), function() {
            creaValidatorPagina1();
            $("#pagina_1").fadeIn(500);
            $("[data-paginacion]").html("Pág. 1/6");
            paginas_totales = 6;
        });

        return $.post("../../php/usu_existe_mat.php", { id_nie: id_nie, curso: anno_curso },()=>{},"json");
    });
    dat5=dat4.then((r)=>{
        existe_mat=(r.error=="ok")?true:false;
        if (existe_mat) {
            mensajeNuevaMat = "Ya existe una matrícula registrada.<br>Si continúa el proceso, se eliminará la que tenga ya creada y se sustituirá por ésta.";
            confirmarnuevaMat(mensajeNuevaMat, "MATRÍCULA EXISTENTE", "Crear Nueva");
        }
        return ($.post('../exencion_fct/php/ciclos.php',{},()=>{},"json")); 
    });
    dat6=dat5.then((resp)=>{
        const option = document.createElement("option");
        option.value="";
        option.text="Seleccione uno...";
        option.selected=true;
        document.getElementById("sel_grado").add(option);
        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i]["e-learning"] == 1) {
                existe_grado=false;
                for (let j = 0; j < document.getElementById("sel_grado").options.length; j++) {
                    if (document.getElementById("sel_grado").options[j].value == resp.datos[i].grado) {
                      existe_grado=true;
                    }
                }
                if (!existe_grado){
                    const option = document.createElement("option");
                    option.value = resp.datos[i].grado;
                    option.text =resp.datos[i].grado;
                    document.getElementById("sel_grado").add(option);
                }
            }
        } 
        
        for (i=0; i<resp.datos.length; i++){
            if (resp.datos[i].grado == "BÁSICO" && resp.datos[i]["e-learning"] == 1) {
                ciclos_gb.push(resp.datos[i].denominacion);
            }
            else if (resp.datos[i].grado == "MEDIO" && resp.datos[i]["e-learning"] == 1) {
                ciclos_gm.push(resp.datos[i].denominacion);
            }
            if (resp.datos[i].grado == "SUPERIOR" && resp.datos[i]["e-learning"] == 1) {
                ciclos_gs.push(resp.datos[i].denominacion);
            }
        }
        
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)
    document.getElementById("mat_ciclos").reset();
});


function seleccionCurso() {
    _curso = document.getElementById("sel_ciclos").value;
    if (mes_mat != 6) $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso) + "/" + (anno_ini_curso + 1) + " - " + _curso);
    else $("h7").text("MATRÍCULA para el curso " + (anno_ini_curso + 1) + "/" + (anno_ini_curso + 2) + " - " + _curso);
}



function pasaPagina(p) {
    if (pagina == 1) creaArrayPasapagina();
    if (p == '-') pagina--;
    else if (p == '+') pagina++;
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
    pag = "ciclos-e_html/" + _paginas[pagina - 1][0] + ".html?q="+Date.now().toString();
    pag_html = _paginas[pagina - 1][1];
    valid = _paginas[pagina - 1][2];
    validExec = "#" + _paginas[pagina - 1][3];

    if (p == "+") {
        if ($(validExec).valid()) {
            if (document.getElementById(pag_html).innerHTML.length == 0) {
                $("#" + pag_html).load(pag, function() {
                    if (valid != "") eval(valid);
                    pasaPagina('0');
                });
            } else pasaPagina('0');
        } else{
            pagina--;
            $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
        } 
    } else {
        for (i = 0; i < _paginas.length; i++) $("#" + _paginas[i][1]).css('display', 'none');
        $("#" + pag_html).css('display', 'inherit').fadeIn(500);

        if (pag_html == "pagina_2") {
            document.getElementById("apellidos").value = apellidos;
            document.getElementById("nombre").value = nombre;
            if (id_nif != '') {
                document.getElementById("nif_nie").value = id_nif;
            }
            if (primera_vez_pag_2){
                //form_pagina_2.sexo.value=sexo;
                form_pagina_2.fecha_nac.value=fecha_nac;
                form_pagina_2.telef_alumno.value=telef_alumno;
                form_pagina_2.email_alumno.value=email_alumno;
                primera_vez_pag_2=false;
            }
        }
        else if (pagina==3){
            if (primera_vez_pag_3){
                form_pagina_3.direccion.value=direccion;
                form_pagina_3.cp.value=cp;
                form_pagina_3.localidad.value=localidad;
                form_pagina_3.provincia.value=provincia;
                form_pagina_3.tutor1.value=tutor1;
                form_pagina_3.email_tutor1.value=email_tutor1;
                form_pagina_3.tlf_tutor1.value=tlf_tutor1;
                form_pagina_3.tutor2.value=tutor2;
                form_pagina_3.email_tutor2.value=email_tutor2;
                form_pagina_3.tlf_tutor2.value=tlf_tutor2;
                primera_vez_pag_3=false;
            }
        }
        else if (pag_html == "pagina_4") {
            $("#form_pagina_4").validate().resetForm();
            if (existe_foto){
                $("#div_fotografia").hide();
                $("#div_existe_fotografia").show();
                //document.getElementById("prev_foto").src="../../docs/fotos/"+id_nie+".jpeg?q="+Date();
            }
            if (existe_dni_A){
                $("#div_anverso_dni").hide();
                $("#div_existe_anverso_dni").show();
                //document.getElementById("prev_anverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-A.jpeg?q="+Date();
            }
            if (existe_dni_R){
                $("#div_reverso_dni").hide();
                $("#div_existe_reverso_dni").show();
                //document.getElementById("prev_reverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-R.jpeg?q="+Date();
            }
            if (mayor28()){
                $("#div_resguardo_seguro_escolar").hide();
                $("#div_existe_resguardo_seguro_escolar").hide();
            }
            else {
                if (existe_seguro){
                    $("#div_resguardo_seguro_escolar").hide();
                    $("#div_existe_resguardo_seguro_escolar").show();
                    //document.getElementById("prev_resguardo_seguro").src="../../docs/"+id_nie+"/seguro/"+anno_curso+"/"+id_nie+".jpeg?q="+Date();    
                }
                else{
                    $("#div_resguardo_seguro_escolar").show();
                    $("#div_existe_resguardo_seguro_escolar").hide();
                }
            }
            if(document.getElementById("oc_si").checked){
                if (existe_certificado){
                    $("#div_certificado").hide();
                    $("#div_existe_certificado").show();
                    document.getElementById("prev_certificado").href="../../docs/"+id_nie+"/certificado_notas/"+anno_curso+"/"+id_nie+".pdf?q="+Date();
                }
                else{
                    $("#div_certificado").show();
                    $("#div_existe_certificado").hide();
                }
            }
            else{
                $("#div_certificado").hide();
                $("#div_existe_certificado").hide();
            }
        }
        else if (pag_html == "pagina_5") {
            if (document.getElementById("mayor").checked) seleccMayorMenor("si");
            else if (document.getElementById("menor").checked) seleccMayorMenor("no");
            else seleccMayorMenor("");
        }
    }
}

function cargaImagen(dest){
    if (dest=="prev_foto"){
        document.getElementById("prev_foto").src="../../docs/fotos/"+id_nie+".jpeg?q="+Date();
    }
    else if(dest=="prev_anverso_dni"){
        document.getElementById("prev_anverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-A.jpeg?q="+Date();
    }
    else if(dest=="prev_reverso_dni"){
        document.getElementById("prev_reverso_dni").src="../../docs/"+id_nie+"/dni/"+id_nie+"-R.jpeg?q="+Date();
    }
    else if(dest=="prev_resguardo_seguro"){
        document.getElementById("prev_resguardo_seguro").src="../../docs/"+id_nie+"/seguro/"+anno_curso+"/"+id_nie+".jpeg?q="+Date();
    }
}


function creaArrayPasapagina() {
    _paginas = [];
    _paginas.push(new Array("pagina1", "pagina_1", "creaValidatorPagina1()", ""));
    _paginas.push(new Array("pagina2", "pagina_2", "creaValidatorPagina2()", "form_pagina_1"));
    _paginas.push(new Array("pagina3", "pagina_3", "creaValidatorPagina3()", "form_pagina_2"));
    _paginas.push(new Array("pagina4", "pagina_4", "creaValidatorPagina4()", "form_pagina_3"));
    _paginas.push(new Array("pagina5", "pagina_5", "creaValidatorPagina5()", "form_pagina_4"));
    _paginas.push(new Array("pagina_final", "pagina_6", "", "form_pagina_5"));
}

function registraMatricula() {
    var f = document.getElementById("mat_ciclos");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f5 = document.getElementById("form_pagina_5");

    f._autor_fotos.value=(f5.autor_fotos.checked)?"Si":"No";
    f._nuevo_otra_comunidad.value=(f1.oc_si.checked)?"Si":"No";
    f._fct.value=(f1.fct.checked)?"Si":"No";
    f._proyecto.value=(f1.proyecto.checked)?"Si":"No";
    f.action = "php/regmat_ciclos-e.php";
    f.appendChild(f1.sel_grado);
    f.appendChild(f1.sel_ciclos);
    f.appendChild(f2.apellidos);
    f.appendChild(f2.nombre);
    f.appendChild(f2.fecha_nac);
    f.appendChild(f2.telef_alumno);
    f.appendChild(f2.email_alumno);
    f.appendChild(f2.nif_nie);
    f.appendChild(f3.direccion);
    f.appendChild(f3.cp);
    f.appendChild(f3.localidad);
    f.appendChild(f3.provincia);
    f.appendChild(f5.tutor);
    f.appendChild(f5.autor_fotos);

    mostrarPantallaEspera();

    var pet = $.ajax({
        url: f.action,
        type: "POST",
        data: $("#mat_ciclos").serialize()
    });
    $.when(pet).done(function(resp) {
        ocultarPantallaEspera();
        if (resp == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp.indexOf("registro_erroneo") != -1) {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la prematrícula.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor", true);
        } else if (resp.indexOf("envio_ok") != -1) {
            alerta("Proceso finalizado correctamente.<br>Puede descargar el impreso registrado desde el panel de control del usuario.", "Registro correcto", true);
        } else window.history.back();
    });
}


function seleccMayorMenor(resp) {
    if (resp != '') {
        document.getElementById("div_sin_selec_mayor_menor").style.display = "none";
        document.getElementById("div_texto").style.display = "inline-block";
        document.getElementById("mayor_edad").value = resp;
    }
    if (resp == "si") {
        document.getElementById("mayor_edad").value = "Si";
        document.getElementById("div_selec_mayor").style.display = "inherit";
        document.getElementById("div_selec_menor").style.display = "none";
        //document.getElementById("div_selec_menor_label").style.display = "none";
        _tt = document.getElementById("nombre").value + " " + document.getElementById("apellidos").value;
        if (document.getElementById("nif_nie").value.trim() != "") {
            _tt += ", con NIF/NIE/Pasaporte " + document.getElementById("nif_nie").value;
        }
        _tt += ", como alumno/a del Centro:";
        document.getElementById("texto_autor_mayor").innerHTML = _tt;

    } else if (resp == "no") {
        document.getElementById("mayor_edad").value = "No";
        document.getElementById("div_selec_mayor").style.display = "none";
        document.getElementById("div_selec_menor").style.display = "inherit";
        //document.getElementById("div_selec_menor_label").style.display = "inherit";
    }
}


function labelAutorizacionesMenor(txt) {
    document.getElementById("texto_autor_menor").innerHTML = "D.Dña. " + document.getElementById("tutor").value;
    document.getElementById("texto_autor_menor").innerHTML += ", como tutor/a legal del alumno/a ";
    document.getElementById("texto_autor_menor").innerHTML += document.getElementById("nombre").value + " " + document.getElementById("apellidos").value + ":";
}



function confirmarnuevaMat(mensaje, titulo, botonAceptar) {
    dialogo_id=generaDivDialog();
    document.getElementById(dialogo_id).innerHTML = mensaje;
    $("#"+dialogo_id).dialog({
        title: titulo,
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        width: 600,
        buttons: [{
                class: "btn btn-success textoboton",
                text: botonAceptar,
                click: function() {
                    $(this).dialog("destroy").remove();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("destroy").remove();
                    window.history.back();
                }
            }
        ]
    });
}


function creaSelCiclos(g) {
    if (g != "") {
        if (g == "BÁSICO") {
            sel = "<option value=''>Seleccione uno...</option>";
            for (i=0; i<ciclos_gb.length; i++){
                sel += "<option value='"+ciclos_gb[i]+"'>"+ciclos_gb[i]+"</option>";
            }
            document.getElementById("div_sel_proy").style.display="none";
        }
        if (g == "MEDIO") {
            sel = "<option value=''>Seleccione uno...</option>";
            for (i=0; i<ciclos_gm.length; i++){
                sel += "<option value='"+ciclos_gm[i]+"'>"+ciclos_gm[i]+"</option>";
            }
            document.getElementById("div_sel_proy").style.display="none";
        }
        else if (g == "SUPERIOR") {
            sel = "<option value=''>Seleccione uno...</option>";
            for (i=0; i<ciclos_gs.length; i++){
                sel += "<option value='"+ciclos_gs[i]+"'>"+ciclos_gs[i]+"</option>";
            }
            document.getElementById("div_sel_proy").style.display="inherit";
        }
        document.getElementById("sel_ciclos").innerHTML=sel;
        seleccionCurso();
    }
    else {
        document.getElementById("sel_ciclos").innerHTML="<option value=''>Seleccione uno...</option>";
        document.getElementById("div_sel_proy").style.display="none";
    }
}


function mayor28() {
    anno_nac = parseInt(document.getElementById("fecha_nac").value.substr(6, 4));
    if (mes_mat != 6) anno_limit = anno_ini_curso;
    else anno_limit = anno_ini_curso + 1;
    edad = (anno_limit + 1900) - anno_nac;
    if (edad > 1900) {
        edad -= 1900;
    }
    if (edad < 28) return false;
    else return true;
}


function muestraAyudaDocs(){
    var mensaje_docs = "<p>Los documentos y sus formatos son los siguientes:";
    mensaje_docs += "<ul>";
    mensaje_docs += "    <li>Fotografía del alummno: en formato JPEG tomada con móvil en vertical y fondo blanco, como se muestra en la imagen:<br><center><img src='../../recursos/foto_carne.jpg'  style='width:128px;'></center></li>";
    mensaje_docs += "    <li>Fotografía del anverso y reverso del documento de identificación (DNI/NIE). Si sólo tiene pasaporte, el anverso será imagen JPEG de la página en la que salen los datos del alumno y su fotografía, y el reverso imagen JPEG en blanco. El documento se fotografiará con el móvil en horizontal y fondo blanco, por ejemplo, poniendo el documento sobre un folio en blanco.</li>";
    mensaje_docs += "    <li>EXCEPTO nacidos antes del 31/12/" + (anno_ini_curso-27) + ", una fotografía del resguardo del pago del seguro escolar, y del anverso y reverso del documento de identificación (DNI/NIE). (Móvil en horizontal y fondo blanco, por ejemplo, sobre un folio).</li>";
    mensaje_docs += "    <li>Si es alumno nuevo e inició los estudios de los que se matricula en otra comunidad autónoma, certificado de notas en formato PDF (puede escanearlo, por ejemplo, con la aplicación gratuita para móvil Microsoft Office Lens).</li>";
    mensaje_docs += "</ul>";
    mensaje_docs += "</p>";
    var dialogo_id=generaDivDialog();
    document.getElementById(dialogo_id).innerHTML = mensaje_docs;
    $("#"+dialogo_id).dialog({
    autoOpen: true,
    dialogClass: "alert no-close",
    modal: true,
    hide: { effect: "fade", duration: 0 },
    resizable: false,
    show: { effect: "fade", duration: 0 },
    title: "AYUDA FORMATOS DOCUMENTOS",
    maxHeight: 850,
    width: 850,
    buttons: [{
        class: "btn btn-success textoboton",
        text: "Cerrar",
        click: function() {
            $(this).dialog("destroy").remove(); 
        }
    }]
});
}
