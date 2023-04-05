<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href=<?php echo "css/rev.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?> />
    <title>REVISIÓN DE CALIFICACIÓN</title>
</head>

<body>
    <center>
        <div id="logos" style="width:1080px">
            <div style="display:inline-block; float:left">
                <input style="width:175px; height:100px" type="image" src="../../recursos/logo_ccm.jpg" />
            </div>
            <div style="display:inline-block; float:right">
                <input style="width:150px; height:150px" type="image" src="../../recursos/mini_escudo.jpg" />
            </div>
        </div>
    </center>
    <div style="clear:both"></div>



    <div class="centrado ancho">
        <div class="ancho" style="text-align:center">
            <label style="font-size:large; font-weight:bold; color:black !important; font-size: 1.5em !important">SOLICITUD DE REVISIÓN DE CALIFICACIÓN</label><br /><br />
        </div>
        <form id="rev_cal" name="rev_cal">
            <div class="ui-widget-header ui-corner-all ancho" style="display:inline-block; padding-left:10px; padding-right:10px; padding-bottom:10px; ">
                <div style="display:inline-block;">
                    <br>
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
                    <label>Domicilio: </label>
                    <input type="text" name="domicilio" id="domicilio" size="50" maxlength="70" />
                    <label style="margin-left: 5px">Teléfono: </label>
                    <input type="tel" name="telefono" id="telefono" size="15" maxlength="15" /><br/><br/>
                    <label style="margin-left: 5px">Población: </label>
                    <input type="text" name="poblacion" id="poblacion" size="30" maxlength="35" />
                    <label style="margin-left: 5px">CP: </label>
                    <input type="text" name="cp" id="cp" size="5" maxlength="5" />
                    <label style="margin-left: 5px">Provincia: </label>
                    <input type="text" name="provincia" id="provincia" size="20" maxlength="20" /><br/><br/>
                    <label>Cursa Ciclo de Grado: </label>
                    <select name="grado" id="grado" size="1" onchange="selGrado(this)">
                            <option value="">Seleccionar...</option>
                            <option value="medio">Medio</option>
                            <option value="superior">Superior</option>
                        </select>
                    <label style="margin-left: 5px">denominado: </label>
                    <input type="text" name="ciclo" id="ciclo" size="40" maxlength="80" readonly/><br/><br/>
                    <label>Módulo: </label>
                    <input type="text" name="modulo" id="modulo" size="50" maxlength="70" />
                    <label style="margin-left: 5px">Nota obtenida: </label>
                    <input type="text" name="nota" id="nota" size="5" maxlength="5" /><br/><br/>
                    <label>Exponer los motivos por los que se solicita la revisión de la nota: </label><br>
                    <textarea name="razones" id="razones" cols="100" rows="5" maxlength="500"></textarea><br/><br/>
                </div>
            </div>
            <input type="hidden" id="id_nie" name="id_nie" />
            <input type="hidden" id="email" name="email" />
            <input type="hidden" id="id_nif" name="id_nif" />
            <input type="hidden" id="usuario" name="usuario" />
            <input type="hidden" id="num_registro" name="num_registro" />
        </form>
        <div style="clear:both"></div>
        <center>
            <div style="display:inline-block; margin-top:10px; margin-bottom:30px">
                <input type="button" id="generar" value="REGISTRAR ONLINE" onclick="iniciaRegistro()" />
                <input type="button" id="cancelar" value="Cancelar" onclick="cancelaRegistro()" />
            </div>
        </center>

    </div>

    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>

    <script src=<?php echo "../../js/jquery-1.12.1.min.js?q=".time();?></script> type="text/javascript"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?></script>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?></script>></script>
    <script src=<?php echo "js/rev.js?q=".time();?></script> type="text/javascript"></script>

    <script type="text/javascript">
        $("#generar").button();
        $("#cancelar").button();
    </script>
</body>

</html>