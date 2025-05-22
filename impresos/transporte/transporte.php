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
    <link rel="stylesheet" href=<?php echo "css/trans.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <title>SOLICITUD TRANSPORTE ESCOLAR</title>
</head>

<body>
    <div style="display:none">
        <form id="transporte" name="transporte"  method="post">
            <input type="hidden" name="_cursa" id="_cursa" />
            <input type="hidden" name="anno_curso" id="anno_curso" />
            <input type="hidden" name="cursa" id="cursa" />
            <input type="hidden" name="registro_trans" id="registro_trans" />
            <input type="hidden" name="id_nie" id="id_nie" />
            <input type="hidden" name="email" id="email" />
            <input type="hidden" name="_t_apartado" id="_t_apartado" />
            <input type="hidden" name="_t_modalidad" id="_t_modalidad" />
            <input type="hidden" name="_t_aut_acred_iden" id="_t_aut_acred_iden" />
            <input type="hidden" name="_t_aut_acred_domic" id="_t_aut_acred_domic" />
            <input type="hidden" name="_t_ruta" id="_t_ruta" />
        </form>
    </div>
    <!-- CABECERA LOGOS ---------------------------------------------------------------------------------->
    <div class="container w-100">
        <!--CABECERA  ------------------------------------------------------------------------------------>
        <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
            <div class="col-2 justify-content-start">
                <input style="width:175px; height:100px" type="image" src="recursos/logo_ccm.jpg" />
            </div>
            <div class="col-8 justify-content-center" style="text-align: center;">
                <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                <h5>REGISTRO ONLINE</h5>
                <h6>SOLICITUD: TRANSPORTE ESCOLAR</h6><br>
                <!--<h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>-->
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
            </div>
        </div>
        <label style="color:red !important;margin-left:100px;margin-top:30px">* Campos obligatorios</label>
        <!--Código HTML de las páginas están en eso_html-->

        <!--PAGINA 1 DATOS ALUMNO------------------------------------------------------------------------------------------>
        <div id="pagina_1" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 2 DATOS DOMICILIO------------------------------------------------------------------------------------------>
        <div id="pagina_2" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 3 TRANSPORTE ESCOLAR - REPRESENTANTE------------------------------------------------------------------------------------------>
        <div id="pagina_3" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 4 TRANSPORTE ESCOLAR - TIPO SERVICIO SOLICITADO------------------------------------------------------------------------------------------>
        <div id="pagina_4" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 5 TRANSPORTE ESCOLAR - AUTORIZACIONES------------------------------------------------------------------------------------------>
        <div id="pagina_5" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 6 TRANSPORTE ESCOLAR - DECLARACIONES RESPONSABLES------------------------------------------------------------------------------------------>
        <div id="pagina_6" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 7 FINAL (REGISTRO)------------------------------------------------------------------------------------------>
        <div id="pagina_7" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>
    </div>

    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/trans.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/validadores.js?q=".time();?> type="text/javascript"></script>

</body>

</html>