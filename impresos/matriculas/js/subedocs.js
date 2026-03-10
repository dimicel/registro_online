function muestraEditor(_file,tipo){
    mm="";
    mmtit="";
    _resp="";
    let orientacionActual = 1;
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/matriculas.htm", "div_edita_imagen","EDICIÓN IMAGEN",600,2000,"center center","center center",
        [
            {
                class: "btn btn-success textoboton",
                text: "Girar +90º",
                click: function() {
                    // 1. Calculamos la nueva orientación (Ciclo de 90º en 90º)
                    // Orientaciones Croppie: 1=0º, 6=90º, 3=180º, 8=270º
                    orientacionActual = (orientacionActual === 1) ? 6 : (orientacionActual === 6) ? 3 : (orientacionActual === 3) ? 8 : 1;

                    // 2. Capturamos dimensiones del DOM para invertirlas
                    var vW = _crop1.elements.viewport.offsetWidth;
                    var vH = _crop1.elements.viewport.offsetHeight;
                    var bW = _crop1.elements.boundary.offsetWidth;
                    var bH = _crop1.elements.boundary.offsetHeight;

                    // 3. ¡FUERA TODO! Destruimos para resetear el zoom y los límites
                    _crop1.destroy();
                    var el = document.getElementById("div_imagen");
                    el.innerHTML = ''; 
                    el.className = '';

                    // 4. Creamos la instancia NUEVA con dimensiones ya invertidas
                    _crop1 = new Croppie(el, {
                        viewport: { width: vH, height: vW }, // Invertidos
                        boundary: { width: bH, height: bW }, // Invertidos
                        showZoomer: true,
                        enableOrientation: true
                        //enforceBoundary: false
                    });

                    // 5. Cargamos la imagen original PERO con la orientación guardada
                    _crop1.bind({
                        url: URL.createObjectURL(_file),
                        orientation: orientacionActual,
                        zoom: 0 // Para que se ajuste al mínimo del nuevo viewport
                    }).then(function() {
                        var zoomer = document.querySelector('.cr-slider');
                        zoomer.setAttribute('max', 6.0); 
                    });
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Girar -90º",
                click: function() {
                    // 1. Calculamos la nueva orientación (Ciclo de 90º en 90º)
                    // Orientaciones Croppie: 1=0º, 6=90º, 3=180º, 8=270º
                    orientacionActual = (orientacionActual === 1) ? 8 : (orientacionActual === 8) ? 3 : (orientacionActual === 3) ? 6 : 1;

                    // 2. Capturamos dimensiones del DOM para invertirlas
                    var vW = _crop1.elements.viewport.offsetWidth;
                    var vH = _crop1.elements.viewport.offsetHeight;
                    var bW = _crop1.elements.boundary.offsetWidth;
                    var bH = _crop1.elements.boundary.offsetHeight;

                    // 3. ¡FUERA TODO! Destruimos para resetear el zoom y los límites
                    _crop1.destroy();
                    var el = document.getElementById("div_imagen");
                    el.innerHTML = ''; 
                    el.className = '';

                    // 4. Creamos la instancia NUEVA con dimensiones ya invertidas
                    _crop1 = new Croppie(el, {
                        viewport: { width: vH, height: vW }, // Invertidos
                        boundary: { width: bH, height: bW }, // Invertidos
                        showZoomer: true,
                        enableOrientation: true
                        //enforceBoundary: false
                    });

                    // 5. Cargamos la imagen original PERO con la orientación guardada
                    _crop1.bind({
                        url: URL.createObjectURL(_file),
                        orientation: orientacionActual,
                        zoom: 0 // Para que se ajuste al mínimo del nuevo viewport
                    }).then(function() {
                        var zoomer = document.querySelector('.cr-slider');
                        zoomer.setAttribute('max', 6.0); 
                    });
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    _crop1.destroy();
                    $(this).dialog("destroy").remove();
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
                        formData.append("subido_por","usuario_matricula");
                        if (tipo=="dni_anverso")formData.append("parte","A");
                        else if(tipo=="dni_reverso")formData.append("parte","R");
                        else if(tipo=="dni_pasaporte")formData.append("parte","P");
                        if(tipo=="seguro") formData.append("anno_curso", anno_curso);
                        mostrarPantallaEspera();
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            cache: false
                        })
                        .done(function(resp) {
                            ocultarPantallaEspera();
                            if (resp == "archivo") {
                                mm = "Ha habido un error al subir el archivo.";
                            } else if(resp=="servidor" || resp=="error_db") {
                                mmtit="ERROR SERVIDOR";
                                if (tipo=="foto") mm="La fotografía se ha subido correctamente, pero ha habido un error al grabar la fecha.";
                                else if (tipo=="seguro") mm="El resguardo del seguro escolar se ha subido correctamente, pero ha habido un error al grabar la fecha.";
                                else mm="El documento se ha subido correctament, pero ha habido un error al grabar la fecha.";
                            }
                            else if (resp == "almacenar") {
                                mm = "Ha habido un error al copiar el archivo.";
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
                    $(this).dialog("destroy").remove();
                }
            }
        ]
    )
    .then((dialogo)=>{
        const img = new Image();
        img.src = URL.createObjectURL(_file);

        img.onload = function() {
            const anchoReal = this.width;
            const altoReal = this.height;
            const esHorizontal = anchoReal > altoReal;

            // Variables que configuraremos según el caso
            let vWidth, vHeight, bWidth, bHeight, msg, dialogoW;

            if (tipo == "dni_anverso" || tipo == "dni_reverso"  || tipo == "dni_pasaporte") {
                msg = "Rota, haz zoom y mueve para ajustar la CARA y CUELLO";
                // Mantenemos proporción horizontal de un DNI
                vWidth = 450; vHeight = 285;
                dialogoW = 700;
                _fname_ajax = "dni";
                _f_ajax = id_nie + ((tipo == "dni_anverso" || tipo == "dni_pasaporte")? "-A.jpeg" : "-R.jpeg");
                url = "impresos/matriculas/php/sube_dni.php";
            } 
            else if (tipo == "foto") {
                msg = "Ajusta la imagen al recuadro";
                // Si la foto subida es horizontal, quizás quieras invertir el viewport a 255x190? 
                // Normalmente las fotos de carnet son verticales:
                vWidth = 190; vHeight = 255;
                dialogoW = 500;
                _fname_ajax = "foto";
                _f_ajax = id_nie + ".jpeg";
                url = "impresos/matriculas/php/sube_foto.php";
            } 
            else if (tipo == "seguro") {
                msg = "Ajusta el recuadro al resguardo...";
                // AQUÍ aplicamos el cambio según la imagen real
                if (esHorizontal) {
                    vWidth = 630; vHeight = 350;
                } else {
                    vWidth = 350; vHeight = 630; // Invertimos para vertical
                }
                dialogoW = 1000;
                _fname_ajax = "seguro";
                _f_ajax = id_nie + ".jpeg";
                url = "impresos/matriculas/php/sube_seguro.php";
            }

            // Calculamos Boundary proporcional al Viewport para que no sea gigante
            bWidth = vWidth + 100;
            bHeight = vHeight + 100;

            // Aplicar cambios al DOM
            document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustar la CARA y CUELLO al recuadro";
            $(dialogo).dialog("option", "width", dialogoW);

            // Inicializar Croppie
            ////ELIMINAMOS LA INSTANCIA DE CROPPIE SI EXISTE ANTES DE CREAR UNA NUEVA, PARA EVITAR ERRORES EN CASO DE QUE EL USUARIO HAGA VARIAS SUBIDAS SIN RECARGAR LA PÁGINA
            // 1. Buscamos el elemento
            const el = document.getElementById("div_imagen");

            // 2. Si _crop1 existe, intentamos destruirlo, pero con un try/catch por si acaso
            if (typeof _crop1 !== 'undefined' && _crop1 !== null) {
                try {
                    _crop1.destroy();
                } catch(e) {
                    console.log("Croppie ya estaba medio destruido, seguimos...");
                }
                    
            }
 
            // 3. LIMPIEZA TOTAL: Vaciamos el HTML y quitamos clases de Croppie
            el.innerHTML = ''; 
            el.className = ''; // Esto quita las clases 'croppie-container' que añade la librería
            _crop1 = null;
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            _crop1 = new Croppie(document.getElementById("div_imagen"), {
                viewport: { width: vWidth, height: vHeight },
                boundary: { width: bWidth, height: bHeight },
                showZoomer: true,
                enableOrientation: true,
                //enforceBoundary: false,
                enableZoomer: true
            });

            _crop1.bind({
                url: img.src
            }).then(function() {
                // Esto le dice a Croppie: "Independientemente de lo que creas, 
                // permite que el usuario amplíe hasta 3 veces el tamaño"
                var zoomer = document.querySelector('.cr-slider');
                zoomer.setAttribute('max', 6.0); // Por defecto suele ser 1.5 o 2
            });
        };

        img.onerror = function() {
            ocultarPantallaEspera();
            alerta("Error al cargar la imagen. Inténtalo de nuevo.","ERROR DE CARGA");
        };
            
    })
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });

}

/*function muestraEditor(_file,tipo){
    mostrarPantallaEspera("Cargando ...");
    cargaHTML("html/matriculas.htm", "div_edita_imagen","EDICIÓN IMAGEN",550,2000,"center center","center center",
        [
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
                    $(this).dialog("destroy").remove();
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
                        mostrarPantallaEspera();
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            cache: false
                        })
                        .done(function(resp) {
                            ocultarPantallaEspera();
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
                    $(this).dialog("destroy").remove();
                }
            }
        ]
    )
    .then ((dialogo)=>{
            ocultarPantallaEspera();
            if (tipo=="dni_anverso" || tipo=="dni_reverso"){
                document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustar la CARA y CUELLO al recuadro";
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
                $(dialogo).dialog("option", "width", 700);
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
                $(dialogo).dialog("option", "width", 500);
            }
            else if(tipo=="seguro"){
                document.getElementById("texto_editor_imagen").innerHTML="Rota, haz zoom (con la rueda del ratón) en la imagen, y ajusta el recuadro al resguardo del seguro escolar. NO IMPORTA QUE EL RESGUARDO SE VEA EN HORIZONTAL, si es el caso";
                _crop1=new Croppie(document.getElementById("div_imagen"), {
                    viewport: { width: 630, height: 350 },
                    boundary: { width: 675, height: 500 },
                    showZoomer: false,
                    enableOrientation: true
                });
                _fname_ajax="seguro";
                _f_ajax=id_nie+".jpeg";
                url="php/sube_seguro.php";
                $(dialogo).dialog("option", "width", 1000);
            }
            _crop1.bind({
                url: URL.createObjectURL(_file),
                orientation: 1
            });  
        }
    )
    .catch (error=>{
        ocultarPantallaEspera();
        var msg = "Error en la carga de procedimiento: " + error.status + " " + error.statusText;
        alerta(msg,"ERROR DE CARGA");
    });

}
*/

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
    mostrarPantallaEspera();
    $.ajax({
            url: "php/sube_certificado.php",
            type: 'POST',
            data: datos,
            contentType: false,
            processData: false,
            cache: false
        })
        .done(function(resp) {
            ocultarPantallaEspera();
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