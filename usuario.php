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
                <li><a href="#misgestiones">Mis Gestiones</a></li>
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
                                <a class="nav-link" style="color:white;font-size: 0.8em;" href="#" onclick="javascript: $('#div_mod_pass').dialog('open');">Cambiar Contraseña</a>
                            </li>
                            
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle " style="color:white;font-size: 0.8em;" id="menu3" href="#" data-toggle="dropdown">
                                        Documentos adjuntos
                                    </a>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('foto');$('#div_subida_archivos_usu').dialog('open');">Fotografía Alumno</a>
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('dni');$('#div_subida_archivos_usu').dialog('open');">Documento Identificación</a>
                                    <a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('seguro');$('#div_subida_archivos_usu').dialog('open');">Resguardo Seguro Escolar</a>
                                    <!--<a class="dropdown-item " href="#" onclick="ocultaDivsSubeDocs('certificado');$('#div_subida_archivos_usu').dialog('open');">Certificado Notas</a>--> 
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
                <div class="row">
                    <div class="col" id="div_solicitudes" style="overflow-y:auto; height: 400px;">
                        <table id="solicitudes" style="margin-left:0;margin-right:0"></table>
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
                    <a id="docs_exencion_fct" class="enlaceEnabled" href="impresos/exencion_fct/exencion_fct.php" target="_self">Exención de Período de Formación en Empresas (PFE, antigua FTC)</a><br>
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

    <!--CAMBIO DE CONTRASEÑA __________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="div_mod_pass" name="nuevaPass_div" style="display: none;  padding: 5px;" class="ui-widget-header ui-corner-all alertas">
        <form id="form_cambioPass" class="needs-validation" style="margin:10px" novalidate>
            <div class="form-group">
                <label for="p1" class="etiquetas">Contraseña: </label>
                <input type="password" name="p1" id="p1" class="form-control" size="40" maxlength="40" style="color:#00F" required>
            </div>
            <div class="form-group">
                <label for="p2" class="etiquetas">Repita Contraseña: </label>
                <input type="password" name="p2" id="p2" class="form-control" size="40" maxlength="40" style="color:#00F" required>
            </div>
        </form>
        <div style="clear:both"></div>
        <p class="etiquetas" style="font-size: 0.75em !important; margin-left: 35px;">La contraseña debe:
            <ul class="etiquetas" style="font-size: 0.75em !important;">
                <li>Tener 8 caracteres de longitud como mínimo.</li>
                <li>Contener al menos un número.</li>
                <li>Contener al menos una mayúscula y una minúscula.</li>
            </ul>
        </p>
    </div>
    <!--_______________________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->

    <div id="cargando" style="z-index:9999;display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" class="alertas"></div>
    <div id="div_aviso_inicio_mat" class="alertas">
    </div>
    <!-- FORMULARIO AUXILIAR PARA VISUALIZACIÓN/DESCARGA DE DOCUMENTOS ________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="formulario_descarga_documento" style="display:none">
        <form id="descarga_doc" method="POST">
            <input type="hidden" id="descarga_referencia" name="referencia" />
            <input type="hidden" id="descarga_nombre" name="nombre" />
        </form>
    </div>
    <!--_______________________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->

    <!-- FORMULARIO AUXILIAR PARA SUBIDA DE DOCUMENTOS ________________________________________________-->
    <!--_______________________________________________________________________________________________-->
    <div id="div_subida_archivos_usu" style="display:none">
        <form id="subida_doc" method="POST">
            <div class="row" id="div_fotografia">
                <div class="col">
                    <label for="foto_alumno">Fotografía del alumno en JPEG (formato DNI) <small>(Si se hace con móvil, en posición vertical)</small>:</label>
                    <input name="foto_alumno" id="foto_alumno" type="file" class="btn btn-success form-control" accept="image/jpeg" onchange="muestraEditor_usu(this.files[0],'foto')//USUsubeFoto(this)" required/>
                </div>
                <div class="w-100" style="text-align: center;margin-top:15px">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                </div>
            </div>
            <div class="row" id="div_resguardo_seguro_escolar">
                <div class="col">
                    <label for="resguardo_seguro_escolar">Resguardo del seguro escolar en JPEG, si no lo adjuntó o lo hizo incorrectamente al hacer la matrícula <small>(Si se hace con móvil, en posición horizontal)</small>:</label>
                    <input name="resguardo_seguro_escolar" id="resguardo_seguro_escolar" type="file" class="btn btn-success form-control" accept="image/jpeg" onchange="muestraEditor_usu(this.files[0],'seguro')//USUsubeSeguro(this)" required/>
                </div>
                <div class="w-100" style="text-align: center;margin-top:15px">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                </div>
            </div>
            <div class="row" id="div_anverso_dni">
                <div class="col">
                    <label for="anverso_dni">Anverso de documento identificativo (DNI/NIE) en JPEG.<br>
                    Si sólo tiene pasaporte, fotografía en JPEG de la página que contenga la foto y nombre del alumno<br>
                    <small>(Si se toma la imagen con móvil, poner éste en posición horizontal):</small></label>
                    <input name="anverso_dni" id="anverso_dni" type="file" class="btn btn-success form-control" accept="image/jpeg" onchange="muestraEditor_usu(this.files[0],'dni_anverso')//USUsubeDNI(this,'A')" required/>
                </div>
                <div class="w-100" style="text-align: center;margin-top:15px">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                </div>
            </div>
            <div class="row" id="div_reverso_dni">
                <div class="col">
                    <label for="reverso_dni">Reverso del documento identificativo (DNI/NIE) en JPEG.<br>
                    Si sólo tiene pasaporte, fotografía en JPEG de una imagen en blanco (por ejemplo, folio).<br>
                    <small>(Si se toma la imagen con móvil, poner éste en posición horizontal):</small></label>
                    <input name="reverso_dni" id="reverso_dni" type="file" class="btn btn-success form-control" accept="image/jpeg" onchange="muestraEditor_usu(this.files[0],'dni_reverso')//USUsubeDNI(this,'R')" required/>
                </div>
                <div class="w-100" style="text-align: center;margin-top:15px">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                </div>
            </div>
            <div class="row" id="div_certificado">
                <div class="col">
                    <label for="certificado">Certificado notas en PDF <small>(si es alumno nuevo y viene de otra comunidad autónoma)</small>:</label>
                    <input name="certificado" id="certificado" type="file" class="btn btn-success form-control" accept="application/pdf" onchange="USUsubeCertificado(this)" required/>
                </div>
                <div class="w-100" style="text-align: center;margin-top:15px">
                    <span class="errorTxt" style="font-size: 1em;"></span>
                </div>
            </div>
        </form>
    </div>
    <!--_______________________________________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->

    <div id="div_edita_imagen_usu" style="display:none; text-align:center">
        <label><small id="texto_editor_imagen">Rota, haz zoom (con la rueda del ratón) y mueve la imagen para ajustarla al recuadro</small></label>
        <div class="container">
            <div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col-12 justify-content-center align-items-center" id="div_imagen">
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/usuario.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>

    <script type="text/javascript">
        $("#apartados").tabs();
    </script>
</body>

</html>