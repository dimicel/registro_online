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
    <link rel="stylesheet" href=<?php echo "../../js/croppie/croppie.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "../../jqueryui/jquery-ui.min.css?q=".time();?>>
    <link rel="stylesheet" href=<?php echo "css/convalidaciones.css?q=".time();?>>
    <title>CONVALIDACIONES</title>
</head>

<body>
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
            <div class="flex-row mt-1">
                <div class="col">
                    <input type="button" value="&#x21c7;" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="right" title="Regresar a página principal" onclick="javascript:window.history.back();" />
                </div>
            </div>
            <div class="flex-row">
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <center>
                            <input type="button" id="instrucciones" class="btn btn-success textoboton" value="Instrucciones y Anexo I (sede electrónica ministerio)" onclick="seleccion(this)">
                            <input type="button" id="formulario" class="btn btn-success textoboton" value="Formulario" onclick="seleccion(this)" />
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

    <section id="seccion-formulario" style="display:none">
        <div class="container w-100">
            <form id="form_convalidaciones">
                <div class="d-flex flex-row">
                    <div class="col">
                        <!--<div class="row d-flex w-100 justify-content-center">
                            <div class="col" style="text-align: center;">
                                <h5 id="rotulo">SOLICITUD CONVALIDACIONES PARA EL CENTRO EDUCATIVO O EL MINISTERIO</h5>
                            </div>
                        </div>-->
                        <div class="row ">
                            <div class="col">
                                <input type="button" id="volver" class="btn btn-success textoboton" value="<<< Volver" onclick="vuelve()"/>
                            </div>
                        </div>
                        <div class="row mt-1 ui-widget-header ui-corner-all" style="padding:10px">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <h6 style="color:brown; font-weight:bold">DATOS PERSONALES</h6>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-1">
                                        <label for="nombre">Nombre</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-1 offset-3">
                                        <label for="apellidos">Apellidos</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-2 offset-3">
                                        <label for="nif_nie">NIF/NIE/Pasaporte</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-3">
                                        <input type="text" class="form-control" name="nombre" id="nombre" size="40" maxlength="40"/>
                                    </div>
                                    <div class="col-4 offset-1">
                                        <input type="text" class="form-control" name="apellidos" id="apellidos" size="60" maxlength="60"/>
                                    </div>
                                    <div class="col-2 ">
                                        <input  type="text" class="form-control" name="nif_nie" id="nif_nie"  maxlength="12"/>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-1">
                                        <label for="direccion">Domicilio</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-1 offset-4">
                                        <label for="cp">CP</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-1">
                                        <label for="localidad">Localidad</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-1 offset-2">
                                        <label for="provincia">Provincia</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-5">
                                        <input type="text" class="form-control" name="direccion" id="direccion"  maxlength="50" />
                                    </div>
                                    <div class="col-1">
                                        <input type="text" class="form-control" name="cp" id="cp"  maxlength="5" />
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" name="localidad" id="localidad"  maxlength="35" />
                                    </div>
                                    <div class="col-3">
                                        <input type="text" class="form-control" name="provincia" id="provincia"  maxlength="35" />
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-3" >
                                        <label for="tlf_fijo">Tlf. Fijo</label>
                                    </div>
                                    <div class="col-3" >
                                        <label for="tlf_movil">Tlf. Móvil</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                    <div class="col-3" >
                                        <label for="email">Email</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-3" >
                                        <input type="text" class="form-control" name="tlf_fijo" id="tlf_fijo"  maxlength="12" />
                                    </div>
                                    <div class="col-3" >
                                        <input type="text" class="form-control" name="tlf_movil" id="tlf_movil"  maxlength="12" />
                                    </div>
                                    <div class="col-3" >
                                        <input type="text" class="form-control" name="email" id="email"  maxlength="40" />
                                    </div>
                                    <!--<div class="col-3">
                                        <input type="text" class="form-control" name="t_firm" id="t_firm" placeholder="Clic aquí para firmar" readonly onclick="canvasFirma();" />
                                    </div>-->
                                </div>
                            </div>
                        </div>
                        <div id="expone" class="row ui-widget-header ui-corner-all mt-2"   style="padding:10px">
                            <div class="col">
                                <div class="row">
                                    <div class="col">
                                        <h6 style="color:brown; font-weight:bold">DATOS ACADÉMICOS Y MÓDULOS QUE SOLICITA CONVALIDAR</h6>
                                    </div>
                                </div>
                                <div class="row" >
                                    <div class="col">
                                        <label for="estudios_superados">Estudios cursados en base a los que solicita la convalidación:</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                </div>
                                <div class="row" data-formulario="consejería">
                                    <div class="col">
                                        <textarea class="form-control"  name="estudios_superados" rows="2" id="estudios_superados" maxlength="300"></textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                    <label>Está matriculado en Ciclo Formativo de</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-2">
                                        <label for="grado">Grado</label>
                                        <select class="form-control" name="grado" id="grado" size="1" onchange="selGrado(this)">
                                            <option value="">Seleccione uno...</option>
                                            <option value="Básico">Básico</option>
                                            <option value="Medio">Medio</option>
                                            <option value="Superior">Superior</option>
                                            <option value="Curso de Especialización">Curso Especializ.</option>
                                        </select> 
                                    </div>
                                    <div class="col">
                                        <label for="ciclos">Denominado</label>
                                        <select class="form-control" name="ciclos" id="ciclos" size="1" onchange="selCiclo(this)">
                                            <option value="">Seleccione grado...</option>
                                        </select>
                                    </div>
                                    <div class="col-2">
                                        <label for="grado">Curso</label>
                                        <select class="form-control" name="curso" id="curso" size="1" onchange="selCurso(this)">
                                            <option value="">Seleccione uno...</option>
                                            <option value="1º">1º</option>
                                            <option value="2º">2º</option>
                                            <option value="3º">3º</option>
                                            <option value="Virtual_Modular">Virtual (E-Learning)/Modular</option>
                                        </select> 
                                    </div>
                                    <div class="col-2">
                                        <label for="grado">Turno</label>
                                        <select class="form-control" name="turno" id="turno" size="1" >
                                            <option value="">Seleccione uno...</option>
                                            <option value="Diurno">Diurno</option>
                                            <option value="Vespertino">Vespertino</option>
                                            <option value="Nocturno">Nocturno</option>
                                            <option value="Virtual">Virtual (E-learning)</option>
                                        </select> 
                                    </div>
                                    <div class="col-2">
                                        <label for="grado">Modalidad</label>
                                        <select class="form-control" name="modalidad" id="modalidad" size="1" >
                                            <option value="">Seleccione uno...</option>
                                            <option value="Presencial">Presencial</option>
                                            <option value="Semipresencial">Semipresencial</option>
                                            <option value="Modular">Modular</option>
                                            <option value="Virtual">Virtual (E-learning)</option>
                                        </select> 
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label for="modulos">Módulos que solicita convalidar (<a style="color:#00C" href="#" onclick="selModulos(event)">Clic AQUÍ para añadir o quitar módulos</a>)</label>
                                        <span class="errorTxt" style="font-size: 1em;"></span>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <textarea class="form-control"  name="modulos" rows="3" id="modulos" maxlength="1000" readonly></textarea>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col">
                                        <label id="label_estudios_aportados" for="estudios">Documentación que aporta (<a style="color:#00C" href="#" onclick="anadeDoc(event)">Clic AQUÍ para añadir documentos</a>)</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col" >
                                        <table  style="width: 70%; margin: 0 auto;background-color:lightslategrey"><tr><td style="width:50%"><b>Descripción</b></td><td  style="width:50%"><b>Documento</b></td></tr></table>
                                        <table id="tab_lista_docs"  style="width: 70%; margin: 0 auto;"><tr><td style="text-align:center">LISTA DE DOCUMENTOS VACÍA</td></tr></table>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="flex-row mt-1 w-100 justify-content-center">
                            <div class="col" style="text-align: center;">
                                <input type="button" class="btn btn-success textoboton" id="generar" value="REGISTRAR SOLICITUD" onclick="registraForm()"/>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div> 
    </section>       
    <div id="anade_documento" style="display:none">
        <form id="form_anade_documento">
            <div class="container">
                <div class="row">
                    <div class="col">
                        <label style="font-weight:bolder">TIPO DOCUMENTO:</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="conv_estud_solicita_con" name="tipo_con" class="custom-control-input" value="Certificación de estar matriculado en los estudios de Formación Profesional cuya convalidación solicita" onchange="$('#div_den_otro_con').hide(); selTipoDoc(this.value)"/>
                        <label for="conv_estud_solicita_con" class="custom-control-label">Certificación de estar matriculado en los estudios de Formación Profesional cuya convalidación solicita</label>
                    </div>
                </div>
                <div class="row mt-2" id="div_doc_identificacion">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <div class="row" >
                            <div class="col-5">
                                <label>Documento de identificación</label>
                            </div>
                            <div class="col">
                                <input type="radio" id="dni_nie_con" name="tipo_con" class="custom-control-input" value="Documento de identificación (DNI/NIE)" onchange="$('#div_den_otro_con').hide(); selTipoDoc(this.value)"/>
                                <label for="dni_nie_con" class="custom-control-label">DNI/NIE</label>
                            </div>
                            <div class="col">
                                <input type="radio" id="pasaporte_con" name="tipo_con" class="custom-control-input" value="Documento de identificación (Pasaporte)" onchange="$('#div_den_otro_con').hide(); selTipoDoc(this.value)"/>
                                <label for="pasaporte_con" class="custom-control-label">Pasaporte</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="cert_acad_con" name="tipo_con" class="custom-control-input" value="Fotocopia compulsada de la certificación académica de los estudios realizados" onchange="$('#div_den_otro_con').hide(); selTipoDoc(this.value)"/>
                        <label for="cert_acad_con" class="custom-control-label">Fotocopia compulsada de la certificación académica de los estudios realizados (en PDF)</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col custom-control custom-switch mi-checkbox">
                        <input type="radio" id="fotoc_titulo_con" name="tipo_con" class="custom-control-input" value="Fotocopia compulsada del título" onchange="$('#div_den_otro_con').hide(); selTipoDoc(this.value)"/>
                        <label for="fotoc_titulo_con" class="custom-control-label">Fotocopia compulsada del título (en PDF)</label>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-2 custom-control custom-switch mi-checkbox">
                        <input type="radio" id="otro_con" name="tipo_con" class="custom-control-input" value="Otro" onchange="$('#div_den_otro_con').show(); selTipoDoc(this.value)"/>
                        <label for="otro_con" class="custom-control-label">Otro</label>
                    </div>
                    <div class="col form-inline" style="display:none" id="div_den_otro_con">
                        <label for="den_otro_con">Especificar:</label>
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


    <!--<div id="div_canvas_firma" style="display:none; text-align:center;">
        <label><small>Puede firmar manteniendo pulsado el botón del ratón, con una tableta digitalizadora o usando el dedo si está con una tablet o un móvil.</small></label><br>
        <div id="div_lienzo" >
            <canvas id="firmaCanvas" width="400" height="200" style="background-color:white; border: 1px solid black;"></canvas>
        </div>
    </div>-->
    <div id="div_edita_imagen" style="display:none; text-align:center">
        <label><small id="texto_editor_imagen">Rota, haz zoom (con la rueda del ratón) y mueve las imágenes para ajustar el documento al recuadro</small></label>
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <div class="row">
                        <div class="col" id="div_imagen_anverso">
                            <!--<img id="imagen_anverso" style="display:none">-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                            <input type="button" class="btn btn-success textoboton" value="-90º" onclick="_crop1.rotate(90);"/>
                        </div>
                        <div class="col-3">
                            <input type="button" class="btn btn-success textoboton" value="+90º" onclick="_crop1.rotate(-90);"/>
                        </div>
                    </div>
                </div>
                <div class="col-6" id="doc_ident_reverso">
                    <div class="row">
                        <div class="col"  id="div_imagen_reverso">
                            <!--<img id="imagen_reverso" style="display:none">-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-3">
                                <input type="button" class="btn btn-success textoboton" value="-90º" onclick="_crop2.rotate(90);"/>
                            </div>
                            <div class="col-3">
                                <input type="button" class="btn btn-success textoboton" value="+90º" onclick="_crop2.rotate(-90);"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </div>

    <div id="array_input_type_file" style="display:none"></div>
    <div id="cargando" style="z-index:9999; display:none; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('../../recursos/espera.gif') no-repeat center center; opacity: .7;"></div>
    <div id="mensaje_div" style="display:none"></div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "../../jqueryui/jquery-ui.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/jquery.validate.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/jquery_validate/additional-methods.min.js?q=".time();?>></script>
    <script src=<?php echo "../../js/comun.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "../../js/croppie/croppie.min.js?q=".time();?> type="text/javascript"></script>
    <script src=<?php echo "js/convalidaciones.js?q=".time();?> type="text/javascript"></script>

</body>

</html>