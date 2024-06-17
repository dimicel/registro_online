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
    <link rel="stylesheet" href=<?php echo "css/sepa.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <link rel="stylesheet" href=<?php echo "../../js/croppie/croppie.css?q=".time();?> type="text/css">
    <title>DATOS ALUMNO PARA RESIDENCIA</title>
</head>

<body>
            
    <form id="sepa">
        <input type="hidden" id="registro" name="registro"> 
        <input type="hidden" id="cp" name="cp"> 
        <input type="hidden" id="direccion" name="direccion"> 
        <input type="hidden" id="localidad" name="localidad"> 
        <input type="hidden" id="provincia" name="provincia"> 
        <input type="hidden" id="firma" name="firma"> 
        <div class="container w-100">
            
            <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
                <div class="col-2 justify-content-start">
                    <input style="width:175px; height:100px" type="image" src="../../recursos/logo_ccm.jpg" />
                </div>
                <div class="col-8 justify-content-center" style="text-align: center;">
                    <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                    <h6>ORDEN SEPA PARA ALUMNOS RESIDENTES NO BONIFICADOS</h6><br>
                    <!--<h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>-->
                    <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
                </div>
                <div class="col-2 justify-content-end">
                    <input style="width:150px; height:150px" type="image" src="../../recursos/mini_escudo.jpg" />
                </div>
            </div>
            <div class=" row ui-widget-header ui-corner-all justify-content-center flex-column">
                <div class="col-12">
                    <div class="row" style=" margin-top:10px; margin-left:10px">
                        <div class="col-2">
                            <input type="button" value="&#x21c7;" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Regresar a página principal" onclick="confirmar()" />
                        </div>
                    </div>
                    <hr>
                    <div class="form-group form-row">
                        <div class="col-12">
                            <label for="titular_cuenta">Titular cuenta <small>(nombre y apellidos)</small>:</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" name="titular_cuenta" id="titular_cuenta" class="form-control" maxlength="65" >
                        </div>
                    </div>
                    <div class="form-group form-row">
                        <div class="col-4">
                            <label for="estudios">Código BIC (8 ó 11 caracteres):</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" name="bic" id="bic" class="form-control" maxlength="11" >
                        </div>
                        <div class="col">
                            <label for="tutor">IBAN (24 caracteres, sin guiones ni espacios):</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" name="iban" id="iban" class="form-control" maxlength="24" />
                        </div>
                        <div class="col-3">
                            <label for="t_firm">Firma</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" class="form-control" name="firma" id="firma" placeholder="Clic aquí para firmar" readonly onclick="canvasFirma();" />
                        </div>
                    </div>
                    <div class="row justify-content-center align-content" style="padding:60px">
                        <input type="button" class="btn btn-success" value="Generar orden SEPA" style="height: 3em;" onclick="registraSolicitud()" />
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>
    <div id="div_canvas_firma" style="display:none; text-align:center;">
        <label><small>Puede firmar manteniendo pulsado el botón del ratón, con una tableta digitalizadora o usando el dedo si está con una tablet o un móvil.</small></label><br>
        <div id="div_lienzo" >
            <canvas id="firmaCanvas" width="400" height="200" style="background-color:white; border: 1px solid black;"></canvas>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/sepa.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "../../js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/validadores_res.js?q=".time();?> type="text/javascript"></script>

</body>

</html>