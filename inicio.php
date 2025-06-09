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
    <link rel="stylesheet" type="text/css" href=<?php echo "css/est.css?q=".time();?>>
    <link rel="stylesheet" href=<?php echo "jqueryui/jquery-ui.min.css?q=".time();?>>
    <title>Tramitación online de documentación - IES UNIVERSIDAD LABORAL</title>
</head>

<body onkeyup="javascript:if(event.keyCode=='13')entra();" style="overflow: hidden;">
   
    <!-- LOGIN ___________________________________________________________-->
    <!--__________________________________________________________________-->
    <div id="login" class="centrado container" style="overflow-y: scroll !important;">
        <header style="text-align:center; color: #000080;">
            <center>
                <h2><img src="recursos/escudo.jpg" class="img-responsive" width="229" height="211" alt="Escudo_Uni"></h2>
                <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
                <h1><strong id="centro"></strong></h1>
                <h2><strong>TRAMITACIÓN ONLINE DE DOCUMENTACIÓN</strong></h2>
                <p>Por favor, utilice navegadores actualizados (Chrome, Edge, Firefox).</p>
                <p><strong>Microsoft Internet Explorer no es compatible 100% con esta plataforma.</strong></p>
                <p class="text-danger">Esta plataforma tampoco es compatible con dispositivos móviles. Utilice un ordenador para realizar los trámites.</p>
            </center>
        </header>
        <div class="row justify-content-center" style="margin-top: 20px;">
            <div class="ui-widget-header ui-corner-all col-12 col-sm-10 col-md-6 col-lg-4 " style="padding:20px">
                <form id="form_login" class="form-horizontal needs-validation" novalidate>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="usuario" class="col-lg-12 control-label ">NIE<small> (Nº de Identificación Escolar)</small></label>
                            <input name="usuario" type="text" id="usuario" class="form-control" tabindex="1" required>
                            <div class="invalid-feedback">Complete el campo</div>
                            <div class="valid-feedback"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <label for="password" class="col-lg-12 control-label ">Contraseña</label>
                            <input name="password" type="password" id="password" class="form-control" tabindex="2" required>
                            <div class="invalid-feedback">Complete el campo</div>
                            <div class="valid-feedback"></div>
                        </div>
                    </div>
                    <center>
                        <div style="padding-top:15px;" class="row justify-content-center">
                            <div class="col-lg-12" style="text-align: center;">
                                <button type="button" id="b_ok_login" class="btn btn-success w-100" onclick="entra()">Entrar</button>                            
                            </div>  
                            <!--<input type="button" id="b_ok_login" class="btn btn-success btn-sm" value="Entrar" tabindex="3" onclick="entra()">-->
                        </div>
                    </center>
                </form>
                <center>
                    <div style="margin-top:5px; margin-bottom:5px">
                        <span>
                            <a href="javascript:recuperaPass()"  class="etiquetas">Recuperar contraseña</a>
                        </span>
                    </div>
                </center>
            </div>
        </div>
    </div>


    <!--_______________________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
	<script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/index.js?q=".time(); ?> type="text/javascript"></script>
    
</body>

</html>