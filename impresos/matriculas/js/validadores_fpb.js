function creaValidatorPagina1() {
    $("#form_pagina_1").validate({
        rules: {
            sel_ciclos: {
                required: true
            },
            sel_curso: {
                required: true
            },
            nuevo_otra_comunidad:{
                required:true
            }
        },
        messages: {
            sel_ciclos: {
                required: "Seleccione un CICLO FORMATIVO"
            },
            sel_curso: {
                required: "Seleccione un CURSO"
            },
            nuevo_otra_comunidad:{
                required:"Seleccione Sí o No"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == 'nuevo_otra_comunidad') $("#al_nuevo_err").html(error);
            else $(element).next().html(error);
        }
    });
}

function creaValidatorPagina2() {
    $("#fecha_nac").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "dd/mm/yy",
        dayNamesMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
        firstDay: 1,
        monthNames: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
        monthNameShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
        showButtonPanel: true,
        currentText: "Hoy",
        closeText: "Cerrar",
        minDate: "01/01/1900",
        maxDate: "-11y",
        nextText: "Siguiente",
        prevText: "Previo"
    });

    $("#form_pagina_2").validate({
        rules: {
            apellidos: {
                required: true
            },
            nombre: {
                required: true
            },
            localidad_nac: {
                required: true
            },
            fecha_nac: {
                required: true
            },/*
            nif_nie: {
                numero_nif: true
            },*/
            email_alumno: {
                email_no_obligatorio: true
            }
        },
        messages: {
            apellidos: {
                required: "Complete el campo"
            },
            nombre: {
                required: "Complete el campo"
            },
            fecha_nac: {
                required: "Seleccione una fecha"
            },/*
            nif_nie: {
                numero_nif: "Incorrecto"
            },*/
            email_alumno: {
                email_no_obligatorio: "Dirección no válida"
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev($('.errorTxt')).html(error);
        }
    });
}

function creaValidatorPagina3() {
    $("#form_pagina_3").validate({
        rules: {
            direccion: {
                required: true
            },
            cp: {
                required: true
            },
            localidad: {
                required: true
            },
            provincia: {
                required: true
            }
        },
        messages: {
            direccion: {
                required: "Complete el campo"
            },
            cp: {
                required: "Falta"
            },
            localidad: {
                required: "Complete el campo"
            },
            provincia: {
                required: "Complete el campo"
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev($('.errorTxt')).html(error);
        }
    });
}



function creaValidatorPagina4() {
    $("#form_pagina_4").validate({
        rules: {
            foto_alumno: {
                required: true
            },
            resguardo_seguro_escolar: {
                required: true
            },
            anverso_dni: {
                required: true
            },
            reverso_dni: {
                required: true
            }
        },
        messages: {
            foto_alumno: {
                required: "Suba una fotografía del alumno/a"
            },
            resguardo_seguro_escolar: {
                required: "Suba un archivo JPEG con el Resguardo del seguro escolar escaneado"
            },
            anverso_dni: {
                required: "Suba un archivo JPEG con el anverso del documento de identificación (DNI/NIE)"
            },
            reverso_dni: {
                required: "Suba un archivo JPEG con el reverso del documento de identificación (DNI/NIE)"
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().next().children().html(error);
        }

    });
}

function creaValidatorPagina5() {
    $("#form_pagina_5").validate({
        rules: {
            tutor: {
                required: true
            }
        },
        messages: {
            tutor: {
                required: "Complete el campo"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == 'mayor') {
                $("#pag_5_error_radio").html(error);
            } else if ($(element).attr('name') == 'tutor') {
                $("#pag_5_error_tutor").html(error);
            }
        }

    });
}