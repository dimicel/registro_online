
function creaValidatorPagina1() {
    $("#fech_nac").datepicker({
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
         minDate: new Date(1990, 0, 1),
         maxDate: "-11y",
         nextText: "Siguiente",
         prevText: "Previo"
     });
     
     $("#form_pagina_1").validate({
         rules: {
            res_bonif:{
                required:true
            },
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
             /*nif_nie: {
                 numero_nif: true
             },*/
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
            }
         },
         messages: {
            res_bonif:{
                required:"Seleccione SÍ o NO"
            },
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
             /*nif_nie: {
                 numero_nif: "Incorrecto"
             },*/
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
            if ($(element).attr('name') == "res_bonif") $("#errBloque1").html(error);
            else  $(element).prev($('.errorTxt')).html(error);
             
         }
     });
 }

 function creaValidatorPagina5() {
     $("#form_pagina_5").validate({
         rules: {
            foto_alumno: {
                existe_imagen: true
             },
             tarjeta_sanitaria: {
                existe_imagen: true
             }
         },
         messages: {
            foto_alumno: {
                existe_imagen: "Suba fotografía del residente"
             },
             tarjeta_sanitaria: {
                existe_imagen: "Suba imagen de la tarjeta sanitaria",
             },
         },
         errorPlacement: function(error, element) {
            if ($(element).attr('name') == "foto_alumno") $("#faltafoto").html(error);
            else if ($(element).attr('name') == "tarjeta_sanitaria") $("#faltatarjeta").html(error);
         }
     });
 }


 
 function creaValidatorPagina6() {
    canvas = document.getElementById('firmaCanvas');
    context = canvas.getContext('2d');
    canvas.addEventListener('mousedown', ev_canvas, false);
    canvas.addEventListener('mousemove', ev_canvas, false);
    canvas.addEventListener('mouseup', ev_canvas, false);
    canvas.addEventListener("mouseout", ev_canvas, false);
    canvas.addEventListener('touchstart', ev_canvas, false);
    canvas.addEventListener('touchmove', ev_canvas, false);
    canvas.addEventListener('touchend', ev_canvas, false);

     $("#form_pagina_6").validate({
         rules: {
             bic: {
                 required: true,
                 bic:true
             },
             iban: {
                 required: true,
                 iban: true
             },
             firma:{
                required: true
             }
         },
         messages: {
             bic: {
                 required: "Complete el campo",
                 bic:"El código BIC no es válido"
             },
             iban: {
                 required: "Complete el campo",
                 iban:"El IBAN no es válido"
             },
             firma:{
                required: "Falta firma para la orden SEPA"
             }
         },
         errorPlacement: function(error, element) {
             $(element).prev($('.errorTxt')).html(error);
         }
     });
 }

 









