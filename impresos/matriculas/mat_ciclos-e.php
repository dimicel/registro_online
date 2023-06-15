<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo "css/matriculas.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?> />
    <link rel="stylesheet" href=<?php echo "../../js/croppie/croppie.css?q=".time();?> type="text/css">
    <title>MATRÍCULA CICLOS FORMATIVOS</title>
</head>

<body>
    <div class="container w-100">
        <div style="display:none;">
            <form id="mat_ciclos" name="mat_ciclos" method="post" action="php/generapdf_matciclos.php">
                <input type="hidden" name="anno_curso" id="anno_curso" />
                <input type="hidden" name="id_nie" id="id_nie" />
                <input type="hidden" name="_nuevo_otra_comunidad" id="_nuevo_otra_comunidad" />
                <input type="hidden" name="email" id="email" />
                <input type="hidden" name="mayor_edad" id="mayor_edad" />
                <input type="hidden" name="_autor_fotos" id="_autor_fotos" />
            </form>
        </div>
        <!--CABECERA  ------------------------------------------------------------------------------------>
        <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
            <div class="col-2 justify-content-start">
                <input style="width:175px; height:100px" type="image" src="recursos/logo_ccm.jpg" />
            </div>
            <div class="col-8 justify-content-center" style="text-align: center;">
                <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                <h5>REGISTRO ONLINE</h5>
                <h6>SOLICITUD: MATRÍCULA DE CICLOS FORMATIVOS</h6><br>
                <!--<h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>-->
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
            </div>
        </div>
        <label style="color:red !important;margin-left:100px;margin-top:30px">* Campos obligatorios</label>
        <!--Código HTML de las páginas están en bach_html-->
        <!--PAGINA 1 SELECCIÓN CICLO------------------------------------------------------------------------------------------>
        <center>
            <div id="pagina_1" class="ui-widget-header ui-corner-all col-10 justify-content-center flex-column" style="display:none"></div>
        </center>

        <!--PAGINA 2 DATOS ALUMNO------------------------------------------------------------------------------------------>
        <div id="pagina_2" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 3 DATOS DOMICILIO------------------------------------------------------------------------------------------>
        <div id="pagina_3" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 4 SUBIDA ARCHIVOS------------------------------------------------------------------------------------------>
        <div id="pagina_4" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 5 PROTECCIÓN DATOS------------------------------------------------------------------------------------------>
        <div id="pagina_5" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>

        <!--PAGINA 6 FINAL (REGISTRO)------------------------------------------------------------------------------------------>
        <div id="pagina_6" class="ui-widget-header ui-corner-all col-10 offset-1 justify-content-center flex-column" style="display:none"></div>
    </div>

    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>
    <div id="consol_div" style="display:none"></div>
    <div id="confirmarnuevaMat_div" class="alertas"></div>
    <div id="div_ayuda_docs" class="alertas">

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

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/mat_ciclos.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/validadores_ciclos.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "../../js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/subedocs.js?q=".time();?> type="text/javascript"></script>

    <script type="text/javascript">
        $("#generar").button();
        $("#generar_limpio").button();
        $("#limpiar").button();
    </script>
</body>

</html>