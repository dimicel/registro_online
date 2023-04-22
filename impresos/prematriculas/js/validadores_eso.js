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
            $(element).prev().html(error);
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
        maxDate: "-12y",
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
            email_tutor1: {
                email: true
            },
            email_tutor2: {
                email: true
            }
        },
        messages: {
            email_tutor1: {
                email: "No es una dirección correcta"
            },
            email_tutor2: {
                email: "No es una dirección correcta"
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
            sel_curso_act: {
                required: true
            },
            sel_grupo_curso_act: {
                required: true
            }
        },
        messages: {
            sel_curso_act: {
                required: "Seleccione un curso"
            },
            sel_grupo_curso_act: {
                required: "Seleccione un grupo"
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev().html(error);
        }
    });
}

function creaValidatorPagina5_2eso() {
    $("#form_pagina_5_2eso").validate({
        rules: {
            eso2_religion: {
                required: true
            },
            eso2_primer_idioma: {
                required: true
            }
        },
        messages: {
            eso2_religion: {
                required: "Seleccione uno"
            },
            eso2_primer_idioma: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().next($('.errorTxt')).html(error);
        }
    });
}

function creaValidatorPagina5_3eso() {
    $("#form_pagina_5_3eso").validate({
        rules: {
            eso3_religion: {
                required: true
            },
            eso3_primer_idioma: {
                required: true
            }
        },
        messages: {
            eso3_religion: {
                required: "Seleccione uno"
            },
            eso3_primer_idioma: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == "eso3_matematicas")
                $(element).parent().parent().prev().children().next().next($('.errorTxt')).html(error);
            else $(element).parent().parent().next($('.errorTxt')).html(error);
        }
    });
}

function creaValidatorPagina5_3esodiv() {
    $("#form_pagina_5_3esodiv").validate({
        rules: {
            eso3_div_religion: {
                required: true
            }
        },
        messages: {
            eso3_div_religion: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().next($('.errorTxt')).html(error);
        }
    });
}

function creaValidatorPagina5_4eso() {
    $("#form_pagina_5_4eso").validate({
        rules: {
            eso4_modalidad: {
                required: true
            },
            eso4_religion: {
                required: true
            },
            eso4_primer_idioma: {
                required: true
            },
            eso4_tron_op_aplic: {
                required: true
            },
            eso4_tron_op_acad: {
                required: true
            },
            eso4_matematicas:{
                required:true
            },
            eso4_bloque1:{
                required:true
            }
        },
        messages: {
            eso4_modalidad: {
                required: "Seleccione uno"
            },
            eso4_religion: {
                required: "Seleccione uno"
            },
            eso4_primer_idioma: {
                required: "Seleccione uno"
            },
            eso4_tron_op_aplic: {
                required: "Seleccione uno"
            },
            eso4_tron_op_acad: {
                required: "Seleccione uno"
            },
            eso4_matematicas:{
                required: "Seleccione uno"
            },
            eso4_bloque1:{
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr('name') == "eso4_primer_idioma" || $(element).attr('name') == "eso4_matematicas")
                $(element).parent().parent().prev().children().next().next().html(error);
            else if ($(element).attr('name') == "eso4_tron_op_aplic")
                $(element).parent().next().next().children($('.errorTxt')).html(error);
            else if ($(element).attr('name') == "eso4_tron_op_acad")
                $(element).parent().next().next().next().children().html(error);
            else if($(element).attr('name') == "eso4_religion")
                $(element).parent().parent().next().children().html(error);
            else if($(element).attr('name') == "eso4_bloque1")
                $(element).parent().parent().parent().next().children().html(error);
            else $(element).parent().parent().next($('.errorTxt')).html(error);
        }
    });
}