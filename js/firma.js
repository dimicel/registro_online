/* Es necesario que en el fichero que contiene html exista el siguiente input, sobre el que se hace click para firmar
<input type="text" class="form-control" name="firma" id="firma" placeholder="Clic aquí para firmar la solicitud" readonly onclick="canvasFirma();" />

La variable que llevará la firma en la petion $.post será igual a encodeURIComponent(canvas_upload), por ejemplo
formData.append("firma", encodeURIComponent(canvas_upload)); 
El resto de datos se añaden a formdata de la misma forma: formData.append("variable que se pasa a php", valor de la variuable);

La estructura $.post será
$.post({
        url:"script php"",
        data: formData,
        contentType: false,
        processData: false,
        success: function(resp) {
        ........
        ........
        },
        error: function(xhr, status, error) {
        .....
        ....
        });


En php, la imagen que contiene la firma se genera con este código:

if (isset($_POST['firma'])){
    $imageData = urldecode($_POST['firma']);
    if (!is_dir(__DIR__."/../../../docs/tmp")) mkdir(__DIR__."/../../../docs/tmp", 0777);
    
    // Generar el archivo temporal
    $tempFile = tempnam(__DIR__."/../../../docs/tmp", 'canvas_' . session_id());
    
    // Asegurarse de que la extensión sea '.png' y no haya caracteres extra
    $tempFile = pathinfo($tempFile, PATHINFO_DIRNAME) . '/' . basename($tempFile, '.tmp') . '.png';
    
    // Guardar el archivo de imagen
    file_put_contents($tempFile, base64_decode(str_replace('data:image/png;base64,', '', $imageData)));
    $firma = $tempFile;
}
*/

var drawing = false;
var mouseX, mouseY;
var canvas, context, tool, canvas_upload;
var formData = new FormData();

$(document).ready(function() {
    /*div_firma="<div id='div_canvas_firma' style='display:none; text-align:center;'>";
    div_firma+="    <label><small>Puede firmar manteniendo pulsado el botón del ratón, con una tableta digitalizadora o usando el dedo si está con una tablet o un móvil.</small></label>";
    div_firma+="    <div id='div_lienzo' >";
    div_firma+="        <canvas id='firmaCanvas' width='400' height='200' style='background-color:white; border: 1px solid black;'></canvas>";
    div_firma+="    </div>";
    div_firma+="</div>";
    document.body.innerHTML+=div_firma;*/
    canvas = document.getElementById('firmaCanvas');
    context = canvas.getContext('2d');
    canvas.addEventListener('mousedown', ev_canvas, false);
    canvas.addEventListener('mousemove', ev_canvas, false);
    canvas.addEventListener('mouseup', ev_canvas, false);
    canvas.addEventListener("mouseout", ev_canvas, false);
    canvas.addEventListener('touchstart', ev_canvas, false);
    canvas.addEventListener('touchmove', ev_canvas, false);
    canvas.addEventListener('touchend', ev_canvas, false);
});

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
                        document.getElementById("firma").value = "SOLICITUD FIRMADA";
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
                        document.getElementById("firma").value = "SOLICITUD FIRMADA";
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
    let imageData = context.getImageData(0, 0, canvas.width, canvas.height);
    let data = imageData.data;

    for (let i = 0; i < data.length; i += 4) {
        // Comprobar si el canal alfa (transparencia) es mayor que 0
        if (data[i + 3] !== 0) {
            return false; // El canvas contiene algo dibujado
        }
    }

    return true; // El canvas está vacío
}

