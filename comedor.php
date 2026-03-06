<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo "css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "css/secretaria.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "js/croppie/croppie.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "jqueryui/jquery-ui.min.css?q=".time();?> >
    <link rel="stylesheet" href=<?php echo "js/context_menu/jquery.contextMenu.min.css?q=".time();?>>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <title id="titulo">GESTIÓN DEL REGISTRO ONLINE - COMEDOR </title>
</head>

<body>
    <div id="res_comedor" style="display: block;  padding: 5px; width:990px" class="ui-widget-header ui-corner-all centrado">
        <div style="display:table-cell; padding-left:10px; padding-top: 10px">
            <img src="recursos/escudo.jpg" width="115" height="105" alt="Escudo_Uni">
        </div>
        <div style="display:table-cell; vertical-align: middle; padding-left: 20px">
            <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
            <h2 id="centro"></h2>
            <h3 id="res_rotulo_comedor">RESIDENCIA - ASISTENCIA AL COMEDOR</h3>
        </div>
       
        <!-- LISTADO USUARIOS _____________________________________________________________________-->
        <!--_______________________________________________________________________________________-->
        <div id="div_lista_comedor" class="ui-widget-header ui-corner-all container" style="padding-top:10px;padding-bottom: 10px;margin-top:20px">
            <div class="row">
                <div class="col-1">
                    <label>Fecha:</label>
                </div>
                <div class="col-2">
                    <input type='text' name='fecha_lista_comedor' id='fecha_lista_comedor' class='form-control' maxlength='10' size='15'  placeholder='Ej. 02/05/2000' onchange="res_listadoRevisionAsistencia()">
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label>Los residentes marcados en amarillo avisaron de la NO asistencia al comedor en la fecha actual.</label>
                    <table width="100%" class="encab_tablas noseleccionable">
                        <tr>
                            <td width="20%">NIE</td>
                            <td width="65%">Apellidos, Nombre</td>
                            <td width="5%" style="text-align: center;">Des</td>
                            <td width="5%" style="text-align: center;">Com</td>
                            <td width="5%" style="text-align: center;">Cen</td>
                        </tr>
                    </table>
                    <table id="lista_comedor"  width="100%" style="overflow-y: auto;max-height: 500px;">
                        <tbody id="asistencia_comedor"></tbody>
                    </table>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <button id="btn_actualiza_comedor" class="btn btn-success" onclick="res_actualizaListadoAsistenciaComedor()">Actualizar asistencia</button>
                    <button id="btn_salir" class="btn btn-danger" onclick="cierrasesion()">Cerrar Sesión</button>
                </div>
        </div>
        <!-- FIN LISTADO DE USUARIOS__________________________________________________________________-->
        <!--__________________________________________________________________________________________-->
    </div>
    <!--______________________________________________________________________________________________-->


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery.bootpag.min.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/context_menu/jquery.contextMenu.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/context_menu/jquery.ui.position.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/comedor.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time(); ?> type="text/javascript"></script>
</body>

</html>