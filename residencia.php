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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title id="titulo">GESTIÓN DEL REGISTRO ONLINE - RESIDENCIA DEL </title>
</head>

<body>
    <div id="res_panel" style="display: block;  padding: 5px; width:990px" class="ui-widget-header ui-corner-all centrado">
        <div style="display:table-cell; padding-left:10px; padding-top: 10px">
            <img src="recursos/escudo.jpg" width="115" height="105" alt="Escudo_Uni">
        </div>
        <div style="display:table-cell; vertical-align: middle; padding-left: 20px">
            <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
            <h2 id="centro"></h2>
            <h3 id="res_rotulo_tipo_usu">RESIDENCIA - GESTIÓN DEL REGISTRO ONLINE</h3>
        </div>
       
        <!-- LISTADO USUARIOS _____________________________________________________________________-->
        <!--_______________________________________________________________________________________-->
        <div id="res_usu_reg_tab" class="ui-widget-header ui-corner-all" >
            <div class="row" style="margin-top:15px;margin-left:20px;display:none" id="secretaria" >
                <div class="col-5">
                    <input type="button" class="textoboton btn btn-success" value="Volver a Secretaría" onclick="document.location='secretaria.php?q='+Date.now()">
                </div>
            </div>
            <div class="row" style="margin-top:15px;margin-left:20px; display:none" id="boton_salir" >
                <div class="col-5">
                    <input type="button"  class="textoboton btn btn-success" value="SALIR" onclick="javascript: res_cierrasesion();">
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
                    <input type="button"  class="textoboton btn btn-success" value="Modificar email Jefe Residencia" onclick="cambioEmailJefeRes()">
                </div>
                <div class="col-3">
                    <input type="button" class="textoboton btn btn-success" onclick="res_GestionComedor()" value="Comedor">
                    <input type="button" class="textoboton btn btn-success" onclick="res_InformesComedor()" value="Informes Comedor">
                </div>
                <div class="col-2" style="display:none" id="csv_remesas">
                    <input type="button" class="textoboton btn btn-success" value="CSV Remesas Banco" onclick="remesasBanco()">
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
                <div class="col-6" style="margin-left:-35px">
                    <input type="text" id="res_busqueda_usus" maxlength="255" class="form-control" onkeyup="res_listaUsus()">
                </div>
                <label class="col-form-label col-1" style="margin-left:10px">FILTROS: </label>
                <label class="col-form-label col-1" style="margin-left:0px">Estado</label>
                <div class="col-2" style="margin-left:-35px">
                    <select id="filtro_bajas" size="1"   class="form-control" onchange="res_listaUsus();">
                        <option value=-1>Todos</option>
                        <option value=0>Altas</option>
                        <option value=1>Bajas</option>
                    </select>
                </div>
                <label class="col-form-label col-1" style="margin-left:10px">Edificio</label>
                <div class="col-1" style="margin-left:-35px">
                    <input type="text" class="form-control" id="filtro_edificio" size="5" maxlength="50" class="col-2" style="margin-left:0px">
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <span style="font-size: 0.75em">
                        Para cambiar la FIANZA, el estado BONIFICADO/NO BONIFICADO, BAJA o el EDIFICIO asignado al residente, haz doble clic sobre la celda correspondiente.
                    </span>
                </div>
            </div>   
            <div class="row justify-content-center" style="margin-top:10px">
                <ul class="pagination pagination-sm" id="res_navegacion_usus_top"></ul>
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


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery.bootpag.min.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/context_menu/jquery.contextMenu.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/context_menu/jquery.ui.position.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/residencia.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time(); ?> type="text/javascript"></script>
</body>

</html>