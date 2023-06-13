function muestraEditor(_file,tipo){
    if (tipo=="dni_anverso" || tipo=="dni_reverso"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 450, height: 285 },
            boundary: { width: 675, height: 383 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="dni";
        if(tipo=="dni_anverso")_f_ajax=id_nie+"-A.jpeg";
        else _f_ajax=id_nie+"-R.jpeg";
        url="php/sube_dni.php";
        __ancho=700;
    }
    else if(tipo=="foto"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 190, height: 255 },
            boundary: { width: 300, height: 450 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="foto";
        _f_ajax=id_nie+".jpeg";
        url="php/sube_foto.php";
        __ancho=500;
    }
    else if(tipo=="seguro"){
        document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) en la imagen, y ajusta el recuadro al resguardo del seguro escolar.";
        _crop1=new Croppie(document.getElementById("div_imagen"), {
            viewport: { width: 350, height: 630 },
            boundary: { width: 675, height: 675 },
            showZoomer: false,
            enableOrientation: true
        });
        _fname_ajax="seguro";
        _f_ajax=id_nie+".jpeg";
        url="php/sube_seguro.php";
        __ancho=1000;
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
        width: __ancho,
        buttons: [
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
                    _crop1.result({
                        type: 'blob'
                    }).then(function (blob) {
                        return fetch(window.URL.createObjectURL(blob))
                    }).then(function (response) {
                        return response.blob();
                    }).then(function (blob) {
                        formData= new FormData();
                        formData.append(_fname_ajax, blob, _f_ajax);
                        formData.append("id_nie",id_nie);
                        if (tipo=="dni_anverso")formData.append("parte","A");
                        else if(tipo=="dni_reverso")formData.append("parte","R");
                        if(tipo=="seguro") formData.append("anno_curso", anno_curso);
                        document.getElementById("cargando").style.display = 'inherit';
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            cache: false
                        })
                        .done(function(resp) {
                            document.getElementById("cargando").style.display = 'none';
                            if (resp == "archivo") {
                                alerta("Ha habido un error al subir el archivo.", "Error carga");
                                obj.value = null;
                            } else if (resp == "almacenar") {
                                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                                obj.value = null;
                            } else if (resp == "ok") {
                                if (tipo == "dni_anverso"){
                                    mm = "Anverso de documento subido.";
                                    document.getElementById("div_existe_anverso_dni").style.display="inherit";
                                    document.getElementById("div_anverso_dni").style.display="none";
                                }
                                else if (tipo == "dni_reverso"){
                                    mm = "Reverso de documento subido.";
                                    document.getElementById("div_existe_reverso_dni").style.display="inherit";
                                    document.getElementById("div_reverso_dni").style.display="none";
                                }
                                else if (tipo == "foto"){
                                    mm = "Fotografía subida.";
                                    document.getElementById("div_existe_fotografia").style.display="inherit";
                                    document.getElementById("div_fotografia").style.display="none";
                                }
                                else if (tipo == "seguro"){
                                    mm = "Resguardo del pago del seguro escolar subido.";
                                    document.getElementById("div_existe_resguardo_seguro_escolar").style.display="inherit";
                                    document.getElementById("div_resguardo_seguro_escolar").style.display="none";
                                }
                                alerta(mm, "OK");
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


function subeCertificado(obj) {
    if (obj.files[0].type != "application/pdf") {
        obj.value = null;
        alerta("Formato de archivo no válido", "NO VÁLIDO");
        return;
    }

    datos = new FormData();
    datos.append("certificado", obj.files[0]);
    datos.append("id_nie", id_nie);
    datos.append("anno_curso", anno_curso);
    document.getElementById("cargando").style.display = 'inline-block';
    $.ajax({
            url: "php/sube_certificado.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            document.getElementById("cargando").style.display = 'none';
            if (resp == "archivo") {
                alerta("Ha habido un error al subir el archivo.", "Error carga");
                obj.value = null;
            } else if (resp == "almacenar") {
                alerta("Ha habido un error al copiar el archivo.", "Error copia");
                obj.value = null;
            } else if (resp == "ok") {
                document.getElementById("div_existe_certificado").style.display="inherit";
                document.getElementById("div_certificado").style.display="none";
                document.getElementById("prev_certificado").href="../../docs/"+id_nie+"/certificado_notas/"+anno_curso+"/"+id_nie+".pdf?q="+Date();
                alerta("Certificado subido.", "OK");
            }
        });
}