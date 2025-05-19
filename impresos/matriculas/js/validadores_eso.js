function creaValidatorPagina1() {
    $("#form_pagina_1").validate({
        rules: {
            curso: {
                required: true
            },
            alumno_nuevo: {
                required: true
            },
            repetidor: {
                required: true
            },
            consolida_prem:{
                required:true
            }
        },
        messages: {
            curso: {
                required: "Seleccione un curso"
            },
            alumno_nuevo: {
                required: "Seleccione uno"
            },
            repetidor: {
                required: "Seleccione uno"
            },
            consolida_prem:{
                required:"Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') != 'curso') {
                $(element).parent().next().next().next($('.errorTxt')).html(error);
            } else $(element).prev().html(error);
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
        minDate: new Date(2000, 0, 1),
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
            sexo: {
                required: true
            },
            localidad_nac: {
                required: true
            },
            fecha_nac: {
                required: true,
                miFecha: true
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
            sexo: {
                required: "Falta"
            },
            fecha_nac: {
                required: "Seleccione una fecha",
                miFecha: "Formato incorrecto"
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
            },
            email_tutor1: {
                email_no_obligatorio: true
            },
            email_tutor2: {
                email_no_obligatorio: true
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
            },
            email_tutor1: {
                email_no_obligatorio: "No es una dirección correcta"
            },
            email_tutor2: {
                email_no_obligatorio: "No es una dirección correcta"
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
            },/*
            anverso_dni: {
                required: true
            },
            reverso_dni: {
                required: true
            },*/
            certificado:{
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
            },
            certificado: {
                required: "Suba el certificado de notas en formato PDF"
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
            tutor:{
                required:true
            }
        },
        messages: {
            tutor:{
                required:"Complete el campo"
            }
        },
        errorPlacement: function(error, element) {
            $(element).next().html(error);
        }
        
    });
}

