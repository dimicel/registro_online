var i = 0;
var id_nie = "";
var anno_curso;
var titular_iban="", iban="", bic="";
var drawing = false;
var mouseX, mouseY;
var canvas, context, tool, canvas_upload;



$(document).ready(function() {
    document.getElementById("cargando").style.display = '';
    canvas = document.getElementById('firmaCanvas');
    context = canvas.getContext('2d');
    canvas.addEventListener('mousedown', ev_canvas, false);
    canvas.addEventListener('mousemove', ev_canvas, false);
    canvas.addEventListener('mouseup', ev_canvas, false);
    canvas.addEventListener("mouseout", ev_canvas, false);
    canvas.addEventListener('touchstart', ev_canvas, false);
    canvas.addEventListener('touchmove', ev_canvas, false);
    canvas.addEventListener('touchend', ev_canvas, false);

    dat1 = Promise.resolve($.post("../../php/sesion.php", { tipo_usu: "usuario" }, () => {}, "json"));
    dat2 = dat1.then((res1) => {
        id_nie = res1["id_nie"];
        anno_ini_curso = res1["anno_ini_curso"];
        mes_mat = res1["mes"];
        dia_mat = res1["dia"];
        //document.getElementById("rotulo_curso").innerHTML = "CURSO ACTUAL - " + anno_ini_curso + "/" + (anno_ini_curso + 1);
        if (mes_mat == 6) anno_ini_premat = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        else if (mes_mat >= 7 && mes_mat <= 9) anno_ini_premat = (anno_ini_curso - 1) + "-" + (anno_ini_curso);
        
        if (mes_mat != 6) {
            anno_curso = (anno_ini_curso) + "-" + (anno_ini_curso + 1);
        } else {
            anno_curso = (anno_ini_curso + 1) + "-" + (anno_ini_curso + 2);
        }
        if (id_nie.trim() == "" || anno_ini_curso.toString().trim() == "") {
            alerta("Error datos. Por favor, inténtelo más tarde.","ERROR");
            window.history.back();
        }

        return $.post("php/datos_residente.php", {id_nie:id_nie, curso:anno_curso }, () => {}, "json");
    });
    dat3 = dat2.then((resp) => {
        if (resp.error=="ok"){
            for (e in resp.datos){
                if(typeof(resp.datos[e])==="undefined" || resp.datos[e]===null) resp.datos[e]="";
            }
            
            if(titular_iban=="") titular_iban=resp.datos.titular_iban;
            if(iban=="")iban=resp.datos.iban;
            if(bic=="")bic=resp.datos.bic;
            document.getElementById("titular_cuenta").value=titular_iban;
            document.getElementById("iban").value=iban;
            document.getElementById("bic").value=bic;
            document.getElementById("registro").value=resp.datos.registro;
            document.getElementById("direccion").value=resp.datos.direccion;
            document.getElementById("cp").value=resp.datos.cp;
            document.getElementById("localidad").value=resp.datos.localidad;
            document.getElementById("provincia").value=resp.datos.provincia;
        }
        else if(resp="error"=="no_inscrito"){
            alerta("El usuario no está inscriopt en la residencia (internado).","NO RESIDENTE");
            window.history.back();
        }
        else if(resp="error"=="bonificado"){
            alerta("El residente es BONIFICADO, y por lo tanto no necesita crear una orden SEPA.","RESIDENTE BONIFICADO");
            window.history.back();
        }
        document.getElementById("cargando").style.display = 'none';
    });

    $('[data-toggle="tooltip"]').tooltip(); //Inicializa todos los tooltips (bootstrap)

});

function confirmar() {
    document.getElementById('mensaje_div').innerHTML = "El proceso de registro será cancelado y se borrarán los datos del formulario.";
    $("#mensaje_div").dialog({
        title: "CANCELACIÓN DE PROCESO",
        autoOpen: false,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    $(this).dialog("close");
                    window.history.back();
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    $(this).dialog("close");
                    return false;
                }
            }
        ]
    });

    $("#mensaje_div").dialog('open');
}

function registraSolicitud() {
    if (!$("#sepa").valid()) return;
    document.getElementById("cargando").style.display = '';
    $.ajax({
            url: 'php/generapdf.php',
            method: 'POST',
            data: $("#residencia").serialize(),
            dataType: 'json',
            success: function(response) {
                document.getElementById("cargando").style.display = 'none';
                if (response.status === 'ok') {
                    var pdfBase64 = response.pdf;
                    var pdfData = atob(pdfBase64); // Decodificar base64
                    var arrayBuffer = new ArrayBuffer(pdfData.length);
                    var uintArray = new Uint8Array(arrayBuffer);
                    
                    for (var i = 0; i < pdfData.length; i++) {
                        uintArray[i] = pdfData.charCodeAt(i);
                    }
        
                    var blob = new Blob([uintArray], { type: 'application/pdf' });
                    var link = document.createElement('a');
                    link.href = window.URL.createObjectURL(blob);
                    //link.target='_blank';
                    link.download = 'inscripcion_residencia_'+id_nie+'.pdf';
                    link.click();
        
                    console.log('PDF descargado correctamente.');
                    alerta("Procedimiento terminado correctamente.<br>En descargas tienes el formulario con los datos de salud que no se han grabado.<br>Guárdalo por si lo solicitan desde Jefatura de Residencia.","CORRECTO",true);
                }
                else if(response.status=="server") {
                    alerta("Hay problemas en el servidor. Inténtelo en otro momento.","ERROR EN SERVIDOR",true);
                    console.error('Error:', response.message);
                }
                else if(response.status=="db"){
                    alerta("Hay problemas en la base de datos. Inténtelo en otro momento.","ERROR DB",true);
                    console.error('Error:', response.message);
                }
                else if(response.status.includes(registro_erroneo)){
                    alerta("No se ha podido hacer el registro por un problema en la base de datos.","ERROR REGISTRO",true);
                    console.error('Error:', response.message);
                }
                
            },
            error: function(jqXHR, textStatus, errorThrown) {
                document.getElementById("cargando").style.display = 'none';
                alerta("Ha ocurrido algún problema y no se ha podido hacer el registro. Error "+textStatus+"/"+errorThrown,"ERROR REGISTRO",true);
                console.error('Error:', textStatus, errorThrown);
            }
        });
    
}


function alerta(mensaje, titulo, previo, ancho) {
    if (typeof(previo) == 'boolean' && previo == true) {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta(true)'/></div>";
    } else {
        document.getElementById('mensaje_div').innerHTML = "<div>" + mensaje + "</div>" + "<br><div style='text-align: right;'><input type='button' class='textoboton btn btn-success' value='Ok' onclick='cierraAlerta()'/></div>";
    }

    if (typeof(ancho) != 'number') ancho = 300;
    if (typeof(duracion) != 'number') duracion = 0;
    $("#mensaje_div").dialog({
        title: titulo,
        autoOpen: false,
        draggable: false,
        dialogClass: "alertas no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        width: ancho
    });
    $("#mensaje_div").dialog('open');
}



function canvasFirma() {
    tool = new tool_pencil();
    $("#div_canvas_firma").dialog({
        autoOpen: true,
        dialogClass: "alert no-close",
        modal: true,
        hide: { effect: "fade", duration: 0 },
        resizable: false,
        show: { effect: "fade", duration: 0 },
        title: "FIRMA",
        width: 500,
        buttons: [{
                class: "btn btn-success textoboton",
                text: "Aceptar",
                click: function() {
                    if (!isCanvasEmpty()) {
                        document.getElementById("firma").value = "FORMULARIO FIRMADO";
                        canvas_upload = canvas.toDataURL('image/png');
                    } else {
                        document.getElementById("firma").value = "";
                    }
                    $("#div_canvas_firma").dialog("close");
                    $("#div_canvas_firma").dialog("destroy");
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Borrar",
                click: function() {
                    context.clearRect(0, 0, canvas.width, canvas.height);
                    document.getElementById("firma").value = "";
                }
            },
            {
                class: "btn btn-success textoboton",
                text: "Cancelar",
                click: function() {
                    if (!isCanvasEmpty()) {
                        document.getElementById("firma").value = "FORMULARIO FIRMADO";
                        canvas_upload = canvas.toDataURL('image/png');
                    } else {
                        document.getElementById("firma").value = "";
                    }
                    $("#div_canvas_firma").dialog("close");
                    $("#div_canvas_firma").dialog("destroy");
                }
            }
        ]
    });
}

function tool_pencil() {
    var tool = this;
    this.started = false;

    // This is called when you start holding down the mouse button or touch the screen.
    // This starts the pencil drawing.
    this.startDrawing = function(x, y) {
        context.beginPath();
        context.moveTo(x, y);
        tool.started = true;
    };

    // This function is called every time you move the mouse or touch the screen. 
    // It draws a line if the tool.started state is set to true.
    this.draw = function(x, y) {
        if (!tool.started) return;
        context.lineTo(x, y);
        context.stroke();
    };

    // This is called when you release the mouse button or end touching the screen.
    this.endDrawing = function() {
        tool.started = false;
    };
}

// The general-purpose event handler. This function determines the mouse or touch position relative to the canvas element.
function ev_canvas(ev) {
    var canvasRect = canvas.getBoundingClientRect();
    var clientX, clientY;

    if (ev.touches && ev.touches.length > 0) {
        ev.preventDefault();
        clientX = ev.touches[0].clientX;
        clientY = ev.touches[0].clientY;
    } else {
        clientX = ev.clientX;
        clientY = ev.clientY;
    }

    var x = clientX - canvasRect.left;
    var y = clientY - canvasRect.top;

    var func;
    if (ev.type === 'mousedown' || ev.type === 'touchstart') {
        func = tool.startDrawing;
    } else if (ev.type === 'mousemove' || ev.type === 'touchmove') {
        func = tool.draw;
    } else if (ev.type === 'mouseup' || ev.type === 'touchend') {
        func = tool.endDrawing;
    }

    if (func) {
        func(x, y);
    }
}

// Función para verificar si el canvas contiene algo dibujado
function isCanvasEmpty() {
    var imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    var data = imageData.data;

    for (var i = 0; i < data.length; i += 4) {
        // Comprobar si el canal alfa (transparencia) es mayor que 0
        if (data[i + 3] !== 0) {
            return false; // El canvas contiene algo dibujado
        }
    }

    return true; // El canvas está vacío
}


