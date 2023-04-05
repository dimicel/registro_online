<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href=<?php echo "css/exenc.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "jqueryui/jquery-ui.min.css?q=".time();?> />
    <title>SOLICITUD EXENCIÓN DE FCT</title>
</head>

<body data-ng-app="exencFCT" ng-controller="ctrlFormacion">
    <script src=<?php echo "js/jquery-1.12.1.min.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/angular.js?q=".time();?>></script>
    <script src=<?php echo "js/controladores.js?q=".time();?>></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "js/exenc.js?q=".time();?> type="text/javascript"></script>

    <form id="exenc" name="exenc">
        <div style="display:table-cell; float:left">
            <input style="width:175px; height:100px" name="" type="image" src="recursos/logo_ccm.jpg" />
        </div>

        <div style="display:table-cell; float:right">
            <input style="width:150px; height:150px" name="" type="image" src="recursos/mini_escudo.jpg" />
        </div>

        <div style="clear:both">
            <div class="centrado ancho">
                <div class="ancho" style="text-align:center">
                    <label style="font-size:large; font-weight:bold">SOLICITUD DE EXENCIÓN DEL MÓDULO DE FORMACIÓN EN CENTROS DE TRABAJO</label><br /><br />
                </div>

                <div class="ui-widget-header ui-corner-all ancho" style="display:inline-block; padding:10px; ">
                    <div style="display:inline-block;">
                        <label style="margin-left:10px">Nombre y Apellidos</label><br>
                        <select name="lista_don" id="lista_don" size="1" onchange="seleccionListaDon()">
                        <option value=""></option>
                        <option value="D.">D.</option>
                        <option value="Dña.">Dña.</option>
                    </select>
                        <input type="text" name="nombre" id="nombre" size="70" maxlength="90" value="Seleccione en el desplegable de la izquierda." readonly="readonly" /><br/><br/>
                        <input type="radio" name="pass_nif" id="nif" value="nif" checked/><label>NIF/NIE</label>
                        <input style="margin-left: 20px" type="radio" name="pass_nif" id="pass" value="pass" /><label>Pasaporte</label>
                        <label style="margin-left: 20px">Nº documento:</label>
                        <input style="margin-left: 5px" type="text" name="nif_nie" id="nif_nie" size="10" maxlength="9" title="Sin espacios ni guiones" />
                        <br /><br>
                    </div>
                    <div style="clear:both"></div>
                    <div id="div_formacion" style="display:inline-block">
                        <label>Formación:</label><br />
                        <select name="formacion" id="formacion" size="1" ng-change="cambiaTipoForm()" ng-model="valTipoForm" ng-options="formac for formac in tipoForm">
                    </select>
                    </div>
                    <div id="div_grado_medio" ng-show="muestraGradoMedio" style="display:inline-block">
                        <label>Denominación:</label><br />
                        <select name="gmedio" id="gmedio" size="1" ng-options="x for x in formGM" ng-model="valFormGM" ng-change="cambiaFormGM()">
                    </select>
                    </div>
                    <div id="div_grado_superior" ng-show="muestraGradoSuperior" style="display:inline-block">
                        <label>Denominación: </label><br />
                        <select name="gsuperior" id="gsuperior" size="1" ng-options="x for x in formGS" ng-model="valFormGS" ng-change="cambiaFormGS()">
                    </select>
                    </div>
                    <div id="div_fpb" ng-show="muestraFPB " style="display:inline-block ">
                        <label>Denominación: </label><br />
                        <select name="fpb " id="fpb " size="1" ng-options="x for x in formFPB" ng-model="valFormFPB" ng-change="cambiaFormFPB()">
                    </select>
                    </div>
                    <div style="clear:both "></div>
                    <br>
                    <label>Documentación que aporta:</label><br />
                    <textarea name="documentacion " cols="90 " rows="6 " id="documentacion "></textarea>
                </div>

                <center>
                    <div style="display:inline-block; margin-top:10px; margin-bottom:30px ">
                        <input type="button" id="generar" value="REGISTRAR ONLINE" onclick="iniciaGeneraPdf() " />
                    </div>
                </center>

            </div>
        </div>

        <div id="div_email " class="ui-widget-header ui-corner-all alertas " title="Exención de FCT - Email requerido ">
            <p>El e-mail que se le pide debe ser válido, puesto que será al que le llegue el número de registro asignado y el impreso generado en formato pdf como fichero adjunto.</p>
            <p>Una vez lo haya recibido, podrá entrar en el sistema por Secretaría->Impresos de Secretaría->Registro Electrónico->Estado del Registro para buscar su solicitud y verificar su estado.</p>
            <p>OBSERVACIONES IMPORTANTES:<br> -DEBE ENVIAR EL Nº DE REGISTRO POR <strong>PAPAS 2.0</strong>, AL GRUPO <strong>'Coordinadores de mi centro'</strong>. DE LO CONTRARIO, EL FORMULARIO NO SE CONSIDERARÁ FIRMADO Y NO SERÁ VÁLIDO.<br> -SI VE QUE
                NO RECIBE EL CORREO ELECTRÓNICO, REVISE LA CARPETA SPAM O CORREO NO DESEADO.<br> -SI EN EL PLAZO DE 24/48 HORAS EL PERSONAL DE SECRETARÍA NO HA RECIBIDO SU SOLICITUD, POR FAVOR, PÓNGASE EN CONTACTO CON ELLOS EN EL TELÉFONO 925 22 34 00
                EXTENSIONES 272 Y 236</p>
            <center>
                <div style="display:inline-block; width:100px; text-align: right ">
                    <label class="etiquetas ">E-mail:</label><br>
                    <label class="etiquetas ">Repita E-mail:</label>

                </div>
                <div style="display:inline-block ">
                    <input type="text " style="margin-left: 10px; width:300px " id="email2 " name="email2 "><br>
                    <input type="text " style="margin-left: 10px; width:300px " id="email3 " name="email3 ">
                </div>
            </center>
        </div>


        <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url( 'recursos/espera.gif') no-repeat center center; opacity: .7; "></div>

        <div id="mensaje_div" style="display:none "></div>
        <script type="text/javascript ">
            $("#generar").button();
        </script>
</body>

</html>