
function creaValidatorPagina1() {
    $("#form_pagina_1").validate({
        rules: {
            curso: {
                required: true
            }
        },
        messages: {
            curso: {
                required: "Seleccione un curso"
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
            },
            email_alumno: {
                email: true
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
            },
            email_alumno: {
                email: "Dirección no válida"
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
            te_nombre_apellidos: {
                required: true
            },
            te_direccion: {
                required: true
            },
            te_localidad: {
                required: true
            },
            te_provincia: {
                required: true
            },
            te_cp: {
                required: true
            },
            te_nif_nie: {
                numero_nif: true
            },
            te_email: {
                email: true
            },
            te_ruta: {
                required: true
            }
        },
        messages: {
            te_nombre_apellidos: {
                required: "Necesita completarse"
            },
            te_direccion: {
                required: "Necesita completarse"
            },
            te_localidad: {
                required: "Necesita completarse"
            },
            te_provincia: {
                required: "Necesita completarse"
            },
            te_cp: {
                required: "Falta"
            },
            te_nif_nie: {
                numero_nif: "Incorrecto"
            },
            te_email: {
                email: "Dirección no válida"
            },
            te_ruta: {
                required: "Seleccione una ruta"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == "te_ruta") {
                $("#error_ruta").html(error);
            } else $(element).prev().html(error);
        }
    });
}


function creaValidatorPagina4() {
    $("#form_pagina_4").validate({
        rules: {
            apartado: {
                required: true
            },
            modalidad: {
                required: true
            }
        },
        messages: {
            apartado: {
                required: "Seleccione un apartado"
            },
            modalidad: {
                required: "Seleccione una modalidad"
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().next().children().html(error);
            /*if ($(element).attr('name') == 'apartado') {
                $(element).parent().parent().next().html(error);
            } else $(element).parent().next().children().html(error);*/
        }
    });
}

function creaValidatorPagina5() {
    $("#form_pagina_5").validate({
        rules: {
            acred_domic: {
                required: true
            },
            acred_iden: {
                required: true
            }
        },
        messages: {
            acred_domic: {
                required: "Seleccione un curso"
            },
            acred_iden: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().next().children().html(error);
        }
    });
}

function creaValidatorPagina6() {
    $("#form_pagina_6").validate({
        rules: { 
        },
        messages: {
        },
        errorPlacement: function(error, element) {
        }
        
    });
}


