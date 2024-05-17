var pagina = 1;
var i = 0;
var _paginas = new Array();
var paginas_totales=6;
existe_tarjeta_san=false;
existe_foto=false;
var id_nie = "";
var id_nif = "";
var nombre = "";
var apellidos = "";
var email = "";
var anno_curso;
var nif_nie_tutor1 = "";
var nif_nie_tutor2 = "";
var sexo="",fecha_nac="",telef_alumno="",email_alumno="",domicilio="",cp="",localidad="",provincia="";
var tutor1="",email_tutor1="",tlf_tutor1="",tutor2="",email_tutor2="",tlf_tutor2="";
var primera_vez_pag_3=true;
var primera_vez_pag_5=true;


$(document).ready(function() {
    document.getElementById("cargando").style.display = 'inherit';
    $("#pagina_1").load("res_html/pagina1.html?q="+Date.now().toString(), function() {
        creaValidatorPagina1();
        $("#pagina_1").show();
        $("[data-paginacion]").html("Pág. 1/6");

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
            document.getElementById("id_nie").value=id_nie;
            //document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
            if (mes_mat == 6) anno_ini_premat = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
            else if (mes_mat >= 7 && mes_mat <= 9) anno_ini_premat = (anno_ini_curso - 1) + "-" + (anno_ini_curso);
            
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
            document.getElementById("nombre").value=nombre;
            document.getElementById("apellidos").value=apellidos;
    
            return $.post("../../php/usu_recdatospers.php", {id_nie:id_nie }, () => {}, "json");
        });
        dat3 = dat2.then((resp) => {
            if (resp.error=="ok"){
                for (e in resp.datos){
                    if(typeof(resp.datos[e])==="undefined" || resp.datos[e]===null) resp.datos[e]="";
                }
                f_nac=resp.datos.fecha_nac;
                if (f_nac!=""){
                    f_nac=f_nac.substr(8,2)+"/"+f_nac.substr(5,2)+"/"+f_nac.substr(0,4);
                    document.getElementById("fech_nac").value=f_nac;
                }
                //fecha_nac=f_nac;
                //if(sexo=="") sexo=resp.datos.sexo;
                if(telef_alumno=="") telef_alumno=resp.datos.telef_alumno;
                if(email_alumno=="")email_alumno=resp.datos.email;
                if(domicilio=="")domicilio=resp.datos.direccion;
                if(cp=="")cp=resp.datos.cp;
                if(localidad=="")localidad=resp.datos.localidad;
                if(provincia=="")provincia=resp.datos.provincia;
                if(tutor1=="")tutor1=resp.datos.tutor1;
                if(tlf_tutor1=="")tlf_tutor1=resp.datos.tlf_tutor1;
                if(email_tutor1=="")email_tutor1=resp.datos.email_tutor1;
                if(tutor2=="")tutor2=resp.datos.tutor2;
                if(tlf_tutor2=="")tlf_tutor2=resp.datos.tlf_tutor2;
                if(email_tutor2=="")email_tutor2=resp.datos.email_tutor2;
                document.getElementById("tlf_alum").value=telef_alumno;
                document.getElementById("email_alumno").value=email_alumno;
                document.getElementById("direccion").value=domicilio;
                document.getElementById("localidad").value=localidad;
                document.getElementById("provincia").value=provincia;
                document.getElementById("cp").value=cp;
                
            }
            return $.post("php/comprueba_docs.php", { id_nie: id_nie, curso:anno_curso });
        });
        dat3.then((resp)=>{
            if (resp.indexOf('F')>-1)existe_foto=true;
            else existe_foto=false;
            if (resp.indexOf('T')>-1) existe_tarjeta_san=true;
            else existe_tarjeta_san=false;
        });
        document.getElementById("cargando").style.display = 'none';
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

function pasaPagina(p) {
    if (pagina == 1) creaArrayPasapagina();
    if (p == '-') pagina--;
    else if (p == '+') pagina++;
    $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
    pag = "res_html/" + _paginas[pagina - 1][0] + ".html?q="+Date.now().toString();
    pag_html = _paginas[pagina - 1][1];
    //valid = _paginas[pagina - 1][2];
    //validExec = "#" + _paginas[pagina - 1][3];
    if (p == "+") {
        if (pag_html=="pagina_2") validacion=$("#form_pagina_1").valid();
        else validacion=true;
        alert(validacion);
        if (validacion) {
            alert(pag_html+"    "+pag);
            if (document.getElementById(pag_html).innerHTML.length == 0) {
                $("#" + pag_html).load(pag, function() {
                    if (pag_html=="pagina_1"){
                        //if (valid != "") eval(valid);
                        creaValidatorPagina1();
                        pasaPagina('0');
                    }
                });
            } else pasaPagina('0');
        } else {
            pagina--;
            $("[data-paginacion]").html("Pág. " + pagina + "/" + paginas_totales);
        }
    } else {
        for (i = 0; i < _paginas.length; i++) $("#" + _paginas[i][1]).css('display', 'none');
        $("#" + pag_html).css('display', 'inherit').fadeIn(500);
        if (pag_html == "pagina_3"){
            if (primera_vez_pag_3){
                primera_vez_pag_3=false;
                document.getElementById("tut1_nom").value=tutor1;
                document.getElementById("tut1_telef").value=tlf_tutor1;
                document.getElementById("tut1_email").value=email_tutor1;
                document.getElementById("tut2_nom").value=tutor2;
                document.getElementById("tut2_telef").value=tlf_tutor2;
                document.getElementById("tut2_email").value=email_tutor2;
            }
        }
        else if (pag_html=="pagina_5"){
            if (primera_vez_pag_5){
                primera_vez_pag_5=false;
                if (existe_foto){
                    document.getElementById("img_foto").src="../../../docs/fotos/"+$id_nie+".jpeg";
                }
                if (existe_tarjeta_san){
                    document.getElementById("img_tarjeta_sanitaria").src="../../../docs/"+$id_nie+"/tarjeta_sanitaria/ts_"+$id_nie+".jpeg";
                }
            }
        }
    }
}


function creaArrayPasapagina() {
    _paginas = [];
    //_paginas.push(new Array("pagina1", "pagina_1","", "creaValidatorPagina1()", ""));
    //_paginas.push(new Array("pagina2", "pagina_2", "", "form_pagina_1"));
    _paginas.push(new Array("pagina1", "pagina_1"));
    _paginas.push(new Array("pagina2", "pagina_2"));
    _paginas.push(new Array("pagina3", "pagina_3"));
    _paginas.push(new Array("pagina4", "pagina_4"));
    _paginas.push(new Array("pagina5", "pagina_5"));
    _paginas.push(new Array("pagina_final", "pagina_6"));
}


function registraSolicitud() {
    var f = document.getElementById("residencia");
    var f1 = document.getElementById("form_pagina_1");
    var f2 = document.getElementById("form_pagina_2");
    var f3 = document.getElementById("form_pagina_3");
    var f4 = document.getElementById("form_pagina_4");
    var f5 = document.getElementById("form_pagina_5");
    f.action = "php/generapdf.php";
    f.appendChild(f1.nombre);
    f.appendChild(f1.apellidos);
    f.appendChild(f1.nif_nie);
    f.appendChild(f1.tlf_urgencias);
    f.appendChild(f1.fech_nac);
    f.appendChild(f1.edad);
    f.appendChild(f1.num_hermanos);
    f.appendChild(f1.lugar_hermanos);
    f.appendChild(f1.tlf_alum);
    f.appendChild(f1.email_alumno);
    f.appendChild(f1.num_ss);
    f.appendChild(f1.direccion);
    f.appendChild(f1.localidad);
    f.appendChild(f1.provincia);
    f.appendChild(f1.cp);
    f.appendChild(f2.estudios);
    f.appendChild(f2.tutor);
    f.appendChild(f2.centro_est);
    f.appendChild(f2.tlf_centro_est);
    f.appendChild(f2.email_centro_est);
    f.appendChild(f2.centro_proc);
    f.appendChild(f2.tlf_centro_proc);
    f.appendChild(f2.email_centro_proc);
    f.appendChild(f3.tut1_nom);
    f.appendChild(f3.tut1_profesion);
    f.appendChild(f3.tut1_estudios);
    f.appendChild(f3.tut1_telef);
    f.appendChild(f3.tut1_email);
    f.appendChild(f3.tut2_nom);
    f.appendChild(f3.tut2_profesion);
    f.appendChild(f3.tut2_estudios);
    f.appendChild(f3.tut2_telef);
    f.appendChild(f3.tut2_email);
    f.appendChild(f4.enfermedad_pasada);
    f.appendChild(f4.enfermedad);
    f.appendChild(f4.medicacion);
    f.appendChild(f4.alergias);
    f.appendChild(f4.otros_datos);

    document.getElementById("cargando").style.display = 'inherit';
    document.getElementById("residencia").submit();
    document.getElementById("cargando").style.display = 'none';
    
    /*
    $.post("php/generapdf.php",$("#residencia").serialize(),(r2)=>{
        document.getElementById("cargando").style.display = 'none';
        if (r2 == "servidor") {
            mensaje = "Ha habido un problema en el servidor. No se puede realizar el registro de la solicitud.<br>Por favor, vuelva a intentarlo más tarde.";
            alerta(mensaje, "Error de servidor");
        }
    });
    */
}

function cargaTarjeta(){
    document.getElementById('tarjeta_sanitaria').value='';
    document.getElementById('tarjeta_sanitaria').click();
}

function cargaFoto(){
    document.getElementById('foto_alumno').value='';
    document.getElementById('foto_alumno').click();
}


function muestraEditor(_file,tipo){
    if(tipo=='foto'){
        tipo='foto';
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 190, height: 255 },
            boundary: { width: 300, height: 450 },
            showZoomer: false,
            enableOrientation: true
        });
        _url="php/subefoto.php";
    }
    else{
        tipo='tarjeta';
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 450, height: 285 },
            boundary: { width: 675, height: 383 },
            showZoomer: false,
            enableOrientation: true
        });
        _url="php/subetarjeta.php";
    }
    
    
    _crop1.bind({
        url: URL.createObjectURL(_file),
        orientation: 1
    });

    $("#div_edita_imagen").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "EDICIÓN IMAGEN",
        width: 700,
        buttons: [
            {
                class: "btn btn-success textoboton",
                text: "Girar +90º",
                click: function() {
                    _crop1.rotate(-90);
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Girar -90º",
                click: function() {
                    _crop1.rotate(90);
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    _crop1.destroy();
                    $("#div_edita_imagen").dialog("close");
                    $("#div_edita_imagen").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    _crop1.result('blob').then(function (blob) {
                        if (tipo=='foto')document.getElementById("img_foto").src=window.URL.createObjectURL(blob);
                        else document.getElementById("img_tarjeta_sanitaria").src=window.URL.createObjectURL(blob);
                        tiempoUnix = new Date().getTime();
                        numeroAleatorio = Math.floor(Math.random() * 1000);
                        nombre_fichero=tipo+'_'+tiempoUnix+'_'+numeroAleatorio+".jpg";
                        formData= new FormData();
                        if(tipo=='foto'){
                            formData.append("foto_alumno", blob, nombre_fichero);
                            document.getElementById("nombre_foto").value=nombre_fichero;
                        }
                        else {
                            formData.append("tarjeta_sanitaria", blob, nombre_fichero);
                            document.getElementById("nombre_tarjeta").value=nombre_fichero;
                        }
                        document.getElementById("cargando").style.display = 'inherit';
                        $.ajax({
                            url: _url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            cache: false
                        }).done(function(resp) {
                            document.getElementById("cargando").style.display = 'none';
                            if (resp == "archivo") {
                                alerta("Ha habido un error al subir la imagen.", "Error carga");
                            } else if (resp == "almacenar") {
                                alerta("Ha habido un error al copiar la imagen.", "Error copia");
                            } 
                        });

                    });
                   _crop1.destroy();
                    $("#div_edita_imagen").dialog("close");
                    $("#div_edita_imagen").dialog("destroy");
                }
            }   
        ]
    });
}



function limitarDigitos(input, maxDigits) {
    // Obtener el valor actual del input como número
    var valor = parseFloat(input.value);

    // Verificar si el valor es un número válido
    if (!isNaN(valor)) {
        // Convertir el valor a una cadena
        var valorCadena = valor.toString();

        // Contar el número de dígitos en la cadena
        var numDigitos = valorCadena.length;

        // Si el número de dígitos supera el límite, recortarlo
        if (numDigitos > maxDigits) {
            valorCadena = valorCadena.slice(0, maxDigits);
            // Actualizar el valor del input
            input.value = parseFloat(valorCadena);
        }
    }
}


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
