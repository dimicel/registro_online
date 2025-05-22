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

<body onkeyup="javascript:if(event.keyCode=='13')entra();">
   
    <!-- LOGIN ___________________________________________________________-->
    <!--__________________________________________________________________-->
    <div id="login" class="centrado container" style="overflow-y: scroll !important;">
        <header style="text-align:center; color: #000080;">
            <center>
                <h2><img src="recursos/escudo.jpg" class="img-responsive" width="229" height="211" alt="Escudo_Uni"></h2>
                <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
                <h1><strong>IES UNIVERSIDAD LABORAL</strong></h1>
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



    <!--REGISTRO DE DATOS DEL NUEVO USUARIO ___________________________________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="nuevoUsuario_div" name="nuevoUsuario_div" style="display: none;  padding: 20px;" class="ui-widget-header ui-corner-all alertas">
        <form id="form_nuevoUsuario" class="needs-validation" novalidate>
            <div class="form-group">
                <div style="display: inline-block;">
                    <label for="nu_nie">NIE <small>(Nº Identificación Escolar)</small>:</label>
                    <input type="text" name="nu_nie" id="nu_nie" class="form-control" readonly>
                </div>
                <div style="display: inline-block; margin-left:20px">
                    <label for="nu_nif">NIF/NIE del alumno:</label>
                    <span class="errorTxt"></span>
                    <input type="text" name="nu_nif" id="nu_nif" class="form-control" onkeyup="javascript:this.value=this.value.toUpperCase();" onblur="revisaNIF(this)" placeholder="(No rellene si no tiene)">
                </div>
            </div>
            <div class="form-group" style="margin-top:-10px">
                <div style="display:inline-block;">
                    <label for="nu_nombre">NOMBRE DEL ALUMNO:</label>
                    <span class="errorTxt"></span>
                    <input type="text" style="margin-top:-5px" name="nu_nombre" id="nu_nombre" class="form-control" required maxlength="25">
                </div>
                <div style="display: inline-block; margin-left:20px">
                    <label for="nu_apellidos">APELLIDOS DEL ALUMNO:</label>
                    <span class="errorTxt"></span>
                    <input type="text" style="margin-top:-5px" name="nu_apellidos" id="nu_apellidos" class="form-control" required maxlength="40" size="38">
                </div>
            </div>
            <div class="form-group" style="margin-top:-10px">
                <label for="nu_email">CORREO ELECTRÓNICO <small>(Para recuperación de contraseña y notificaciones)</small>:</label>
                <span class="errorTxt"></span>
                <input type="email" style="margin-top:-5px" name="nu_email" id="nu_email" class="form-control" required maxlength="40">
            </div>
            <div class="form-group" style="margin-top:-10px">
                <label for="nu_repemail">REPITA EL CORREO ELECTRÓNICO:</label>
                <span class="errorTxt"></span>
                <input type="email" style="margin-top:-5px" name="nu_repemail" id="nu_repemail" class="form-control" required maxlength="40">
            </div>
            <div class="form-group" style="margin-top:-10px">
                <label for="nu_password">CONTRASEÑA:</label>
                <span class="errorTxt"></span>
                <input type="password" style="margin-top:-5px" name="nu_password" id="nu_password" class="form-control" required maxlength="40">
            </div>
            <div class="form-group" style="margin-top:-10px">
                <label for="nu_reppassword">REPITA LA CONTRASEÑA:</label>
                <span class="errorTxt"></span>
                <input type="password" style="margin-top:-5px" name="nu_reppassword" id="nu_reppassword" class="form-control" required maxlength="40">
            </div>
            <div style="margin-top:-10px">
                <!--<label for="nu_reppassword">Acepto las condiciones:</label>
                <input type="checkbox" style="margin-top:-5px" name="nu_condiciones" id="nu_condiciones" required>-->
                <a href="javascript:condiciones();" style="margin-left:10px; font-size: 0.7em;">Ver condiciones</a>
                <span class="errorTxt"></span>
            </div>
            <div class="justify-content-center" style="margin-top:10px">
                <label>Por su comodidad a la hora de cumplimentar los formularios disponibles, complete sus datos en el panel del usuario (botón 'Mis Datos').</label>
            </div>

            <div style="text-align: center;">
                <input type="button" value="Complete el registro" class="btn btn-success textoboton" onclick="solicitaRegistro();" />
                <input type="button" value="Cancelar" class="btn btn-success textoboton" onclick="$('#nuevoUsuario_div').dialog('close');" />
            </div>
        </form>
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