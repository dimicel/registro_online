<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Expires" content="-1">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" href=<?php echo "../../css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <title>CONVALIDACIONES</title>
</head>

<body>
    <div style="display:none">
        <form id="convalidaciones" name="convalidaciones" class="needs-validation" novalidate method="post">
            <input type="hidden" name="anno_curso" id="anno_curso" />
            <input type="hidden" name="id_nie" id="id_nie" />
            <input type="hidden" name="tlf_movil" id="tlf_movil" />
            <input type="hidden" name="tlf_fijo" id="tlf_fijo" />
            <input type="hidden" name="email" id="email" />
        </form>
    </div>
    <header>
        <div class="container w-100">
            <!--CABECERA  ------------------------------------------------------------------------------------>
            <div class="d-flex flex-row justify-content-center" style="margin-top:30px">
                <div class="col-2 justify-content-start">
                    <input style="width:175px; height:100px" type="image" src="recursos/logo_ccm.jpg" />
                </div>
                <div class="col-8 justify-content-center" style="text-align: center;">
                    <h5>IES UNIVERSIDAD LABORAL DE TOLEDO</h5>
                    <h5>REGISTRO ONLINE</h5>
                    <h6>SOLICITUD: CONVALIDACIONES</h6><br>
                    <h5 id="rotulo_curso" style="color:#900; font-weight:bold">CURSO ACTUAL:</h5>
                </div>
                <div class="col-2 justify-content-end">
                    <input style="width:150px; height:150px" type="image" src="recursos/mini_escudo.jpg" />
                </div>
            </div>
        </div>
    </header>
    <section id="seccion-intro">
        <div class="container w-100">
            <div class="row">
                <div class="col">
                    <input type="button" value="&#x21c7;" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Regresar a página principal" onclick="javascript:window.history.back();" />
                </div>
            </div>
            <div class="d-flex row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <input type="button" id="instrucciones" class="btn btn-success textoboton" value="Instrucciones" onclick="seleccion(this)"><br />
                            <input style="margin-top:10px" type="button" id="consejeria" class="btn btn-success textoboton" value="Anexo X -> Convalidaciones  cuyo reconocimiento corresponde a la Consejería de Educación" onclick="seleccion(this)" /><br />
                            <input style="margin-top:10px" type="button" id="centro_ministerio" class="btn btn-success textoboton" value="Anexo V -> Convalidaciones cuyo reconocimiento corresponde al Centro y al Ministerio de Educación" onclick="seleccion(this)" />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <h4 style="color:brown">RESOLUCIÓN DE LAS CONVALIDACIONES</h4>
                            <p>Las convalidaciones resueltas positivamente por la Dirección del centro serán reconocidas automáticamente en el expediente del alumno.</p>
                            <p>Para que las convalidaciones resueltas por Consejería o el Ministerio sean reconocidas en el expediente, será necesario que el alumno presente la resolución positiva en el centro.</p>  
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </section>

    <section id="seccion-consejeria" style="display:none">
        <div class="container w-100">
            <div class="d-flex flex-row">
                <div class="col">
                    <div class="row d-flex w-100 justify-content-center">
                        <div class="col">
                            <h3>SOLICITUD CONVALIDACIONES PARA EL MINISTERIO</h3>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <input type="button" id="volver" class="btn btn-success textoboton" value="<<< Volver" onclick="vuelve()"/>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-1">
                            <label>Nombre</label>
                        </div>
                        <div class="col-1 offset-4">
                            <label>Apellidos</label>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col-5">
                            <input type="text" name="nombre" id="nombre" size="40" maxlength="40"/>
                        </div>
                        <div class="col">
                            <input type="text" name="apellidos" id="apellidos" size="60" maxlength="60"/>
                        </div>
                    </div>
                        <div class="ui-widget-header ui-corner-all ancho" style="display:inline-block; padding-left:10px; padding-right:10px; padding-bottom:10px;">
                            <div style="display:inline-block; margin-top:20px">
                                <div style="display:inline-block;">
                                    <br>
                                    <input type="radio" name="pass_nif" id="nif" value="nif" checked/><label>NIF/NIE</label>
                                    <input style="margin-left: 20px" type="radio" name="pass_nif" id="pass" value="pass"/><label>Pasaporte</label>
                                    <label style="margin-left: 20px">Nº documento:</label>
                                    <input style="margin-left: 5px" type="text" name="nif_nie" id="nif_nie" size="10" maxlength="9" title="Sin espacios ni guiones" /><br><br>
                                    <label>Dirección (calle, plaza, número, escalera, puerta ...</label>
                                    <label style="margin-left:145px">C.P.</label>
                                    <input name="direccion" type="text" id="direccion" size="70" maxlength="70" />
                                    <input style="margin-left:6px" name="cp" type="text" id="cp" size="6" maxlength="5" />
                                </div>
                                <div style="clear:both"></div>
                                <div style="display:inline-block;">
                                    <label>Localidad</label><br /><input type="text" name="localidad" id="localidad" size="35" maxlength="35" />
                                </div>
                                <div style="display:inline-block;">
                                    <label>Provincia</label><br /><input type="text" name="provincia" id="provincia" size="25" maxlength="25" />
                                </div>
                                <div style="clear:both"></div>
                                <div style="display:inline-block;">
                                    <label>Tlf. Fijo:</label><br /><input type="text" name="tlf_dom" id="tlf_dom" size="10" maxlength="9" />
                                </div>
                                <div style="display:inline-block; margin-left:10px">
                                    <label>Tlf. Móvil:</label><br /><input type="text" name="tlf_mov" id="tlf_mov" size="10" maxlength="9" />
                                </div>
                                <div style="display:inline-block; margin-left:10px">
                                    <label>Email:</label><br /><input type="text" name="email" id="email" size="30" maxlength="30" />
                                </div>
                            </div>
                        </div>



                        <div id="expone" class="ui-widget-header ui-corner-all ancho" style="display:inline-block; padding-left:10px; padding-right:10px; padding-bottom:10px; margin-top:10px;">
                            <p style="font-size: larger; color: #900; font-weight: bold;">MÓDULOS QUE SOLICITA CONVALIDAR</p>
                            <div id="div_ciclo" style="display:inline-block">
                                <label>Ciclo Formativo de Grado</label>
                                <select name="grado" id="grado" size="1" onchange="selGrado(this)">
                                    <option value=""></option>
                                    <option value="Medio">Medio</option>
                                    <option value="Superior">Superior</option>
                                </select> 
                            </div>
                            <div id="div_grado_medio" style="display:none; margin-left:10px">
                                <label>Denominación:</label>
                                <select name="gmedio" id="gmedio" size="1">
                                    <option value=""></option>
                                    <option value="Cocina y Gastronomía">Cocina y Gastronomía</option>
                                    <option value="Gestión Administrativa">Gestión Administrativa</option>
                                    <option value="Instalaciones Eléctricas y Automáticas">Instalaciones Eléctricas y Automáticas</option>
                                    <option value="Instalaciones Frigoríficas y de Climatización">Instalaciones Frigoríficas y de Climatización</option>
                                    <option value="Instalaciones de Producción de Calor">Instalaciones de Producción de Calor</option>
                                    <option value="Panadería, Repostería y Confitería">Panadería, Repostería y Confitería</option>
                                    <option value="Servicios en Restauración">Servicios en Restauración</option>
                                </select>
                            </div>
                            <div id="div_grado_superior" style="display:none; margin-left:10px">
                                <label>Denominación: </label>
                                <select name="gsuperior" id="gsuperior" size="1">
                                    <option value=""></option>
                                    <option value="Administración y Finanzas">Administración y Finanzas</option>
                                    <option value="Agencias de Viajes y Gestión de Eventos">Agencias de Viajes y Gestión de Eventos</option>
                                    <option value="Asistencia a la Dirección">Asistencia a la Dirección</option>
                                    <option value="Automatización y Robótica Industrial">Automatización y Robótica Industrial</option>
                                    <option value="Dirección de Cocina">Dirección de Cocina</option>
                                    <option value="Gestión de Alojamientos Turísticos">Gestión de Alojamientos Turísticos</option>
                                    <option value="Guía, Información y Asistencia Turísticas">Guía, Información y Asistencia Turísticas</option>
                                    <option value="Mantenimiento de Instalaciones Térmicas y Fluidos">Mantenimiento de Instalaciones Térmicas y Fluidos</option>
                                    <option value="Sistemas Electrotécnicos y Automatizados">Sistemas Electrotécnicos y Automatizados</option>
                                </select>
                            </div>
                            <div style="clear:both"></div>
                            <div style="margin-top: 10px">
                                <label>Módulos que solicita convalidar: (escribirlos uno a continuación del otro y separados por punto y coma (;), poniendo delante el código del módulo según indica el Real Decreto que establece el Título del Ciclo Formativo al que corresponde, seguio de un guión.)</label><br />
                                <label>Ejemplo: 0966-Robótica Industrial; 0967-Comunicaciones Industriales</label><a style="margin-left:100px; color:#00C" href="docs/modulos_y_codigos.htm" target="_blank">Ver Códigos y Módulos</a><br />
                                <textarea name="modulos" cols="107" rows="3" id="modulos" maxlength="630"></textarea>  
                            </div>
                            <div style="clear:both"></div>
                            <div style="margin-top: 10px">
                                <label>Estudios que aporta (indicar si son LOGSE/ LOE/Estudios universitarios/Otros)</label><br />
                                <textarea name="estudios" cols="107" rows="3" id="estudios" maxlength="321"></textarea>  
                            </div>
                        </div>

                        <div style="display:inline-block; margin-top:10px; margin-bottom:30px">
                            <input type="button" id="generar" value="GENERAR IMPRESO" onclick="generaImpreso()"/>
                        </div>
                    
                </div>
            </div>
        </div>        
    </section>





    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/convalidaciones.js?q=".time();?> type="text/javascript"></script>

</body>

</html>