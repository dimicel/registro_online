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
            tutor1:{
                required:true
            },
            tlf_tutor1:{
                required:true
            },
            email_tutor1: {
                email: true
            },
            email_tutor2: {
                email: true
            }
        },
        messages: {
            tutor1:{
                required:"Requerido"
            },
            tlf_tutor1:{
                required:"Requerido"
            },
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
                required: "Seleccione el curso actual"
            },
            sel_grupo_curso_act: {
                required: "Seleccione el grupo"
            }
        },
        errorPlacement: function(error, element) {
            $(element).prev($('.errorTxt')).html(error);
        }
    });
}

jQuery.validator.addMethod("b1_obligatorias2", function(value, element) {
    var lista=$("[name='"+$(element).attr('name')+"']");
    clic=0;
    for (i=0;i<lista.length;i++){
        if(lista[i].checked) clic++;
    }
    console.log(clic);
    if (clic<2) return false;
    else return true;
});

function creaValidatorPagina5_1() {
    $("#form_pagina_5_1").validate({
        rules: {
            b1_modalidad: {
                required: true
            },
            b1_primer_idioma: {
                required: true
            },
            
            b1_religion: {
                required: true
            },
            oblig1_h:{
                required:true
            },
            o2c:{
                b1_obligatorias2:true
            },
            o2h:{
                b1_obligatorias2:true
            },
            o2g:{
                b1_obligatorias2:true
            }
        },
        messages: {
            b1_modalidad: {
                required: "Seleccione una modalidad"
            },
            b1_primer_idioma: {
                required: "Seleccione un idioma"
            },
            
            b1_religion: {
                required: "Seleccione una de las dos"
            },
            oblig1_h:{
                required:"Seleccione una de las dos obligatorias"
            },
            o2c:{
                b1_obligatorias2:"Debe marcar 2 materias"
            },
            o2h:{
                b1_obligatorias2:"Debe marcar 2 materias"
            },
            o2g:{
                b1_obligatorias2:"Debe marcar 2 materias"
            }
        },
        errorPlacement: function(error, element) {
            nombre=$(element).attr('name');
            if (nombre=='oblig1_h'){
                $(element).parent().parent().next().next().children($('.errorTxt')).html(error);
            }
            else if(nombre=='o2c' || nombre=='o2h' || nombre=='o2g'){
                $(element).parent().parent().parent().parent().next().children($('.errorTxt')).html(error);
            }
            else{
                $(element).parent().parent().next().children($('.errorTxt')).html(error);
            }
        }
    });
}



function creaValidatorPagina5_2c() {
    $("#form_pagina_5_2c").validate({
        rules: {
            b2c_primer_idioma: {
                required: true
            },
            b2c_itin: {
                required: true
            },
            b2c_op1: {
                required: true
            },
            b2c_op2: {
                required: true
            }
        },
        messages: {
            b2c_primer_idioma: {
                required: "Seleccione uno"
            },
            b2c_itin: {
                required: "Seleccione uno"
            },
            b2c_op1: {
                required: "Seleccione uno"
            },
            b2c_op2: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr("name") == "b2c_primer_idioma") $(element).parent().parent().parent().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name") == "b2c_itin") $(element).parent().parent().parent().parent().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name") == "b2c_op1") $(element).parent().parent().next().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name") == "b2c_op2") $(element).parent().parent().next().children($('.errorTxt')).html(error);
        }
    });
}

function creaValidatorPagina5_2hcs() {
    $("#form_pagina_5_2hcs").validate({
        rules: {
            b2h_primer_idioma: {
                required: true
            },
            b2h_itin: {
                required: true
            },
            b2h_to1: {
                required: true
            },
            b2h_op1: {
                required: true
            },
            b2h_op2: {
                required: true
            }
        },
        messages: {
            b2h_primer_idioma: {
                required: "Seleccione uno"
            },
            b2h_itin: {
                required: "Seleccione uno"
            },
            b2h_to1: {
                required: "Seleccione uno"
            },
            b2h_op1: {
                required: "Seleccione uno"
            },
            b2h_op2: {
                required: "Seleccione uno"
            }
        },
        errorPlacement: function(error, element) {
            if ($(element).attr("name")=="b2h_primer_idioma") $(element).parent().parent().parent().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name")=="b2h_itin") $(element).parent().parent().parent().parent().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name")=="b2h_to1") $(element).parent().parent().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name")=="b2h_op1") $(element).parent().parent().next().next().children($('.errorTxt')).html(error);
            else if ($(element).attr("name")=="b2h_op2") $(element).parent().parent().next().children($('.errorTxt')).html(error);
            
        }
    });
}