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
    <title>GESTIÓN DEL REGISTRO ONLINE - SECRETARIA DEL IES UNIVERSIDAD LABORAL DE TOLEDO</title>
</head>

<body>
    <div id="res_panel" style="display: block;  padding: 5px; width:990px" class="ui-widget-header ui-corner-all centrado">
        <div style="display:table-cell; padding-left:10px; padding-top: 10px">
            <img src="recursos/escudo.jpg" width="115" height="105" alt="Escudo_Uni">
        </div>
        <div style="display:table-cell; vertical-align: middle; padding-left: 20px">
            <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
            <h2>IES UNIVERSIDAD LABORAL</h2>
            <h3 id="rotulo_tipo_usu">RESIDENCIA - GESTIÓN DEL REGISTRO ONLINE</h3>
        </div>
       
        <!-- LISTADO USUARIOS _____________________________________________________________________-->
        <!--_______________________________________________________________________________________-->
        <div id="res_usu_reg_tab" class="ui-widget-header ui-corner-all" >
            <div class="row" style="margin-top:15px;margin-left:20px;display:none" id="secretaria" >
                <div class="col-5">
                    <input type="button" class="textoboton btn btn-success" value="Volver a Secretaría" onclick="document.location='secretaria.php?q='+Date.now()">
                </div>
            </div>
            <div class="row" style="margin-top:15px">
                <div class="col-2" >
                    <label class="col-form-label" style="margin-left: 30px;">Año Académico: </label>
                </div>
                <div class="col-2" >
                    <select id="res_curso" size="1" onchange="res_listaUsus();" class="form-control"></select>
                </div>
                <div class="col-3">
                    <input type="button"  class="textoboton btn btn-success" value="Modificar email Jefe Residencia" onclick="res_cambioEmailJefeRes()">
                </div>
                <div class="col-2" style="display:none" id="csv_remesas">
                    <input type="button" class="textoboton btn btn-success" value="CSV Remesas Banco" onclick="remesasBanco()">
                </div>
                <div class="col-1">
                    <input type="button" id="boton_salir"  class="textoboton btn btn-success" value="SALIR" onclick="javascript: res_cierrasesion();">
                </div>
                <!--
                <div class="col-1">
                    <label class="col-form-label" style="margin-left:20px; ">Mostrar: </label>
                </div>
                <div class="col-4" style="margin-left:-10px">
                    <select id="tipo_residente" size="1" onchange="res_listaUsus()" class="form-control">
                        <option value='res'>Residentes</option>
                        <option value='resnm'>SÓLO Residentes NO matriculados</option>
                        <option value='resm'>SÓLO Residentes matriculado</option>
                        <option value='todos' selected>Todos</option>
                    </select>
                </div>
            -->
            </div>
            
            <div class="row" style="margin-top:15px">
                <label class="col-form-label col-1" style="margin-left:20px">Buscar: </label>
                <div class="col-7" style="margin-left:-35px">
                    <input type="text" id="res_busqueda_usus" maxlength="255" class="form-control" onkeyup="res_listaUsus()">
                </div>
                <label class="col-form-label col-1" style="margin-left:20px">Filtro: </label>
                <div class="col-2" style="margin-left:-35px">
                    <select id="filtro_bajas" size="1"   class="form-control" onchange="res_listaUsus();">
                        <option value=-1>Todos</option>
                        <option value=0>Altas</option>
                        <option value=1>Bajas</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <span style="font-size: 0.75em">
                        Para cambiar la fianza, o el estado Bonificado/No bonificado o Baja, haz doble clic sobre la celda
                    </span>
                </div>
            </div>   
            <div class="row justify-content-center" style="margin-top:10px">
                <ul class="pagination pagination-sm" id="navegacion_usus_top"></ul>
            </div>
            <!--<div class="row justify-content-center" >
                <div class="col" style="text-align: center;">
                    <span style="display: block; margin: 0 auto;">Usuario con fondo rojo = Usuario INHABILITADO.</span>
                </div>
            </div>-->

            <div class="row justify-content-center">
                <table id="res_encabezado_usus" class="encab_tablas noseleccionable" cellpadding="0" cellspacing="0" style="margin-left:20px">
                </table><br>
                <div id="div_res_tabla_usus" style="overflow: auto; height: 480px;" class="table-hover">
                    <table id="res_registros_usus" cellpadding="0" cellspacing="0" class="noseleccionable" style="margin-left:20px">
                    </table>
                </div>
                <div id="div_res_notabla_usus" style="display:none; height: 400px; text-align: center; margin-left: 20px">
                    No hay usuarios que listar.
                </div>
            </div>
            <div class="row justify-content-center">
                <ul class="pagination pagination-sm" id="res_navegacion_usus_bottom"></ul>
            </div>
        </div>
        <!-- FIN LISTADO DE USUARIOS__________________________________________________________________-->
        <!--__________________________________________________________________________________________-->
    </div>
    <!--______________________________________________________________________________________________-->


    <div id="res_verInfoUsu_div" style="display:none; font-size:0.85em !important;" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="res_cargando" style="display:none; font-size:4em; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('recursos/espera.gif') no-repeat center center; opacity: .7;z-index:9999;text-align:center;">
    </div>
    <div id="res_mensaje_div" class="alertas"></div>
    <div id="res_div_dialogs" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="div_cambio_email_jef_res" class="ui-widget-header ui-corner-all alertas">
        <form id="cambio_email_jef_res">
            <div class="form-row">
                <div class="form-group col">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                    <label for="email_jr">Email del Jefe de Residencia:</label>
                    <input type="text" name="email_jr" id="email_jr" class="form-control" maxlength="255"/>
                </div>
            </div>
        </form>
    </div>
    <div id="div_csv_remesas" style="display:none">
        <form id="descarga_csv_remesas" action="php/residencia_csv_remesas.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_remesas" name="curso_csv_remesas" />
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery.bootpag.min.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/context_menu/jquery.contextMenu.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/context_menu/jquery.ui.position.js?q=".time(); ?>></script>
    <script src=<?php echo "js/residencia.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time(); ?> type="text/javascript"></script>
</body>

</html>