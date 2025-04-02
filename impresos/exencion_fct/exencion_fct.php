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
                <h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>
                <h7 style="color:#900; font-weight:bold">&nbsp;</h7>
            </div>
            <div class="col-2 justify-content-end">
                <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
            </div>
        </div>
    </div>
    <div class="container" style="width:38%">    
        <label style="color:red !important; margin-top:30px">* Campos obligatorios</label>

        <!-- FORMULARIO --------------------------------------------------------------------------------------->
        <form id="exenc">
        <div class="row ui-widget-header ui-corner-all" style="padding-left:10px; padding-right:10px">
            <div  style="display:inline-block;margin-top:10px;margin-left:10px">
                <input type="button" value="&#x21c7;" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Regresar a página principal" onclick="confirmar()" />
            </div>
            <div class="row mt-2" >
                <div class="col">
                    <div class="row mt-3">
                        <div class="col-2">
                            <label for="lista_don">*Tratamiento</label>
                        </div>
                        <div class="col">
                            <label for="nombre" style="margin-left:10px">*Nombre y Apellidos</label>
                        </div>
                        <div class="col-3">
                            <label for="nombre" style="margin-left:10px">*NIF/NIE/Pasaporte</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-2">
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <select name="lista_don" id="lista_don" size="1" class="custom-select" >
                                <option value="">Marque uno</option>
                                <option value="D.">D.</option>
                                <option value="Dña.">Dña.</option>
                            </select>
                        </div>
                        <div class="col">
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" class="form-control" name="nombre" id="nombre"  maxlength="90" />
                        </div>
                        <div class="col-3">
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input style="margin-left: 5px" class="form-control" type="text" name="nif_nie" id="nif_nie" size="12" maxlength="12" title="Sin espacios ni guiones" />
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-3" id="div_formacion">
                            <label for="formacion">*Formación:</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <select name="formacion" id="formacion" size="1" class="custom-select" onchange="cambiaTipoForm(this.value)">
                                <option value="">Marque uno...</option>
                                <option value="basico">GRADO BÁSICO</option>
                                <option value="medio">GRADO MEDIO</option>
                                <option value="superior">GRADO SUPERIOR</option>
                            </select>
                        </div>
                        <div class="col"  id="div_grado_medio">
                            <label for="ciclos_f">*Denominación:</label>
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <select name="ciclos_f" id="ciclos_f" size="1"  class="custom-select" onchange="cambiaFormGM()">
                            </select>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col">
                            <label id="label_estudios_aportados" for="estudios">*Documentación que aporta (<a style="color:#00C" href="#" onclick="anadeDoc(event)">Clic AQUÍ para añadir documentos</a>)</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col" >
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="hidden" name="validar_tabla">
                            <table  style="width: 90%; margin: 0 auto;background-color:lightslategrey"><tr><td style="width:50%"><b>Descripción</b></td><td  style="width:50%"><b>Documento</b></td></tr></table>
                            <table id="tab_lista_docs"  style="width: 90%; margin: 0 auto;"><tr><td style="text-align:center">LISTA DE DOCUMENTOS VACÍA</td></tr></table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6 offset-3 mt-3">
                            <span class="errorTxt" style="font-size: 1em;"></span>
                            <input type="text" class="form-control" name="firma" id="firma" placeholder="Clic aquí para firmar la solicitud" readonly onclick="canvasFirma();" />
                        </div>
                    </div>
                    <div class="d-flex justify-content-center mt-5 mb-4">
                        <input type="button" id="generar" class="btn btn-success" value="REGISTRAR SOLICITUD" onclick="iniciaGeneraPdf() " />
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
    <!---------------------------------------------------------------------------------------------------------------------------->
    <!-- PANEL SELECCIÓN DE DOUMENTO A SUBIR ------------------------------------------------------------------------------------->
    <div id="anade_documento" style="display:none">
        <form id="form_anade_documento">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label style="font-weight:bolder">TIPO DOCUMENTO (deben estar en formato PDF):</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="cert_empresa" name="tipo_con" class="custom-control-input" value="Certificación de la empresa" onchange="$('#div_den_otro_con').hide(); "/>
                        <label for="cert_empresa" class="custom-control-label">Certificación de la empresa donde haya adquirido la experiencia laboral, en la que conste específicamente la duración del contrato, la actividad desarrollada y el periodo de tiempo en el que se ha desarrollado dicha actividad.</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="cert_tgss" name="tipo_con" class="custom-control-input" value="Certificación de la Tesorería General de la Seguridad Social" onchange="$('#div_den_otro_con').hide(); "/>
                        <label for="cert_tgss" class="custom-control-label">Certificación de la Tesorería General de la Seguridad Social o de la mutualidad laboral a la que estuviera afiliado, donde conste la empresa, la categoría laboral (grupo de cotización) y el período de contratación, o en su caso el período de cotización en el Régimen Especial de Trabajadores Autónomos.</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="cert_alta_ob_trib" name="tipo_con" class="custom-control-input" value="Certificación de alta en el censo de obligados tributarios" onchange="$('#div_den_otro_con').hide(); "/>
                        <label for="cert_alta_ob_trib" class="custom-control-label">Certificación de alta en el censo de obligados tributarios (SÓLO TRABAJADOPRES POR CUENTA PROPIA).</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="declar_interesado" name="tipo_con" class="custom-control-input" value="Declaración del interesado" onchange="$('#div_den_otro_con').hide(); "/>
                        <label for="declar_interesado" class="custom-control-label">Declaración del interesado de las actividades más representativas (SÓLO TRABAJADOPRES POR CUENTA PROPIA).</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="cert_vol_bec" name="tipo_con" class="custom-control-input" value="Certificación para voluntarios o becarios" onchange="$('#div_den_otro_con').hide(); "/>
                        <label for="cert_vol_bec" class="custom-control-label">Para trabajadores o trabajadoras voluntarios o becarios, certificación de la organización donde se haya prestado la asistencia en la que consten, específicamente, las actividades y funciones realizadas, el año en el que se han realizado y el número total de horas dedicadas a las mismas.</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-2 custom-control custom-switch mi-checkbox">
                        <input type="radio" id="otro_con" name="tipo_con" class="custom-control-input" value="Otro" onchange="$('#div_den_otro_con').show();"/>
                        <label for="otro_con" class="custom-control-label">Otro</label>
                    </div>
                    <div class="col form-inline" style="display:none" id="div_den_otro_con">
                        <label for="den_otro_con">Descripción documento:</label>
                        <input type="text" id="den_otro_con" class="form-control ml-2" maxlength="50"/>
                    </div>
                </div>
                <hr>
                <div class="row mt-2 justify-content-center">
                    <div class="col-10 form-inline">
                        <label for="archivo_con">Documento:</label>
                        <input type="text"  id="archivo_con" style="width: 80%" class="form-control ml-2" maxlength="256" onclick="selArchConsej();" placeholder="Click aquí para seleccionar documento" readonly/>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!---------------------------------------------------------------------------------------------------------------------------->
    <div id="array_input_type_file" style="display:none"></div>
    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url( 'recursos/espera.gif') no-repeat center center; opacity: .7; "></div>
    <div id="mensaje_div" style="display:none "></div>
    <div id="div_canvas_firma" style="display:none; text-align:center;">
        <label><small>Puede firmar manteniendo pulsado el botón del ratón, con una tableta digitalizadora o usando el dedo si está con una tablet o un móvil.</small></label>
        <div id="div_lienzo" >
            <canvas id="firmaCanvas" width="400" height="200" style="background-color:white; border: 1px solid black;"></canvas>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
<script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
<script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
<script src=<?php echo "js/exenc.js?q=".time();?> type="text/javascript"></script>
<script src=<?php echo "js/validadores_exenfct.js?q=".time();?> type="text/javascript"></script>
</html>