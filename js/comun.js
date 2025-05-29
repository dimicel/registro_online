$(function() {
    if (window.document.addEventListener) {
        window.document.addEventListener("keydown", pulsaTecla, false);
        window.document.addEventListener("keyup", borraTecla, false);
    } else {
        window.document.attachEvent("onkeydown", pulsaTecla);
        window.document.attachEvent("onkeyup", borraTecla);
    }
    String.prototype.miTrim = function() { return this.replace(/^\s+|\s+$/gm, ''); }

    setInterval(() => {
        $.post("php/keep_alive.php", function(){
          //se mantiene la sesion activa haciendo una llamada a keep_alive.php cada 10mn
        });
      }, 600000); // Cada 10 minutos
      
});



function alerta(mensaje, titulo, previo, ancho) {
    let $div = $("#mensaje_div");
    if ($div.length === 0) {
        $("body").append("<div id='mensaje_div'></div>");
        $div = $("#mensaje_div");
    }
    if (typeof(previo) == 'boolean' && previo == true) {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta(true)'/></div>";
    } else {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta()'/></div>";
    }

    if (typeof(ancho) != 'number') ancho = 300;
    if (typeof(duracion) != 'number') duracion = 0;
    $("#mensaje_div").dialog({
        title: titulo.toUpperCase(),
        autoOpen: true,
        draggable: false,
        dialogClass: "alertas no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        width: ancho
    });
}

function cierraAlerta(previo) {
    if (typeof(previo) == 'boolean' && previo == true) {
        window.history.back();
    }
    $("#mensaje_div").dialog("close").dialog("destroy");
    $("#mensaje_div").remove();
}

//Confirmar o no la salida de un formulario
function confirmarSalida() {
    confirmar("El proceso de registro será cancelado y se borrarán los datos del formulario.", "CANCELACIÓN DE PROCESO")
    .then(function(confirmacion) {
        if (confirmacion) {
            window.history.back();
        }
    });
    }


//Función para confirmar una acción, devuelve un booleano. Ancho es opcional y por defecto es 300px
function confirmar(mensaje, titulo, ancho) {
    if (typeof ancho !== 'number') ancho = 300;
    let $div = $("#mensaje_div");
    if ($div.length === 0) {
        $("body").append("<div id='mensaje_div'></div>");
        $div = $("#mensaje_div");
    }
    mensaje+="<hr><div class='mt-3' style='text-align: right;'>";
    mensaje+="<button id='btnAceptar' class='textoboton btn btn-success btn-sm ml-2'>Aceptar</button>";
    mensaje+="<button id='btnCancelar' class='textoboton btn btn-danger btn-sm ml-2'>Cancelar</button>";
    mensaje+="</div>";
    $div.html('<div>'+mensaje+'</div>');
    return new Promise((resolve) => {
        $div.dialog({
            title: titulo.toUpperCase(),
            autoOpen: true,
            modal: true,
            draggable: false,
            dialogClass: "alertas no-close",
            resizable: false,
            width: ancho,
            show: { effect: "fade", duration: 0 },
            hide: { effect: "fade", duration: 0 },
            open: function () {
                // Asocia eventos a los botones internos
                $div.find("#btnAceptar").off("click").on("click", () => {
                    $div.dialog("close").dialog("destroy");
                    $("#mensaje_div").remove();
                    resolve(true);
                });

                $div.find("#btnCancelar").off("click").on("click", () => {
                    $div.dialog("close").dialog("destroy");
                    $("#mensaje_div").remove();
                    resolve(false);
                });
            }
        });
    });
}


function pulsaTecla(evnt) {
    var ev = (evnt) ? evnt : event;
    tecla = (ev.which) ? ev.which : event.keyCode;
    return;
}

function borraTecla() {
    tecla = 0;
}


function retornaValRadioButton(obj) {
    for (i = 0; i < obj.length; i++) {
        if (obj[i].checked) return obj[i].value;
    }
}

function validaDNI_NIE(dni) {
    var numero, le, letra;
    var expresion_regular_dni = /^[XYZ]?\d{5,8}[A-Z]$/;

    dni = dni.toUpperCase();

    if (expresion_regular_dni.test(dni) === true) {
        numero = dni.substr(0, dni.length - 1);
        numero = numero.replace('X', 0);
        numero = numero.replace('Y', 1);
        numero = numero.replace('Z', 2);
        le = dni.substr(dni.length - 1, 1);
        numero = numero % 23;
        letra = 'TRWAGMYFPDXBNJZSQVHLCKET';
        letra = letra.substring(numero, numero + 1);
        if (letra != le) {
            return false;
        } else {
            return true;
        }
    } else {
        return false;
    }
}

jQuery.validator.addMethod("password", function(value, element) {
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/.test(value);
});

//Permite que el pasaporte esté en blanco
jQuery.validator.addMethod("password2", function(value, element) {
    if (value.length==0) return true;
    return /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).*$/.test(value);
});

jQuery.validator.addMethod("numero_nif", function(value, element) {
    if (value.miTrim() == '') return true;
    return /(([X-Z]{1})([-]?)(\d{7})([-]?)([A-Z]{1}))|((\d{8})([-]?)([A-Z]{1}))/.test(value.miTrim());
});


jQuery.validator.addMethod("miFecha", function(value, element) {
    return (/^\d{2}\/\d{2}\/\d{4}$/).test(value);
});

jQuery.validator.addMethod("iban", function(value, element) {
    return validateIBAN(value);
});

jQuery.validator.addMethod("bic", function(value, element) {
    if (value.length==0) return true;
    return validateBIC(value);
});

jQuery.validator.addMethod("email", function(value, element) {
    // Expresión regular para validar un correo electrónico
    var emailPattern = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
    return emailPattern.test(value);
});

jQuery.validator.addMethod("email_no_obligatorio", function(value, element) {
    // Expresión regular para validar un correo electrónico
    if (value.trim()=="") return true;
    var emailPattern = /^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/;
    return emailPattern.test(value);
});


//Limita un grupo de checkbox a que puedan activar si hay menos de un número de ellos activo
function limitCheckboxes(selector, maxCount) {
    const checkboxes = document.querySelectorAll(selector);
  
    checkboxes.forEach((checkbox) => {
      checkbox.addEventListener('click', (e) => {
        const checkedCount = document.querySelectorAll(`${selector}:checked`).length;
  
        if (checkedCount > maxCount) {
          e.preventDefault();
          checkbox.checked = false;
        }
      });
    });
  }

  function validateIBAN(iban) {
    // Lista de longitudes de IBAN por país
    const IBAN_LENGTHS = {
        'AL': 28, 'AD': 24, 'AT': 20, 'AZ': 28, 'BH': 22, 'BY': 28, 'BE': 16, 'BA': 20,
        'BR': 29, 'BG': 22, 'CR': 22, 'HR': 21, 'CY': 28, 'CZ': 24, 'DK': 18, 'DO': 28,
        'EG': 27, 'EE': 20, 'FO': 18, 'FI': 18, 'FR': 27, 'GE': 22, 'DE': 22, 'GI': 23,
        'GR': 27, 'GL': 18, 'GT': 28, 'HU': 28, 'IS': 26, 'IQ': 23, 'IE': 22, 'IL': 23,
        'IT': 27, 'JO': 30, 'KZ': 20, 'XK': 20, 'KW': 30, 'LV': 21, 'LB': 28, 'LI': 21,
        'LT': 20, 'LU': 20, 'MT': 31, 'MR': 27, 'MU': 30, 'MD': 24, 'MC': 27, 'ME': 22,
        'NL': 18, 'MK': 19, 'NO': 15, 'PK': 24, 'PS': 29, 'PL': 28, 'PT': 25, 'QA': 29,
        'RO': 24, 'LC': 32, 'SM': 27, 'SA': 24, 'RS': 22, 'SK': 24, 'SI': 19, 'ES': 24,
        'SE': 24, 'CH': 21, 'TL': 23, 'TN': 24, 'TR': 26, 'UA': 29, 'AE': 23, 'GB': 22,
        'VG': 24
    };
    // Eliminar espacios y convertir a mayúsculas
    iban = iban.replace(/\s+/g, '').toUpperCase();

    // Verificar el código del país
    const countryCode = iban.slice(0, 2);
    if (!IBAN_LENGTHS.hasOwnProperty(countryCode)) {
        return false;
    }

    // Verificar longitud específica del país
    if (iban.length !== IBAN_LENGTHS[countryCode]) {
        return false;
    }

    // Mover los primeros cuatro caracteres al final del string
    let rearrangedIBAN = iban.slice(4) + iban.slice(0, 4);

    // Convertir las letras en números (A = 10, B = 11, ..., Z = 35)
    let numericIBAN = '';
    for (let char of rearrangedIBAN) {
        if (char >= 'A' && char <= 'Z') {
            numericIBAN += (char.charCodeAt(0) - 55).toString();
        } else {
            numericIBAN += char;
        }
    }

    // Verificar si el número es divisible por 97
    let remainder = BigInt(numericIBAN) % BigInt(97);
    return remainder === BigInt(1);
}

function validateBIC(bic) {
    // Eliminar espacios y convertir a mayúsculas
    bic = bic.replace(/\s+/g, '').toUpperCase();

    // Verificar la longitud del BIC (8 o 11 caracteres)
    if (bic.length !== 8 && bic.length !== 11) {
        return false;
    }

    // Verificar que los primeros 4 caracteres sean letras (código del banco)
    if (!/^[A-Z]{4}/.test(bic)) {
        return false;
    }

    // Verificar que los siguientes 2 caracteres sean letras (código del país)
    if (!/^[A-Z]{4}[A-Z]{2}/.test(bic)) {
        return false;
    }

    // Verificar que los siguientes 2 caracteres sean letras o números (código de localidad)
    if (!/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}/.test(bic)) {
        return false;
    }

    // Si hay 3 caracteres adicionales, deben ser letras o números (código de la sucursal)
    if (bic.length === 11 && !/^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}[A-Z0-9]{3}/.test(bic)) {
        return false;
    }

    return true;
}


function cargaHTML(url="",contenido="",titulo="",ancho=600,alto=400,posicion_my="center top",posicion_at="center top",botones=[],funcAbrir=null,funcCerrar=null) {
    return new Promise((resolve, reject) => {
        //Si ancho y alto son 0, se genera el div, se carga el contenido HTML pero no se genera ni se abre el diálogo
        var _d=generaDivDialog();
        if (posicion_my=='') posicion_my="center top";
        if (posicion_at=='') posicion_at="center top";
        if (!_d) {
            reject(new Error("No se pudo crear el diálogo, límite alcanzado"));
            return;
        }
        if(url!=""){
            $("#"+_d).load(url+"?q="+Date.now()+" #"+contenido,function(response, status, xhr) {
                if (status == "error") {
                    var msg = "Error: ";
                    $("#"+_d).html(msg + xhr.status + " " + xhr.statusText);
                    reject(new Error(msg + xhr.status + " " + xhr.statusText));
                } else {
                    if(ancho>0 && alto>0){
                        $("#"+_d).dialog({
                            autoOpen: true,
                            modal: true,
                            draggable: false,
                            resizable: false,
                            title: titulo,
                            dialogClass: "alertas no-close",
                            width: ancho,
                            maxHeight: alto,
                            show: { effect: "fade", duration: 0 },
                            hide: { effect: "fade", duration: 0 },
                            position: { my: posicion_my, at: posicion_at, of: window },
                            buttons: botones,
                            open: function(event, ui) {
                                $(this).css("overflow", "hidden");
                                funcAbrir && funcAbrir();  //Equivalente a  if(funcAbrir!=null) funcAbrir();
                                resolve(this);
                            },
                            close:function(event, ui) {
                                funcCerrar && funcCerrar();
                                $("#"+_d).dialog("destroy").remove();
                            }
                        });
                    }
                }
            });
        }
        else{
            if(ancho>0 && alto>0){
                $("#"+_d).dialog({
                    autoOpen: true,
                    modal: true,
                    draggable: false,
                    resizable: false,
                    title: titulo,
                    dialogClass: "alertas no-close",
                    width: ancho,
                    maxHeight: alto,
                    show: { effect: "fade", duration: 0 },
                    hide: { effect: "fade", duration: 0 },
                    position: { my: posicion_my, at: posicion_at, of: window },
                    buttons: botones,
                    open: function(event, ui) {
                        $(this).css("overflow", "hidden");
                        funcAbrir && funcAbrir();  //Equivalente a  if(funcAbrir!=null) funcAbrir();
                        resolve(this);
                    },
                    close:function(event, ui) {
                        funcCerrar && funcCerrar();
                        $("#"+_d).dialog("destroy").remove();
                    }
                });
            }
        }
    });
}

function generaDivDialog(){
    let di="";
    for (let i=0; i < 1000; i++) {
        if (document.getElementById('div_dialogs' + i) == null) {
            di=document.createElement('div');
            di.id='div_dialogs' + i;
            di.classList.add('ui-widget-header', 'ui-corner-all', 'alertas');
            di.style.overflow='hidden';
            document.body.appendChild(di);
            break;
        }
    }
    return di.id;
}


function generaPass() {
    mayus = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
    minus = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
    nums = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    matriz = [];
    password = "";
    matriz.length = 8;
    matriz[0] = mayus[Math.trunc(Math.random() * (mayus.length - 1))];
    matriz[1] = minus[Math.trunc(Math.random() * (minus.length - 1))];
    matriz[2] = nums[Math.trunc(Math.random() * (nums.length - 1))];
    matriz[3] = mayus[Math.trunc(Math.random() * (mayus.length - 1))];
    matriz[4] = minus[Math.trunc(Math.random() * (minus.length - 1))];
    matriz[5] = nums[Math.trunc(Math.random() * (nums.length - 1))];
    matriz[6] = mayus[Math.trunc(Math.random() * (mayus.length - 1))];
    matriz[7] = minus[Math.trunc(Math.random() * (minus.length - 1))];
    //matriz=matriz.sort(function(){Math.random()-0.5});
    //Desordena el array
    var j, x, i;
    for (i = matriz.length - 1; i > 0; i--) {
        j = Math.floor(Math.random() * (i + 1));
        x = matriz[i];
        matriz[i] = matriz[j];
        matriz[j] = x;
    }
    return matriz.join('');
}

function enviarFormularioSubmit(opciones) {
  const {
    url,
    metodo = "POST",
    datos = {},//puede ser FormData o un objeto JSON, o array bidimensional [["clave", "valor"], ["clave2", "valor2"]]
    target = null // por si quieres abrir en otra pestaña
  } = opciones;

  const form = document.createElement("form");
  form.action = url;
  form.method = metodo.toUpperCase();
  if (target) form.target = target;
  form.style.display = "none";

  const esFormData = datos instanceof FormData;
  const entradas = esFormData ? datos.entries() : Object.entries(datos);

  for (const [clave, valor] of entradas) {
    const input = document.createElement("input");
    input.type = "hidden";
    input.name = clave;
    input.value = valor;
    form.appendChild(input);
  }

  document.body.appendChild(form);
  form.submit();
  document.body.removeChild(form);
}


function mostrarPantallaEspera(mensaje = "Procesando...") {
  if (document.getElementById("pantalla-espera")) return;

  // Crear overlay
  const overlay = document.createElement("div");
  overlay.id = "pantalla-espera";
  Object.assign(overlay.style, {
    position: "fixed",
    top: "0",
    left: "0",
    width: "100vw",
    height: "100vh",
    backgroundColor: "rgba(255, 250, 250, 0.7)",
    display: "flex",
    flexDirection: "column",
    alignItems: "center",
    justifyContent: "center",
    zIndex: "9999",
    fontFamily: "Arial, sans-serif",
    fontSize: "18px"
  });

  // Ruta al script actual y base
  const currentScript = document.currentScript || document.querySelector('script[src*="comun.js"]');
  const scriptBase = new URL('..', currentScript.src).pathname;

  // Imagen GIF
  const gif = document.createElement("img");
  gif.src = scriptBase + 'recursos/espera.gif';
  gif.alt = "Procesando...";
  Object.assign(gif.style, {
    width: "128px",
    height: "128px",
    marginBottom: "12px"
  });

  // Mensaje
  const texto = document.createElement("div");
  texto.textContent = mensaje;
  Object.assign(texto.style, {
    textAlign: "center",
    color: "grey"
  });

  overlay.appendChild(gif);
  overlay.appendChild(texto);
  document.body.appendChild(overlay);
}


function ocultarPantallaEspera() {
  const overlay = document.getElementById("pantalla-espera");
  if (overlay) overlay.remove();
}

function habilitarContextMenuTactil(contenedor, objetivo = 'tr', delay = 600) {
    // Solo en dispositivos táctiles
    if (!('ontouchstart' in window)) return;

    let pressTimer;

    $("#"+contenedor).on('touchstart', objetivo, function(e) {
        const elem = this;
        const touch = e.originalEvent.touches[0];

        pressTimer = setTimeout(function() {
            $(elem).contextMenu({
                x: touch.clientX,
                y: touch.clientY
            });
        }, delay);
    }).on('touchend touchcancel touchmove', objetivo, function() {
        clearTimeout(pressTimer);
    });
}


