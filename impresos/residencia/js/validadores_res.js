
function creaValidatorPagina1() {
    /*$("#fecha_nac").datepicker({
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
     });*/
     
     $("#form_pagina_1").validate({
         rules: {
             apellidos: {
                 required: true
             },
             nombre: {
                 required: true
             },
             tlf_urgencias:{
                required: true
             },
             localidad_nac: {
                 required: true
             },
             fecha_nac: {
                 required:true
             },
             edad: {
                required:true
            },
            num_hermanos: {
                required:true
            },
             nif_nie: {
                 numero_nif: true
             },
            lugar_hermanos: {
                required:true
            },
            direccion: {
                required:true
            },
            localidad: {
                required:true
            },
            provincia: {
                required:true
            },
            cp: {
                required:true
            },
           
    
         },
         messages: {
             apellidos: {
                 required: "Complete el campo"
             },
             nombre: {
                 required: "Complete el campo"
             },
             tlf_urgencias:{
                required: "Complete el campo"
             },
             fecha_nac: {
                 required: "Introduzca una fecha"
             },
             edad: {
                required: "Falta"
            },
            num_hermanos: {
                required: "Falta"
            },
             nif_nie: {
                 numero_nif: "Incorrecto"
             },
            lugar_hermanos: {
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
            cp: {
                required: "Falta"
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
                email: true
            },
            email_tutor2: {
                email: true
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
            },
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

