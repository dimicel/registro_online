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
    <link rel="stylesheet" href=<?php echo "css/secretaria.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "js/croppie/croppie.css?q=".time();?> type="text/css">
    <link rel="stylesheet" href=<?php echo "jqueryui/jquery-ui.min.css?q=".time();?> >
    <link rel="stylesheet" href=<?php echo "js/context_menu/jquery.contextMenu.min.css?q=".time();?>>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <title>GESTIÓN DEL REGISTRO ONLINE - SECRETARIA DEL IES UNIVERSIDAD LABORAL DE TOLEDO</title>
</head>

<body>
    <div id="panel" style="display: block;  padding: 5px; width:990px" class="ui-widget-header ui-corner-all centrado">
        <div style="display:table-cell; padding-left:10px; padding-top: 10px">
            <img src="recursos/escudo.jpg" width="115" height="105" alt="Escudo_Uni">
        </div>
        <div style="display:table-cell; vertical-align: middle; padding-left: 20px">
            <h1 style="color:red;display:none" id="servidor_pruebas" ><strong>¡¡¡SERVIDOR DE PRUEBAS!!!</strong></h1>
            <h2>IES UNIVERSIDAD LABORAL</h2>
            <h3 id="rotulo_tipo_usu">SECRETARÍA - GESTIÓN DEL REGISTRO ONLINE</h3>
        </div>
        <!--
        <div style="display:table-cell; height: 105px; vertical-align: middle; padding-left: 200px">
            <input type="button" id="colocar_ficheros" value="Colocar Ficheros" onclick="colocarFicheros()">
        </div>
        -->
        <!-- GESTIÓN SECRETARÍA ___________________________________________________________________-->
        <!--_______________________________________________________________________________________-->
        <div id="doc_reg_tab" class="ui-widget-header ui-corner-all">
            <div style="margin-top:10px; margin-left:10px;" id="menu_div">
                <ul class="nav bg-white" style="font-size:0.7em !important">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="menu1" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                            Gestión Usuarios
                        </a>
                        <div class="dropdown-menu" aria-labelledby="menu1">
                            <a class="nav-link" href="#" onclick="javascript: panelNuevoUsuario();">Nuevos Usuarios</a>
                            <a class="nav-link" href="#" onclick="javascript: verListaUsuarios();">Usuarios</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" id="menu2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Seleccionar
                        </a>
                        <div class="dropdown-menu" aria-labelledby="menu2" >
                            <a class="dropdown-item " href="#" onclick="seleccionaRegistros('todo')">Todas</a>
                            <a class="dropdown-item " href="#" onclick="seleccionaRegistros('ninguno')">Ninguna</a>
                            <a class="dropdown-item " href="#" onclick="seleccionaRegistros('invertir')">Invertir Selección</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle " id="menu3" href="#" data-toggle="dropdown">
                            Listar Solicitudes
                        </a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item " href="#" onclick="registrosAPdf('seleccionadas')">Seleccionadas</a>
                            <a class="dropdown-item " href="#" onclick="registrosAPdf('no listadas')">No listadas</a>
                            <a class="dropdown-item " href="#" onclick="registrosAPdf('listadas')">Listadas</a>
                            <a class="dropdown-item " href="#" onclick="registrosAPdf('todas')">Todas</a>
                        </div>
                    </li>
                    <li class="nav-item" id="menu4">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">Prematrícula</a>
                        <div class="dropdown-menu" >
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="premat_eso" onchange="cambiaEstadoPrematricula(this,'eso')">
                                <label for="premat_eso" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">ESO</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="premat_bach" onchange="cambiaEstadoPrematricula(this,'bach')">
                                <label for="premat_bach" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">Bachillerato</label>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a id="CSV_premat" class="dropdown-item disabled" href="#" onclick="descargaCSVpremat()">Descarga CSV</a>
                        </div>
                    </li>
                    <li class="nav-item" id="menu5">
                        <a class="nav-link dropdown-toggle" id="menu_matricula" href="#" data-toggle="dropdown">Matrícula</a>
                        <div class="dropdown-menu" >
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_mat_eso" onchange="cambiaEstadoMatricula(this,'eso')">
                                <label for="check_mat_eso" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">ESO</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_mat_bach" onchange="cambiaEstadoMatricula(this,'bach')">
                                <label for="check_mat_bach" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">Bachillerato</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_mat_ciclos" onchange="cambiaEstadoMatricula(this,'ciclos')">
                                <label for="check_mat_ciclos" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">Ciclos</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_mat_ciclos-e" onchange="cambiaEstadoMatricula(this,'ciclo_e')">
                                <label for="check_mat_ciclos-e" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">Ciclos Elearning (FCT/Proyecto)</label>
                            </div>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_mat_fpb" onchange="cambiaEstadoMatricula(this,'fpb')">
                                <label for="check_mat_fpb" class="custom-control-label" style="margin-top:10px;margin-left:10px; color:#493f26 !important">FPB</label>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a id="menu_listado_mat" class="dropdown-item" href="#" onclick="javascript:subirMatDelphos();">Subir CSV Delphos para Matrícula</a>
                        </div>
                    </li>
                    <li class="nav-item" id="menu6">
                        <a class="nav-link" href="#" onclick="document.location='residencia.php?q='+Date.now()">Residencia</a>
                    </li>
                    <li class="nav-item" id="menu7">
                        <a class="nav-link dropdown-toggle" id="menu_matricula" href="#" data-toggle="dropdown">Configuración</a>
                        <div class="dropdown-menu" >
                            <a id="param_centro" class="dropdown-item" href="#" onclick="parametrosCentro()">Parámetros asociados al centro</a>
                            <a id="logos_firma_sello" class="dropdown-item" href="#" onclick="logosFirmaSello()">Logos - Firma y sello</a>
                            <a id="config_ciclos" class="dropdown-item" href="#" onclick="alerta('En desarrollo','')">Ciclos Formativos</a>
                            <a id="config_modulos" class="dropdown-item" href="#" onclick="alerta('En desarrollo','')">Módulos de Ciclos Formativos</a>
                            <a id="config_jef_dpto" class="dropdown-item" href="#" onclick="datosDepartamentos()">Departamentos</a>
                        </div>    
                    </li>
                    <li class="nav-item" id="menu8">
                        <a class="nav-link dropdown-toggle" id="menu_matricula" href="#" data-toggle="dropdown">Descargas</a>
                        <div class="dropdown-menu" >
                            <!--<a id="menu_listado_mat_pdf" class="dropdown-item disabled" href="#" onclick="listaMatriculas();">Listado de Matrículas</a>-->
                            <!--<a id="menu_csv_mat" class="dropdown-item disabled" href="#" onclick="descargaCSVmatriculas();">CSV Matrículas</a>-->
                            <a id="CSV_consol_premat" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVelearningFctProy()">CSV Matrícula E-Learning (FCT y Proyecto)</a>  
                            <a id="CSV_consol_premat" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVconsolPremat()">CSV Consolidan Prematrícula</a>  
                            <a id="CSV_nuevos_otra_com" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVProgLing()">CSV Programa Lingüístico (Sólo ESO)</a>
                            <a id="CSV_nuevos_otra_com" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVAlNuevos()">CSV Alumnos Nuevos (Sólo ESO y BACH)</a>                         
                            <a id="CSV_nuevos_otra_com" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVnuevosOtraCom()">CSV Nuevos de otra comunidad (TODOS)</a>
                            <a id="CSV_transporte" data="csv_tra_seg" class="dropdown-item" href="#" onclick="descargaCSVtransporte()">CSV Transporte Escolar</a>
                            <a id="CSV_seguro" data="csv_tra_seg" class="dropdown-item" href="#" onclick="listadoSeguroEscolarCiclos()">CSV Seguro Escolar Ciclos</a>
                            <a id="CSV_num_ss" data="csv_tra_seg" class="dropdown-item" href="#" onclick="listadoNumSS()">CSV Número de la Seguridad Social</a>
                            <a id="CSV_autor_fotos" data="csv_tra_seg" class="dropdown-item" href="#" onclick="listadoAutorUsoImag()">CSV Autorización Uso Imágenes</a>
                            <a id="fotos_alumnos" class="dropdown-item" href="#" onclick="descargaFotos()">Descarga Fotos</a>
                        </div>
                    </li>
                    <li class="nav-item" id="menu9">
                        <a class="nav-link" href="#" onclick="javascript: cierrasesion();">Salir</a>
                    </li>
                </ul>
            </div>
            <div style="margin-top:20px">
                <form>
                    <div class="form-group row">
                        <label class="col-form-label col-lg-2" style="margin-left: 20px;">Año Acad.: </label>
                        <div class="col-lg-2" style="margin-left:-85px">
                            <select id="curso" size="1" onchange="obtenRegSinProcesar();listaRegistros();" class="form-control"></select>
                        </div>
                        <label class="col-form-label col-lg-2">Tipo de formulario: </label>
                        <div class="col-lg-3" style="margin-left:-40px">
                            <select id="tipo_form" size="1" onchange="listaRegistros();" class="form-control"></select>
                        </div>
                        <div id="div_curso_premat" style="display:none">
                            <label class="col-form-label col-lg-2">Curso: </label>
                            <div class="col-lg-8">
                                <select id="curso_pre_mat" size="1" onchange="listaRegistros();" class="form-control"></select>
                            </div>
                        </div>
                        <div id="div_curso_mat" style="display:none">
                            <label class="col-form-label col-lg-2">Curso: </label>
                            <div class="col-lg-8">
                                <select id="curso_mat" size="1" onchange="listaRegistros();" class="form-control"></select>
                            </div>
                        </div>
                    </div>
                </form>
                <form class="form-inline">
                    <div class="form-group row" id="div_curso_mat_ciclos" style="display:none">
                        <label class="col-form-label col-lg-2" style="margin-left: -15px;">Ciclo: </label>
                        <div class="col-lg-5">
                            <select id="mat_ciclos" size="1" style="margin-left: -60px;" onchange="listaRegistros();" class="form-control"></select>
                        </div>
                        <label class="col-form-label col-lg-1" style="margin-left: 20px;">Curso: </label>
                        <div class="col-lg-1">
                            <select id="mat_ciclos_curso" style="margin-left: -10px;" size="1" onchange="listaRegistros();" class="form-control">
                                <option value="" selected>...</option>
                                <option value='1º'>1º</option>
                                <option value='2º'>2º</option>
                                <option value='3º'>3º</option>
                                <option value='Modular'>Modular</option>
                                <option value='learning'>E-learning</option>
                            </select>
                        </div>
                        <label class="col-form-label col-lg-1" style="margin-left: 50px;">Turno: </label>
                        <div class="col-lg-1">
                            <select id="mat_ciclos_turno" style="margin-left: -15px;" size="1" onchange="listaRegistros();" class="form-control">
                                <option value="" selected>...</option>
                                <option value='Diurno'>Diurno</option>
                                <option value='Vespertino'>Vespertino</option>
                                <option value='Nocturno'>Nocturno</option>
                            </select>
                        </div>
                    </div>
                </form>
                <form class="form-inline">
                    <div class="form-group row" id="div_curso_mat_fpb" style="display:none">
                        <label class="col-form-label col-lg-2" style="margin-left: 15px;">Ciclo: </label>
                        <div class="col-lg-5">
                            <select id="mat_fpb" size="1" style="margin-left: -30px;" onchange="listaRegistros();" class="form-control"></select>
                        </div>
                        <label class="col-form-label col-lg-1" style="margin-left: 20px;">Curso: </label>
                        <div class="col-lg-1">
                            <select id="mat_fpb_curso" style="margin-left: -10px;" size="1" onchange="listaRegistros();" class="form-control">
                                <option value="" selected>...</option>
                                <option value='1º'>1º</option>
                                <option value='2º'>2º</option>
                            </select>
                        </div>
                    </div>
                </form>
                <form>
                    <div id="div_nuevos_otra_comunidad" class="form-group row" style="margin-top: 10px;display:none">
                        <div class="custom-control custom-switch col" style="margin-left:20px">
                            <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_nuevo_otra_com" onchange="listaRegistros();">
                            <label for="check_nuevo_otra_com" class="custom-control-label" style="margin-top:5px;margin-left:10px;">Nuevo que inicia estudios en otra comunidad</label>
                        </div>
                    </div>
                    <div class="form-group row" style="margin-top: 10px;">
                        <label class="col-form-label col-lg-1" style="margin-left:20px">Buscar: </label>
                        <div class="col-lg-8" style="margin-left:-35px">
                            <input type="text" id="busqueda" maxlength="255" class="form-control" onkeyup="listaRegistros()">
                        </div>
                        <div class="custom-control custom-switch col-lg-2" id="div_incidencias">
                            <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_incidencias" onchange="listaRegistros();">
                            <label for="check_incidencias" class="custom-control-label" style="margin-top:5px;margin-left:10px;">Sólo Incidencias</label>
                        </div>
                        <div class="custom-control custom-switch col-lg-2" id="div_convalidaciones" style="display:none">
                            <input type="checkbox" class="custom-control-input checkbox_prematricula" id="check_vistas" onchange="listaRegistros();">
                            <label for="check_vistas" class="custom-control-label" style="margin-top:5px;margin-left:10px;">SÓLO No Vistas</label>
                        </div>
                    </div>
                    <div class="row" style="display:none" id="div_exencion_fct">
                        <label class="col-form-label col-1" style="margin-left:20px">Departamento: </label>
                        <div class="col-5" style="margin-left:20px">
                            <select id="departamento" class="form-control" onchange="listaRegistros();"></select>
                        </div>
                        <div class="col">
                            <input type="button" class="btn btn-success" value="Aviso a Jefe del Dpto seleccionado" onclick="avisarJefesDpto()">
                        </div>
                    </div>
                </form>
            </div>
            <div style="clear: both"></div>
            <div style="margin-left:20px">
                <table id="encabezado_docs" class="encab_tablas noseleccionable" cellpadding="0" cellspacing="0" style="margin-top:1em;">
                    <tr>
                        <td style="text-align: center; width: 900px;">Selecciona tipo de formulario en el desplegable de arriba.</td>
                    </tr>
                </table>
                <div id="div_tabla" style="overflow: auto; height: 400px" class="table-hover">
                    <table id="registros_docs" cellpadding="0" cellspacing="0" class="noseleccionable">
                    </table>
                </div>
                <div id="div_notabla" style="display:none; height: 400px; text-align: center; margin-left: 10px">
                    No se ha seleccionado tipo de formulario o no hay formularios de este tipo registrados.
                </div>
            </div>
        </div>
        <!-- FIN GESTIÓN DE SECRETARÍA ____________________________________________________________-->
        <!--_______________________________________________________________________________________-->


        <!-- LISTADO USUARIOS _____________________________________________________________________-->
        <!--_______________________________________________________________________________________-->
        <div id="usu_reg_tab" class="ui-widget-header ui-corner-all d-none" >
            <div class="row" style="margin-top:15px">
                <div class="col-1">
                    <label class="col-form-label" style="margin-left:20px; ">Mostrar: </label>
                </div>
                <div class="col-4" style="margin-left:-10px">
                    <select id="sel_solo_entrado" size="1" onchange="listaUsus()" class="form-control">
                        <option value='Si'>Los que han entrado alguna vez</option>
                        <option value='No'>Los que NO han entrado nunca</option>
                        <option value='Todos' selected>Todos</option>
                    </select>
                </div>
                <div class="col-4 offset-3">
                    <input type="button" value="Subida Masiva Docs" class="textoboton btn btn-success" onclick="subeDocExpediente('varios','');" />
                    <input type="button" value="Volver" class="textoboton btn btn-success" onclick="cierraListaUsuarios()" />
                </div>
            </div>
            <div class="row" style="margin-top:15px">
                <label class="col-form-label col-lg-1" style="margin-left:20px">Buscar: </label>
                <div class="col-lg-8" style="margin-left:-35px">
                    <input type="text" id="busqueda_usus" maxlength="255" class="form-control" onkeyup="listaUsus()">
                </div>
            </div>
            <div class="row justify-content-center" style="margin-top:10px">
                <ul class="pagination pagination-sm" id="navegacion_usus_top"></ul>
            </div>
            <div class="row justify-content-center" >
                <div class="col" style="text-align: center;">
                    <span style="display: block; margin: 0 auto;">Usuario con fondo rojo = Usuario INHABILITADO. Clic en botón derecho para habilitar/inhabilitar en el menú</span>
                </div>
            </div>

            <div class="row justify-content-center">
                <table id="encabezado_usus" class="encab_tablas noseleccionable" cellpadding="0" cellspacing="0" style="margin-left:20px">
                </table><br>
                <div id="div_tabla_usus" style="overflow: auto; height: 480px;" class="table-hover">
                    <table id="registros_usus" cellpadding="0" cellspacing="0" class="noseleccionable" style="margin-left:20px">
                    </table>
                </div>
                <div id="div_notabla_usus" style="display:none; height: 400px; text-align: center; margin-left: 20px">
                    No hay usuarios que listar.
                </div>
            </div>
            <div class="row justify-content-center">
                <ul class="pagination pagination-sm" id="navegacion_usus_bottom"></ul>
            </div>
        </div>
        <!-- FIN LISTADO DE USUARIOS__________________________________________________________________-->
        <!--__________________________________________________________________________________________-->
    </div>
    <!--______________________________________________________________________________________________-->


    <!-- REASIGNACIÓN DE PASSWORD _____________________________________________________________________-->
    <!--__No se carga desde fichero externo por la complejidad de implementarlo en javascript. Aparece en demasidos sitios con demasiados condicionantes__-->
    <div id="div_nie_registrado" style="display: none;  padding: 5px;" class="ui-widget-header ui-corner-all alertas">
        <div id="div_usuario" data-alta="usuario">
            <span>El NIE está dupliado, pero el usuario todavía no se ha habilitado como usuario del sistema.</span><br>
            <span>Si el motivo del alta ha sido olvido de contraseña, o simplemente quiere reasignar la contraseña generada, pulse 'Continuar'</span>
        </div>
        <div id="div_registrado" data-alta="registrado">
            <span>El usuario ya se ha habilitado como usuario del sistema.</span>
            <span>Si pulsa 'Continuar', al usuario se le asignará otra contraseña.</span><br>
            <span>IMPORTANTE: Es conveniente verificar que la identidad de quien solicita la renovación de contraseña coincide con el titular del Nº de Identificación Escolar.</span><br>
        </div>
        <div style="text-align: center;">
            <br>
            <input type="button" value="Continuar" class="btn btn-success textoboton" onclick="reasignarPassword();$('#div_nie_registrado').dialog('close');" />
            <input type="button" value="Cancelar" class="btn btn-success textoboton" onclick="$('#div_nie_registrado').dialog('close');" />
        </div>
    </div>
    <!-- FIN REASIGNACIÓN DE PASSWORD _________________________________________________________________-->
    <!--_______________________________________________________________________________________________-->



    <div id="verRegistro_div" style="display:none; font-size:0.85em !important;" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="verModulosConvalidaciones_div" style="display:none; font-size:0.85em !important;" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="verInfoUsu_div" style="display:none; font-size:0.85em !important;" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="cargando" style="display:none; font-size:4em; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white url('recursos/espera.gif') no-repeat center center; opacity: .7;z-index:9999;text-align:center;">
        <!--<label id="progreso_php" style="color:brown;vertical-align:middle;"></label>-->
    </div>
    <div id="progreso" style="display:none; justify-content:center; align-items:center; font-size:4em; position: absolute; left: 0; top: 0; width: 100%; height: 100%; background: white; opacity: .7;z-index:9999;text-align:center;">
        <div class="progress" style="width:50%; height:40px;margin-top:25%;margin-left:25%">
            <div id="bar_prog" class="progress-bar progress-bar-striped progress-bar-animated bg-success"  style="width: 0%;"></div>
        </div>
        <br>
        <div>
            <span id="num_procesados" style="font-size:0.5em !important"></span>
        </div>
    </div>
    <div id="mensaje_div" class="alertas"></div>
    <div id="div_dialogs" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="div_dialogs2" class="ui-widget-header ui-corner-all alertas"></div>
    <div id="div_dialogs_adjuntosconvalid" class="ui-widget-header ui-corner-all alertas"></div>
    
    <div id="formulario_descargar_csv" style="display:none">
        <form id="descarga_csv_premat" action="php/secret_csv_prematricula.php" method="POST" target="_self">
            <input type="hidden" id="premat_csv" name="premat_csv" />
            <input type="hidden" id="curso_csv" name="curso_csv" />
        </form>
    </div>
    <!--
    <div id="formulario_descargar_csv_matricula" style="display:none">
        <form id="descarga_csv_matricula" action="php/secret_csv_matricula.php" method="POST" target="_self">
            <input type="hidden" id="mat_csv" name="mat_csv" />
            <input type="hidden" id="curso_csv_mat" name="curso_csv_mat" />
        </form>
    </div>
    -->
    <div id="formulario_descargar_csv_nuevos_eso_bach" style="display:none">
        <form id="descarga_csv_nuevos_eso_bach" action="php/secret_csv_nuevos_eso_bach.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_nuevos_eso_bach" name="curso_csv_nuevos_eso_bach" />
        </form>
    </div>
    <div id="formulario_descargar_csv_prog_ling" style="display:none">
        <form id="descarga_csv_prog_ling" action="php/secret_csv_programaling.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_prog_ling" name="curso_csv_prog_ling" />
        </form>
    </div>
    <div id="formulario_descargar_csv_nuevos_alumnos_otra_comunidad" style="display:none">
        <form id="descarga_csv_nuevosotracomunidad" action="php/secret_csv_nuevosotracomunidad.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_nuevosotracomunidad" name="curso_csv_nuevosotracomunidad" />
        </form>
    </div>
    <div id="formulario_descargar_csv_consolida_prematricula" style="display:none">
        <form id="descarga_csv_consolidaprematricula" action="php/secret_csv_consolidaprematricula.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_consolidaprematricula" name="curso_csv_consolidaprematricula" />
        </form>
    </div>
    <div id="formulario_descargar_csv_elearning_fct_proyecto" style="display:none">
        <form id="descarga_csv_elearning_fctproyecto" action="php/secret_csv_elearning_fctproyecto.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_elearning_fctproyecto" name="curso_csv_elearning_fctproyecto" />
        </form>
    </div>
    <div id="formulario_descargar_csv_transporte" style="display:none">
        <form id="descarga_csv_transporte" action="php/secret_csv_transporte.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_transporte" name="curso_csv_transporte" />
        </form>
    </div>
    <div id="formulario_descargar_csv_segurociclos" style="display:none">
        <form id="descarga_csv_segurociclos" action="php/secret_csv_segurociclos.php" method="POST" target="_self">
            <input type="hidden" id="curso_csv_seguro" name="curso_csv_seguro" />
        </form>
    </div>
    <div id="formulario_descargar_num_ss" style="display:none">
        <form id="descarga_csv_num_ss" action="php/secret_csv_fct_num_ss.php" method="POST" target="_self">
        </form>
    </div>
    <div id="formulario_descargar_autor_uso_fotos" style="display:none">
        <form id="descarga_csv_autor_uso_imagenes" action="php/secret_csv_autor_uso_imag.php" method="POST" target="_self">
        <input type="hidden" id="curso_csv_autor_uso_imagenes" name="curso_csv_autor_uso_imagenes" />
        </form>
    </div>
    <div id="formulario_descargar_fotos_alumnos" style="display:none">
        <form id="descarga_fotos_alumnos" action="php/secret_descargafotos.php" method="POST" target="_self">
            <input type="hidden" id="usuario" name="usuario" />
        </form>
    </div>

    <div id="formulario_datos_centro" style="display:none">
        <form id="datos_centro" >
        <span>Doble click en un campo para editarlo.</span>
        <div class="form-row">
            <div class="form-group col">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="director">Director:</label>
                <input type="text" name="director" id="director" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="centro">Centro:</label>
                <input type="text" name="centro" id="centro" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-2">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="cp">CP:</label>
                <input type="text" name="cp" id="cp" class="form-control" maxlength="5" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
            <div class="form-group col-5">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="localidad">Localidad:</label>
                <input type="text" name="localidad" id="localidad" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
            <div class="form-group col-5">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="provincia">Provincia:</label>
                <input type="text" name="provincia" id="provincia" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-4">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="tlf_centro">Teléfono:</label>
                <input type="text" name="tlf_centro" id="tlf_centro" class="form-control" maxlength="12" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
            <div class="form-group col-4">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="fax_centro">Fax:</label>
                <input type="text" name="fax_centro" id="fax_centro" class="form-control" maxlength="12" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
            <div class="form-group col-4">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="email_centro">Email:</label>
                <input type="text" name="email_centro" id="email_centro" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <hr>
        <div class="form-row">
            <div class="form-group col">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="email_jef_res">Email Jefe Residencia:</label>
                <input type="text" name="email_jef_res" id="email_jef_res" class="form-control" maxlength="255" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="finza_bonif">Fianza Bonificados Residencia (€):</label>
                <input type="number" name="finza_bonif" id="finza_bonif" class="form-control"  step="0.01" min="0" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
            <div class="form-group col-6">
                <span class="errorTxt" style="font-size: 1em;"></span>
                <label for="finza_nobonif">Fianza NO Bonificados Residencia (€):</label>
                <input type="number" name="finza_nobonif" id="finza_nobonif" class="form-control"  step="0.01" min="0" readonly ondblclick="this.readOnly=false" onblur="this.readOnly=true"/>
            </div>
        </div>
        </form>
    </div>

    <div class="row alertas" id="div_carga_logos_sellofirma">
        <div class="col-12">
            <div class="row">
                <div class="col-12 text-center">
                    <span>Doble clic en una imagen para sustituirla por otra.</span>
                </div>
            </div>
            <div class="row" style="height:300px;">
                <div class="col-4" style="display: flex;justify-content: center;align-items: center;height: 100%;">
                    <a href="#" ondblclick="document.getElementById('logo_centro').click()"><img id="imagen_logo_centro" src="recursos/escudo.jpg" width="220"></a>
                    <input type="file" id="logo_centro" name="logo_centro" style="position: absolute; left:-9999px" accept="image/jpeg" onchange="subeLogo(this,'logo_centro')" />
                </div>
                <div class="col-4" style="display: flex;justify-content: center;align-items: center;height: 100%;">
                    <a href="#" ondblclick="document.getElementById('logo_junta').click()"><img id="imagen_logo_junta" src="recursos/logo_ccm.jpg" width="220"></a>
                    <input type="file" id="logo_junta" name="logo_junta" style="position: absolute; left:-9999px" accept="image/jpeg" onchange="subeLogo(this,'logo_junta')" />
                </div>
                <div class="col-4" style="display: flex;justify-content: center;align-items: center;height: 100%;">
                    <a href="#" ondblclick="document.getElementById('firma_sello').click()"><img id="imagen_firma_sello" src="recursos/sello_firma.jpg" width="220"></a>
                    <input type="file" id="firma_sello" name="firma_sello" style="position: absolute; left:-9999px" accept="image/jpeg" onchange="subeLogo(this,'firma_sello')" />
                </div>
            </div>
            <div class="row">
                <div class="col-4 text-center">
                    <span>Logo del Centro</span>
                </div>
                <div class="col-4 text-center">
                    <span>Logo de la Junta</span>
                </div>
                <div class="col-4 text-center">
                    <span>Sello y firma del director</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container alertas" id="div_config_departamentos">
        <form id="config_departamentos">
            <div class="row">
                <div class="col-4">
                    <label for="config_dpto">Departamento</label>
                    <span class="errorTxt" style="font-size: 1em;"></span>
                    <select id="config_dpto" name="config_dpto" class="form-control" onchange="selDptoConfigDpto(this)"></select>
                </div>
                <div class="col">
                    <label for="config_email">Nombre y Apellidos del Jefe de Dpto.</label>
                    <span class="errorTxt" style="font-size: 1em;"></span>
                    <input type="text" id="config_nombre_jd" name="config_nombre_jd" class="form-control" maxlength="120" placeholder="Seleccione antes un departamento" readonly>
            </div>
            <div class="row mt-3">
                <div class="col-4">
                    <label for="config_email_jd">Email del Jefe de Dpto.</label>
                    <span class="errorTxt" style="font-size: 1em;"></span>
                    <input type="text" id="config_email_jd" name="config_email_jd" class="form-control" maxlength="255" placeholder="Seleccione antes un departamento" readonly>
                </div>
                <div class="col-4">
                    <label for="config_password_jd">Contraseña del Jefe de Dpto.<small>(En blanco no se cambia)</small></label>
                    <span class="errorTxt" style="font-size: 1em;"></span>
                    <input type="text" id="config_password_jd" name="config_password_jd" class="form-control" placeholder="Seleccione antes un departamento" readonly>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col">
                    <label>En caso de introducir la contraseña (si se desea cambiar), debe tener como mínimo 8 caracteres de longitud, y contener, al menos, una minúscula, una mayúscula y un número.</label>
                </div>
            </div>
        </form>
    </div>
    

    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
    <script src=<?php echo "jqueryui/jquery-ui.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/jquery.validate.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery_validate/additional-methods.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/jquery.bootpag.min.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/context_menu/jquery.contextMenu.min.js?q=".time(); ?>></script>
    <script src=<?php echo "js/context_menu/jquery.ui.position.js?q=".time(); ?>></script>
    <script src=<?php echo "js/secretaria_usu.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/secretaria.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/comun.js?q=".time(); ?> type="text/javascript"></script>
    <script src=<?php echo "js/croppie/croppie.min.js?q=".time(); ?> type="text/javascript"></script>
</body>

</html>