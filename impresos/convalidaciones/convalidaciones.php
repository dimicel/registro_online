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
        <div class="container w-100 ui-widget-header ui-corner-all">
            <div class="flex-row">
                <div class="col">
                    <input type="button" value="&#x21c7;" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Regresar a página principal" onclick="javascript:window.history.back();" />
                </div>
            </div>
            <div class="flex-row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <center>
                            <input type="button" id="instrucciones" class="btn btn-success textoboton" value="Instrucciones" onclick="seleccion(this)">
                            <input type="button" id="consejeria" class="btn btn-success textoboton" value="Convalidaciones  Consejería de Educación" onclick="seleccion(this)" />
                            <input type="button" id="centro_ministerio" class="btn btn-success textoboton" value="Convalidaciones Centro Educativo y Ministerio de Educación" onclick="seleccion(this)" />
                            </center>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <h5 style="color:brown; font-weight:bold">RESOLUCIÓN DE LAS CONVALIDACIONES</h5>
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
                            <h5>SOLICITUD CONVALIDACIONES PARA EL MINISTERIO</h5>
                        </div>
                    </div>
                    <div class="row ">
                        <div class="col">
                            <input type="button" id="volver" class="btn btn-success textoboton" value="<<< Volver" onclick="vuelve()"/>
                        </div>
                    </div>
                    <div class="row mt-1 ui-widget-header ui-corner-all">
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h6 style="color:brown">DATOS PERSONALES</h6>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-1">
                                    <label>Nombre</label>
                                </div>
                                <div class="col-1 offset-3">
                                    <label>Apellidos</label>
                                </div>
                                <div class="col-2 offset-3">
                                    <label>NIF/NIE/Pasaporte</label>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-3">
                                    <input type="text" name="nombre" id="nombre" size="30" maxlength="40"/>
                                </div>
                                <div class="col-4">
                                    <input type="text" name="apellidos" id="apellidos" size="50" maxlength="60"/>
                                </div>
                                <div class="col">
                                <input style="margin-left: 5px" type="text" name="nif_nie" id="nif_nie" size="10" maxlength="9"/><br><br>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label>Localidad</label>
                                </div>
                                <div class="col-1 offset-3">
                                    <label>Provincia</label>
                                </div>
                                <div class="col-1 offset-3">
                                    <label>CP</label>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-4">
                                    <input type="text" name="localidad" id="localidad" size="35" maxlength="35" />
                                </div>
                                <div class="col-4">
                                    <input type="text" name="provincia" id="provincia" size="25" maxlength="25" />
                                </div>
                                <div class="col">
                                    <input type="text" name="cp" id="cp" size="5" maxlength="5" />
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-1">
                                    <label>Tlf. Fijo</label>
                                </div>
                                <div class="col-1 offset-3">
                                    <label>Tlf. Móvil</label>
                                </div>
                                <div class="col-1 offset-3">
                                    <label>Email</label>
                                </div>
                            </div>
                            <div class="row ">
                                <div class="col-4">
                                    <input type="text" name="tlf_fijo" id="tlf_fijo" size="10" maxlength="9" />
                                </div>
                                <div class="col-4">
                                    <input type="text" name="tlf_movil" id="tlf_movil" size="10" maxlength="9" />
                                </div>
                                <div class="col">
                                    <input type="text" name="email" id="email" size="30" maxlength="30" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="expone" class="row ui-widget-header ui-corner-all " >
                        <div class="col">
                            <div class="row">
                                <div class="col">
                                    <h6 style="color:brown">MÓDULOS QUE SOLICITA CONVALIDAR</h6>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-4">
                                    <div id="div_ciclo" style="display:inline-block">
                                        <label>Ciclo Formativo de Grado</label>
                                        <select name="grado" id="grado" size="1" onchange="selGrado(this)">
                                            <option value="">Seleccione uno...</option>
                                            <option value="Medio">Medio</option>
                                            <option value="Superior">Superior</option>
                                        </select> 
                                    </div>
                                </div>
                                <div class="col">
                                    <div id="div_grado_medio" style=" margin-left:10px">
                                        <label>Denominación:</label>
                                        <select name="ciclos" id="ciclos" size="1">
                                        <option value="">Seleccione grado...</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <label>Módulos que solicita convalidar (<a style="margin-left:10px; color:#00C" href="#" >Clic AQUÍ para añadir o quitar módulos</a>)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    <textarea name="modulos" cols="107" rows="3" id="modulos" maxlength="1000" readonly></textarea>  
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                <label>Estudios que aporta (indicar si son LOGSE/ LOE/Estudios universitarios/Otros)</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                <textarea name="estudios" cols="107" rows="3" id="estudios" maxlength="1000"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-row mt-1 w-100 justify-content-center">
                        <div class="col">
                            <input type="button" class="btn btn-success textoboton" id="generar" value="REGISTRAR SOLICITUD" onclick="registraSol()"/>
                        </div>
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