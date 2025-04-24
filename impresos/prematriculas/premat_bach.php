<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo "css/matriculas.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <title>PREMATRÍCULA BACHILLERATO</title>
</head>

<body>
    <div style="display:none">
        <form id="matricula_bach" class="needs-validation" novalidate method="post">
            <input type="hidden" name="anno_curso" id="anno_curso" />
            <input type="hidden" name="anno_curso_premat" id="anno_curso_premat" />
            <input type="hidden" name="premat" id="premat" value="0" />
            <input type="hidden" name="id_nie" id="id_nie" />
            <input type="hidden" name="email" id="email" />
            <input type="hidden" name="b1_modalidad" id="b1_modalidad" />
            <input type="hidden" name="primer_idioma" id="primer_idioma" />
            <!-- 1º BACH -->
            <input type="hidden" name="religion" id="religion" />
            <input type="hidden" name="obligatoria1" id="obligatoria1" />
            <input type="hidden" name="obligatoria2" id="obligatoria2" />
            <input type="hidden" name="obligatoria3" id="obligatoria3" />
            <input type="hidden" name="optativa1" id="optativa1" />
            <input type="hidden" name="optativa2" id="optativa2" />
            <input type="hidden" name="optativa3" id="optativa3" />
            <input type="hidden" name="optativa4" id="optativa4" />
            <input type="hidden" name="optativa5" id="optativa5" />
            <input type="hidden" name="optativa6" id="optativa6" />
            <input type="hidden" name="optativa7" id="optativa7" />
            <input type="hidden" name="optativa8" id="optativa8" />
            <input type="hidden" name="optativa9" id="optativa9" />
            <input type="hidden" name="optativa10" id="optativa10" />
            <input type="hidden" name="optativa11" id="optativa11" />
            <input type="hidden" name="optativa12" id="optativa12" />
            <input type="hidden" name="optativa13" id="optativa13" />
            <input type="hidden" name="optativa14" id="optativa14" />
            <input type="hidden" name="optativa15" id="optativa15" />
            <input type="hidden" name="optativa16" id="optativa16" />
            <input type="hidden" name="optativa17" id="optativa17" />
            <!-- 2º BACH CIENCIAS Y HHCCSS-->
            <input type="hidden" name="tronc_opc1" id="tronc_opc1" />
            <input type="hidden" name="tronc_opc2" id="tronc_opc2" />
            <!-- 2º BACH HHCCSS-->
            <input type="hidden" name="modalidad1" id="modalidad1" />
            <input type="hidden" name="modalidad2" id="modalidad2" />
            <input type="hidden" name="modalidad3" id="modalidad3" />
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
                <h6>SOLICITUD: PREMATRÍCULA DE BACHILLERATO</h6><br>
                <h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
            </div>
        </div>
        <label style="color:red !important;margin-left:100px;margin-top:30px">* Campos obligatorios</label>
        <!--Código HTML de las páginas están en eso_html-->
        <!--PAGINA 1 ------------------------------------------------------------------------------------------>
        <center>
            <div id="pagina_1" class="ui-widget-header ui-corner-all justify-content-center flex-column" style="display:none"></div>
        </center>

        <!--PAGINA 2------------------------------------------------------------------------------------------>
        <div id="pagina_2" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 3------------------------------------------------------------------------------------------>
        <div id="pagina_3" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 4------------------------------------------------------------------------------------------>
        <div id="pagina_4" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 5------------------------------------------------------------------------------------------------------------------------------------------------------>
        <div id="pagina_5" class="ui-widget-header ui-corner-all col-12 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 6------------------------------------------------------------------------------------------>
        <div id="pagina_6" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column " style="display:none"></div>

    </div>

    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="confirmarnuevaPrem_div" class="alertas"></div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/premat_bach.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/bach1.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/bach2_ciencias.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/bach2_hhccss.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/validadores_bach.js?q=".time();?> type="text/javascript"></script>
</body>

</html>