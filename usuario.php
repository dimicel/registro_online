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
    <link rel="stylesheet" href=<?php echo "css/est.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "jqueryui/jquery-ui.min.css?q=".time();?> />
    <link rel="stylesheet" href=<?php echo "js/croppie/croppie.css?q=".time();?> type="text/css">
    <title>Tramitación online de documentación - IES UNIVERSIDAD LABORAL</title>
</head>

<body>
    <!--PRINCIPAL _____________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="main" style="display:inline-block; width: 800px;" class="centrado ui-widget-header ui-corner-all alertas">
        <div style="display:table-cell; padding-left:10px; padding-top: 10px">
            <img src="recursos/escudo.jpg" width="115" height="105" alt="Escudo_Uni">
        </div>
        <div style="display:table-cell; height: 105px; vertical-align: middle; padding-left: 20px">
            <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
            <h3>IES UNIVERSIDAD LABORAL</h3>
            <h4>TRAMITACIÓN ONLINE DE SOLICITUDES</h4>
            <h5 style="color:brown">PANEL DE CONTROL DEL USUARIO</h5>
        </div>
        <div id="apartados">
            <ul>
                <li><a href="#misgestiones" onclick="listaSolicitudes()">Mis Gestiones</a></li>
                <li><a href="#impresos">Solicitudes</a></li>
            </ul>
            
            <div id="misgestiones" class="container">
                <div class="row justify-content-center">
                    <span style="font-size:14px; color:green">Complete sus datos personales en 'Mis Datos'. Le facilitará el proceso a la hora de cumplimentar formularios.</span>
                </div>
                <div id="menu_div" class="row justify-content-center">
                    <div class="col">
                        <!--<nav class="navbar" style="background-color: rgb(63, 151, 63);">-->
                        <ul class="nav bg-success">
                            <li class="nav-item" id="menu1">
                                <a class="nav-link" style="color:white;font-size: 0.8em;" href="#" onclick="javascript: cambioDatosPers();">Mis Datos</a>
                            </li>
                            <li class="nav-item" id="menu2">
                                <a class="nav-link" style="color:white;font-size: 0.8em;" href="#" onclick="javascript: cambioPassword();">Cambiar Contraseña</a>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle " style="color:white;font-size: 0.8em;" id="menu3" href="#" data-toggle="dropdown">
                                        Documentos adjuntos
                                    </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('foto');">Fotografía Alumno</a>
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('dni');">Documento Identificación</a>
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('seguro');">Resguardo Seguro Escolar</a>
                                    <!--<a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('certificado');">Certificado Notas</a>--> 
                                </div>
                            </li>
                                            
                            <li class="nav-item" id="menu4">
                                <a class="nav-link" style="color:white;font-size: 0.8em;" href="#" onclick="javascript: cierraSesion();">Salir</a>
                            </li>

                        </ul>
                        <!--</nav>-->
                    </div>
                </div>
                <!--<div style="clear:both"></div>-->
                <div class="row">
                    <div class="col">
                        <h5 style="color:brown;margin-top:20px;">Solicitudes Registradas</h5>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-auto p-0" style="padding:0px;">
                        <div id="div_solicitudes" style="overflow-y:auto; height: 400px; padding: 0;">
                            <table id="solicitudes"  ></table>
                        </div>
                    </div>
                </div>
            </div>
            <div id="impresos">
                <span>AMPA</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="ampa" class="enlaceEnabled" href="https://ampauniversidadlaboraltoledo.es/se-socio/" target="_blank">¡¡¡HAZTE SOCIO DEL AMPA!!!</a>
                </div>

                <span>MATRÍCULA</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_mat_eso" class="enlaceEnabled" href="#" onclick="javascript:lanzaAvisoMatricula(eso);">Matrícula de ESO</a>
                </div>

                <div style="display:list-item; margin-left:50px">
                    <a id="docs_mat_bach" class="enlaceEnabled" href="#" onclick="javascript:lanzaAvisoMatricula(bach);">Matrícula de Bachillerato</a>
                </div>

                <div style="display:list-item; margin-left:50px">
                    <a id="docs_mat_ciclos" class="enlaceEnabled" href="#" onclick="javascript:lanzaAvisoMatricula(ciclos);">Matrícula de Ciclos Formativos (Presencial y E-Learning)</a>
                </div>

                <div style="display:list-item; margin-left:50px">
                    <a id="docs_mat_ciclos-e" class="enlaceEnabled" href="#" onclick="javascript:lanzaAvisoMatricula(ciclos);">Matrícula de Ciclos E-Learning (Sólo FCT y Proyecto)</a>
                </div>

                <div style="display:list-item; margin-left:50px">
                    <a id="docs_mat_fpb" class="enlaceEnabled" href="#" onclick="javascript:lanzaAvisoMatricula(fpb);">Matrícula de Grado Básico (antigua FPB)</a>
                </div>

                <span>PREMATRÍCULA</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_premat_eso" class="enlaceEnabled" href="" target="_self">Prematrícula de ESO</a><br>
                </div>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_premat_bach" class="enlaceEnabled" href="" target="_self">Prematrícula de Bachillerato</a><br>
                </div>

                <span>FORMACIÓN PROFESIONAL</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_convalidaciones" class="enlaceEnabled" href="impresos/convalidaciones/convalidaciones.php" target="_self">Convalidaciones</a><br>
                </div>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_exencion_fct" class="enlaceEnabled" href="impresos/exencion_fct/exencion_fct.php"  target="_self">Exención de Período de Formación en Empresas (PFE, antigua FTC)</a><br>
                </div>

                <span>OTROS</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_transporte_escolar" class="enlaceEnabled" href="impresos/transporte/transporte.php" target="_self">Transporte Escolar</a><br>
                </div>

                <span>RESIDENCIA</span>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_residencia" class="enlaceEnabled" href="impresos/residencia/res.php" target="_self">Residencia (sólo para alumnos con plaza asignada para residencia)</a><br>
                </div>
                <div style="display:list-item; margin-left:50px">
                    <a id="docs_residencia" class="enlaceEnabled" href="impresos/orden_sepa/sepa.php" target="_self">Orden SEPA (sólo para RESIDENTES NO BONIFICADOS, si han cambiado sus datos bancarios)</a><br>
                </div>
                <!--
                <span>SOLICITUDES DE REVISIÓN</span>
                <div style="display:list-item; margin-left:50px">
                    <a class="enlaceEnabled" href="impresos/revision_examen/revision_examen.html" target="_self">Revisión de Examen</a><br>
                </div>
                <div style="display:list-item; margin-left:50px">
                    <a class="enlaceEnabled" href="impresos/revision_calificacion/revision_calificacion.html" target="_self">Revisión de Calificación</a><br>
                </div>
                -->
            </div>
        </div>
    </div>
    <!--_______________________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->

    <!--FORMULARIO MODIFICACIÓN DATOS USUARIO _________________________________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="div_mod_datos" style="display: none;  padding:10px;" class="ui-widget-header ui-corner-all alertas">
        <form id="form_mod_datos">
            <input type="hidden" id="dat_idnie" name="dat_idnie">
            <div class="row justify-content-center" style="color:green">
                <div class="col">
                    <span>¡¡¡ATENCIÓN!!! - SI HAY UN ERROR EN EL NOMBRE, APELLIDOS, NIF DEL ALUMNO O EL CORREO PARA RECUPERACIÓN DE CONTRASEÑA, PÓNGASE EN CONTACTO CON LA SECRETARÍA DEL CENTRO</span>
                </div>
            </div>
            <hr>
            <div class="row" style="color:brown">
                <div class="col">
                    <span>DATOS PERSONALES DEL ALUMNO</span>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <label for="dat_sexo" class="etiquetas">Sexo:</label>
                    <span class="errorTxt"></span>
                    <select id="dat_sexo" name="dat_sexo" class="form-control">
                        <option value=""></option>
                        <option value="H">Hombre</option>
                        <option value="M">Mujer</option>
                    </select>
                </div>
                <div class="col-3">
                    <label for="dat_fecha_nac" class="etiquetas">Fecha Nacimiento:</label>
                    <input type="text" class="form-control" id="dat_fecha_nac" name="dat_fecha_nac" maxlength="10" size="10"  placeholder="dd/mm/aaaa">
                </div>
                <div class="col-3">
                    <label for="dat_telefono" class="etiquetas">Teléfono del alumno:</label>
                    <input type="text" class="form-control" id="dat_telefono" name="dat_telefono" maxlength="12" size="12">
                </div>
                <div class="col">
                    <label for="dat_email" class="etiquetas">Email alumno:</label>
                    <input type="email" class="form-control" id="dat_email" name="dat_email" maxlength="80" size="80">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="dat_nss" class="etiquetas">Nº Seguridad Social:
                    <small>Para FCT. Sólo alumnos de Ciclos Formativos de Grado Básico, Medio y Superior</small></label>
                    <input type="text" class="form-control" id="dat_nss" name="dat_nss" maxlength="12" size="12">
                </div>
            </div>
            <hr>
            <div class="row" style="color:brown">
                <div class="col">
                    <span>DATOS RELATIVOS AL DOMICILIO</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="dat_direccion" class="etiquetas">Dirección:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_direccion" name="dat_direccion" class="form-control" maxlength="90" size="90"/>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <label for="dat_cp" class="etiquetas">CP:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_cp" name="dat_cp" class="form-control" maxlength="5" size="5"/>
                </div>
                <div class="col">
                    <label for="dat_localidad" class="etiquetas">Localidad:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_localidad" name="dat_localidad" class="form-control" maxlength="35" size="35"/>
                </div>
                <div class="col">
                    <label for="dat_provincia" class="etiquetas">Provincia:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_provincia" name="dat_provincia" class="form-control" maxlength="35" size="35"/>
                </div>
            </div>
            <hr>
            <div class="row" style="color:brown">
                <div class="col">
                    <span>REPRESENTANTES LEGALES (SI PROCEDE)</span>
                </div>
            </div>
            <div class="row" style="color:brown">
                <div class="col">
                    <span style="font-size:18px">PADRE/MADRE/TUTOR/A LEGAL 1</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="dat_tutor1" class="etiquetas">Nombre y apellidos:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_tutor1" name="dat_tutor1" class="form-control" maxlength="40" size="40"/>
                </div>
                <div class="col-2">
                    <label for="dat_telef_tut1" class="etiquetas">Teléfono:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_telef_tut1" name="dat_telef_tut1" class="form-control" maxlength="12" size="12"/>
                </div>
                <div class="col-4">
                    <label for="dat_email_tut1" class="etiquetas">Correo electrónico:</label>
                    <span class="errorTxt"></span>
                    <input type="email" id="dat_email_tut1" name="dat_email_tut1" class="form-control" maxlength="40" size="40"/>
                </div>
            </div>
            <div class="row" style="color:brown">
                <div class="col">
                    <span style="font-size:18px">PADRE/MADRE/TUTOR/A LEGAL 2</span>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <label for="dat_tutor2" class="etiquetas">Nombre y apellidos:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_tutor2" name="dat_tutor2" class="form-control" maxlength="40" size="40"/>
                </div>
                <div class="col-2">
                    <label for="dat_telef_tut2" class="etiquetas">Teléfono:</label>
                    <span class="errorTxt"></span>
                    <input type="text" id="dat_telef_tut2" name="dat_telef_tut2" class="form-control" maxlength="12" size="12"/>
                </div>
                <div class="col-4">
                    <label for="dat_email_tut2" class="etiquetas">Correo electrónico:</label>
                    <span class="errorTxt"></span>
                    <input type="email" id="dat_email_tut2" name="dat_email_tut2" class="form-control" maxlength="40" size="40"/>
                </div>
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
    <script src=<?php echo "js/usuario.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>
</body>

</html>