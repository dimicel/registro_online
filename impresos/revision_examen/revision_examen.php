<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="css/rev.css" type="text/css">
    <link rel="stylesheet" href="../../jqueryui/jquery-ui.min.css" />
    <title>REVISIÓN DE EXAMEN</title>
</head>

<body>

    <center>
        <div id="logos" style="width:1080px">
            <div style="display:block; float:left">
                <input style="width:175px; height:100px" type="image" src="../../recursos/logo_ccm.jpg" />
            </div>
            <div style="display:block; float:right">
                <input style="width:150px; height:150px" type="image" src="../../recursos/mini_escudo.jpg" />
            </div>
        </div>
    </center>
    <div style="clear:both"></div>

    <div class="centrado ancho">
        <div class="ancho" style="text-align:center">
            <label style="font-size:large; font-weight:bold; color:black !important; font-size: 1.5em !important">SOLICITUD DE REVISIÓN DE EXAMEN</label><br /><br />
        </div>

        <div class="ui-widget-header ui-corner-all ancho" style="display:inline-block; padding-left:10px; padding-right:10px; padding-bottom:10px; ">
            <form id="rev_exa" name="rev_exa">
                <div style="display:inline-block;">
                    <br>
                    <label style="margin-left:65px">Nombre y Apellidos</label><br>
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
                    <label>En calidad de </label>
                    <input type="radio" name="padres" id="alumno" value="alumno" onClick="habAlumno()" /><label>Alumno/a</label>
                    <input style="margin-left: 20px" type="radio" name="padres" id="padre" value="padre" checked onClick="habAlumno()" /><label>Padre/Madre</label>
                    <input style="margin-left: 20px" type="radio" name="padres" id="tutor" value="tutor" onClick="habAlumno()" /><label>Tutor/a</label>
                    <br><br>
                    <div id="en_calidad_de">
                        <label>del alumno/a </label>
                        <input type="text" name="alum" id="alum" size="70" maxlength="90" /><br/><br/>
                    </div>
                    <label>que cursa </label>
                    <input type="text" name="curso" id="curso" size="70" maxlength="90" /><br/><br/>
                    <label>solicita la prueba escrita al departamento de </label><input type="text" name="dpto" id="dpto" size="40" maxlength="50" /><br/><br/>
                    <label>Profesor/a examinador/a </label>
                    <input type="text" name="profesor" id="profesor" size="70" maxlength="90" /><br/><br/>
                    <label>de la asignatura/módulo </label>
                    <input type="text" name="asignatura" id="asignatura" size="70" maxlength="90" /><br/><br/>
                    <label>realizada con fecha </label>
                    <input type="text" name="fecha" id="fecha" size="15" maxlength="10" /><br/><br/>
                </div>
                <input type="hidden" id="id_nie" name="id_nie" />
                <input type="hidden" id="email" name="email" />
                <input type="hidden" id="id_nif" name="id_nif" />
                <input type="hidden" id="usuario" name="usuario" />
                <input type="hidden" id="num_registro" name="num_registro" />
            </form>
        </div>
        <div style="clear:both"></div>
        <center>
            <div style="display:inline-block; margin-top:10px; margin-bottom:30px">
                <input type="button" id="generar" value="Registrar Online" onclick="iniciaRegistro()" />
                <input type="button" id="cancelar" value="Cancelar" onclick="cancelaRegistro()" />
            </div>
        </center>

    </div>

    
    <script src="../../js/jquery-1.12.1.min.js" type="text/javascript"></script>
    <script src="../../jqueryui/jquery-ui.min.js"></script>
    <script src="../../js/comun.js"></script>
    <script src="js/rev.js" type="text/javascript"></script>

    <script type="text/javascript">
        $("#generar").button();
        $("#cancelar").button();
    </script>
</body>

</html>