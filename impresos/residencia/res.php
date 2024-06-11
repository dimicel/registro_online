<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo "css/res.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <link rel="stylesheet" href=<?php echo "../../js/croppie/croppie.css?q=".time();?> type="text/css">
    <title>DATOS ALUMNO PARA RESIDENCIA</title>
</head>

<body>
    <div style="display:none">
        <form id="residencia">
            <input type="hidden" id="nombre_tarjeta" name="nombre_tarjeta">
            <input type="hidden" id="nombre_foto" name="nombre_foto">
            <input type="hidden" id="id_nie" name="id_nie">
            <input type="hidden" id="anno_curso" name="anno_curso">
            <input type="hidden" id="email" name="email">
            <input type="hidden" id="bonificado" name="bonificado">
        </form>
    </div>
    <!-- CABECERA LOGOS ---------------------------------------------------------------------------------->
    <div class="container w-100">
        <!--CABECERA  ------------------------------------------------------------------------------------>
        <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
            <div class="col-2 justify-content-start">
                <input style="width:175px; height:100px" type="image" src="../../recursos/logo_ccm.jpg" />
            </div>
            <div class="col-8 justify-content-center" style="text-align: center;">
                <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                <h6>DATOS ALUMNO PARA RESIDENCIA</h6><br>
                <h7>ATENCIÓN: Los datos RELACIONADOS CON LA SALUD recogidos en este formulario sólo se emplearán para generar un fichero PDF y después se destruirán. NUNCA SERÁN GRABADOS.</h7>
                <!--<h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>-->
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="../../recursos/mini_escudo.jpg" />
            </div>
        </div>
        <!--Código HTML de las páginas están en eso_html-->

        <!--PAGINA 1 DATOS ALUMNO------------------------------------------------------------------------------------------>
        <div id="pagina_1" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 2 DATOS ESTUDIOS------------------------------------------------------------------------------------------>
        <div id="pagina_2" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 3 DATOS TUTORES------------------------------------------------------------------------------------------>
        <div id="pagina_3" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 4 DATOS MÉDICOS------------------------------------------------------------------------------------------>
        <div id="pagina_4" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 5 CARGA TARJETA SANITARIA------------------------------------------------------------------------------------------>
        <div id="pagina_5" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 6 DATOS BANCARIOS Y FIRMA------------------------------------------------------------------------------------------>
        <div id="pagina_6" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 7 FINAL (REGISTRO)------------------------------------------------------------------------------------------>
        <div id="pagina_7" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>
    </div>

    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>
    <div id="div_edita_imagen" style="display:none; text-align:center"> 
    <label><small id="texto_editor_imagen">Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro</small></label>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col-12 justify-content-center align-items-center" id="div_imagen">
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>
    <div id="div_canvas_firma" style="display:none; text-align:center;">
        <label><small>Puede firmar manteniendo pulsado el botón del ratón, con una tableta digitalizadora o usando el dedo si está con una tablet o un móvil.</small></label><br>
        <div id="div_lienzo" >
            <canvas id="firmaCanvas" width="400" height="200" style="background-color:white; border: 1px solid black;"></canvas>
        </div>
    </div>
    <div id="confirmarnuevaInsc_div" class="alertas"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/res.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "../../js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/validadores_res.js?q=".time();?> type="text/javascript"></script>

</body>

</html>