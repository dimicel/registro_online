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
    <link rel="stylesheet" href=<?php echo "css/trans.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <title>SOLICITUD EXENCIÓN DE FCT</title>
</head>

<body>
    <div class="container w-50">
        <!--CABECERA LOGOS ------------------------------------------------------------------------------------>
        <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
            <div class="col-2 justify-content-start">
                <input style="width:175px; height:100px" type="image" src="recursos/logo_ccm.jpg" />
            </div>
            <div class="col-8 justify-content-center" style="text-align: center;">
                <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                <h5>REGISTRO ONLINE</h5>
                <h6>SOLICITUD: EXENCIÓN DE FCT</h6><br>
                <!--<h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>-->
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
            </div>
        </div>
    </div>
    <div class="container" style="width:35%">    
        <label style="color:red !important; margin-top:30px">* Campos obligatorios</label>

        <!-- FORMULARIO --------------------------------------------------------------------------------------->
        <form id="exenc">
            <div class="row ui-widget-header ui-corner-all" >
                <div class="col">
                    <div class="row mt-3">
                        <div class="col-2">
                            <label for="lista_don">*Tratamiento</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                        </div>
                        <div class="col">
                            <label for="nombre" style="margin-left:10px">*Nombre y Apellidos</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <select name="lista_don" id="lista_don" size="1" class="custom-select" onchange="seleccionListaDon()">
                                <option value=""></option>
                                <option value="D.">D.</option>
                                <option value="Dña.">Dña.</option>
                            </select>
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="nombre" id="nombre" size="70" maxlength="90" value="Seleccione en el desplegable de la izquierda." readonly />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-4"> 
                            <label>*Tipo de documento:</label><br>  
                            <input type="radio" name="pass_nif" id="nif" value="nif" checked/><label style="margin-left: 5px">NIF/NIE</label>
                            <input style="margin-left: 20px" type="radio" name="pass_nif" id="pass" value="pass" /><label style="margin-left: 5px">Pasaporte</label>
                        </div>
                        <div class="col">
                            <label for="nif-nie">*Nº documento:</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input style="margin-left: 5px" class="form-control" type="text" name="nif_nie" id="nif_nie" size="12" maxlength="12" title="Sin espacios ni guiones" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-3" id="div_formacion">
                            <label for="formacion">Formación:</label>
                            <select name="formacion" id="formacion" size="1" class="custom-select" onchange="cambiaTipoForm()"></select>
                        </div>
                        <div class="col"  id="div_grado_medio">
                            <label for="ciclos_f">Denominación:</label>
                            <select name="ciclos_f" id="ciclos_f" size="1"  class="custom-select" onchange="cambiaFormGM()">
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5">
                        <input type="button" id="generar" value="REGISTRAR SOLICITUD" onclick="iniciaGeneraPdf() " />
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url( 'recursos/espera.gif') no-repeat center center; opacity: .7; "></div>
    <div id="mensaje_div" style="display:none "></div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
<script src=<?php echo "js/exenc.js?q=".time();?> type="text/javascript"></script>
<script src=<?php echo "js/validadores.js?q=".time();?> type="text/javascript"></script>
</html>