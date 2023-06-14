<?php
session_start();
if (!isset($_SESSION['acceso_logueado']) || $_SESSION['acceso_logueado']!=="correcto") exit("Acceso denegado");
header('Content-Type: text/html; charset=UTF-8');
include("conexion.php");
include("mail.php");

if ($mysqli->errno>0) exit("server");

if(is_uploaded_file($_FILES['csv']['tmp_name'])){
    array_map('unlink', glob("excel/*.csv"));
    $archivo=basename($_FILES['csv']['name'],".csv")."_".time().".csv";
    $ruta="excel/" . $archivo;
    if(!move_uploaded_file($_FILES['csv']['tmp_name'], $ruta)) exit("almacenar");
}
else exit("archivo");

if (file_exists($ruta)) {
    chmod($ruta, 0777);
    $csv=fopen($ruta,'r+');
}
else exit ("noexiste");


$body_html="<h3><strong>FINALIZACIÓN DEL PROCESO DE MATRÍCULA: ENTREGA DE DOCUMENTACIÓN EN EL REGISTRO ONLINE DEL IES UNIVERSIDAD LABORAL</strong></h3>";
$body_html.="<p>La documentación a aportar para completar la matrícula, se realizará a través del Registro online del centro.</p>";
$body_html.="<p>Acceso al Registro online en el siguiente enlace: <a href='https://registro.ulaboral.org/' target='_blank'>https://registro.ulaboral.org/</a></p>";
$body_html.="<p>Importante: al acceder por primera vez (alumnos nuevos) al Registro Online se completará correo electrónico para recuperar contraseña y recibir incidencias en el proceso de matrícula.</p>";
$body_html.="<p>Imagen con la pantalla de acceso al Registro Online del IES Universidad Laboral:</p>";
$body_html.="<center><p><img src='cid:pantalla_inicio' height='350' width='568'></p></center>";
$body_html.="<p>Tal y como se puede observar en la imagen anterior, para acceder se necesita:</p>";
$body_html.="<ul>";
$body_html.="<li>Usuario: el NIE (número de identificación escolar)</li>";
$body_html.="<li>Contraseña</li>";
$body_html.="</ul>";
$body_html.="<p>Todo el alumnado que ha estado matriculado en el centro durante este curso pasado ya tiene el NIE y la contraseña. No obstante, en el correo que recibirán para completar la matrícula, se les recordará el NIE. Si no recuerdan la contraseña, pueden recuperarla, a través del enlace &#8220;Recuperar contraseña&#8221; y la recibirán en el correo electrónico que facilitaron cuando accedieron por primera vez al Registro online.</p>";
$body_html.="<p>El alumnado que se matricule por primera vez en el centro, recibirá en el correo electrónico el NIE y la contraseña para poder acceder al Registro online.</p>";
$body_html.="<p>&nbsp;</p>";
$body_html.="<h3><strong>Recomendaciones a tener en cuenta para completar la matrícula:</strong></h3>";
$body_html.="<ul>";
$body_html.="<li>El alumnado que ha estado matriculado en el centro durante este curso pasado, y que ha realizado prematrícula, deberá indicar en la primera pantalla si ha elegido las mismas materias que en la prematrícula o no, seleccionando una casilla u otra. (Este apartado no afecta a los alumnos que se matriculan por primera vez en el centro).</li>";
$body_html.="<li>Documentación que necesita tener a mano para completar la matrícula en el Registro online: <strong><u>DNI, NIE o Pasaporte</u></strong>, <strong><u>fotografía</u></strong>, <strong><u>justificante de pago del seguro escolar</u></strong> (para los alumnos que estén matriculados en 3º y 4º de ESO, Bachillerato, Formación Profesional, siempre que no superen los 28 años), y para los alumnos nuevos procedentes de otra Comunidad, <strong><u>certificado académico en formato PDF</u></strong>. El certificado puede ser escaneado desde el dispositivo móvil con una aplicación (por ejemplo, la aplicación gratuita Microsoft Lens).</li>";
$body_html.="</ul>";
$body_html.="<p style='padding-left: 40px;'><strong>1- DNI o NIE/Pasaporte </strong>si no posee nacionalidad española, escaneado en formato jpeg (Aunque no es obligatorio tener este documento antes de los 14 años, es importante que lo tengan). Si ya es usuario del Registro online, y el curso pasado presentó <strong>DNI o NIE/Pasaporte</strong>, no es necesario que vuelva a subir la documentación, a no ser que desee actualizarla.</p>";
$body_html.="<p style='padding-left: 40px;'><strong>2- Fotografía</strong>. Es necesaria para la elaboración del carné escolar. Si ya es usuario del Registro online, y el curso pasado presentó fotografía, no es necesario que vuelva a subir la fotografía, a no ser que desee actualizarla.</p>";
$body_html.="<p style='padding-left: 40px;'>Características de la fotografía tamaño carnet:</p>";
$body_html.="<ul style='padding-left: 60px;'>";
$body_html.="<li>Formato jpeg.</li>";
$body_html.="<li>En color y con el fondo blanco.</li>";
$body_html.="<li>Centradas y totalmente de frente mirando a la cámara.</li>";
$body_html.="<li>Deberán ser recortadas y presentadas de acuerdo a la siguiente imagen de ejemplo:</li>";
$body_html.="</ul>";
$body_html.="<center><p><img src='cid:foto' height='222' width='173'></p></center>";
$body_html.="<p style='padding-left: 40px;'>Las fotografías que se envíen sin recortar o que no presenten el formato de la imagen de ejemplo no serán válidas.</p>";
$body_html.="<p style='padding-left: 40px;'><strong>3-</strong> <strong>Justificante de pago del seguro escolar</strong> en formato jpeg. El pago se realizará previamente (1&#8217;12 &#8364;) y preferiblemente por transferencia bancaria, al nº de cuenta: ES95 3081 0224 4237 1874 8423 de Eurocaja Rural. El concepto del ingreso será el nombre y apellidos del alumno y el curso en el que se matricula: NOMBRE Y APELLIDOS DEL ALUMNO+CURSO (Ejemplo: Antonio Pérez Gómez 4ESO).</p>";
$body_html.="<p style='padding-left: 40px;'><strong>4- Certificado académico</strong> en formato PDF (sólo para alumnos que hayan iniciado los estudios en los que se matricula, en otra comunidad autónoma). El certificado puede ser escaneado desde el dispositivo móvil con una aplicación (por ejemplo, la aplicación gratuita Microsoft Lens).</p>";


$ruta_descarga="excel/".basename($ruta,".csv")."_informe.csv";
$csv_descarga=fopen($ruta_descarga,'w+');
if($csv_descarga==false) exit ("informe");

set_time_limit(10800);

$mail->Subject = 'Registro Online';
$mail->clearAttachments();
$mail->addEmbeddedImage("../recursos/instrucciones1.jpg","pantalla_inicio");
$mail->addEmbeddedImage("../recursos/foto_carne.jpg","foto");


$delimitador=$_POST["delimitador"];
$acotacampos=$_POST["acotacampos"];
$curso_actual=$_POST["curso_actual"];
$alumnoNuevo=false;

///////Barra Progreso
$sesion_id=$_SESSION["ID"];
session_write_close();
$num_usuarios=0;
while(($datos = fgetcsv($csv, 0,$delimitador,$acotacampos)) !== FALSE){
    $num_usuarios++;
}
$num_usuarios=$num_usuarios-1;
fclose($csv);
$csv=fopen($ruta,'r+');
/*$_r=$mysqli->query("select * from progreso where id='$sesion_id'");
if($_r->num_rows>0) $mysqli->query("update progreso set procesados=0,total='$num_usuarios' where id='$sesion_id'");
else $mysqli->query("insert into progreso (id,procesados,total) values ('$sesion_id',0,'$num_usuarios')");
*/
/////////////////


if ($csv != false) {
    $fila=0;
    $columnaNIE=0;
    $columnaNombre=-1;
    $columnaApellidos=-1;
    $columnaNIF=-1;
    $columnaEmailAlumno=-1;
    $columnaEmailTutor1=-1;
    $columnaEmailTutor2=-1;
    $nuevoArray=Array();//Se utiliza para generar el csv que se descargará como informe de estado de las operaciones realizadas

    while(($datos = fgetcsv($csv, 0,$delimitador,$acotacampos)) !== FALSE){  
        $cuerpo = 'Registro online del IES Universidad Laboral<br>';
        $cuerpo.= "Su matrícula en Secretaría Virtual ha sido procesada.<br>";
        $cuerpo.="Puede continuar con el proceso de matrícula en nuestra plataforma de Registro Online (ver instrucciones que se envían a continuación).<br>";
        $err_duplicado=false;
        $emailAlumnoSend=true;
        $emailTutor1Send=true;
        $emailTutor2Send=true;
        $usuario_nuevo=false;
        $no_ha_entrado="N";
        if ($fila==0){
            $encabezamientoMat=$datos;//Se utiliza posteriormente para saber el nombre de la materia en la que está matriculada el alumno
            $col_faltan="";
            if(!in_array("NIE",$datos)) $col_faltan.="- NIE<br>";
            if(!in_array("APELLIDOS",$datos)) $col_faltan.="- APELLIDOS<br>";
            if(!in_array("NOMBRE",$datos)) $col_faltan.="- NOMBRE<br>";
            if(!in_array("DNI",$datos)) $col_faltan.="- DNI<br>";
            if(!in_array("EMAIL_ALUMNO",$datos)) $col_faltan.="- EMAIL_ALUMNO<br>";
            if(!in_array("EMAIL_TUTOR1",$datos)) $col_faltan.="- EMAIL_TUTOR1<br>";
            if(!in_array("EMAIL_TUTOR2",$datos)) $col_faltan.="- EMAIL_TUTOR2";
            if($col_faltan!="") exit($col_faltan);
            for ($c=0; $c < count($datos); $c++){
                if ($datos[$c]=="NIE")$columnaNIE=$c;
                else if($datos[$c]=="DNI")$columnaNIF=$c;
                else if($datos[$c]=="APELLIDOS")$columnaApellidos=$c;
                else if($datos[$c]=="NOMBRE")$columnaNombre=$c;
                else if($datos[$c]=="EMAIL_ALUMNO")$columnaEmailAlumno=$c;
                else if($datos[$c]=="EMAIL_TUTOR1")$columnaEmailTutor1=$c;
                else if($datos[$c]=="EMAIL_TUTOR2")$columnaEmailTutor2=$c;
            }
            $nuevoArray[$fila-1]=Array("NIE","PASSWORD","APELLIDOS","NOMBRE","NIF/NIE","EMAIL_ALUMNO","EMAIL_TUTOR1","EMAIL_TUTOR2","ERROR");
            $fila++;
            continue;
        }

        ///////Barra Progreso
        $pBar=fopen("excel/".$sesion_id.".csv",'w+');
        fwrite($pBar,"");
        fputcsv($pBar,Array($fila,$num_usuarios));
        fclose($pBar);
        //$mysqli->query("update progreso set procesados='$procesados',total='$num_usuarios' where id='$sesion_id'");
        //////

        //Asignamos datos del alumno a las variables que se utilizarán para alta/modificación datos en tablas. Se asigna los emailas a las variables para envío de correos al final
        $id_nie=trim($datos[$columnaNIE]);
        $apellidos=trim($datos[$columnaApellidos]);
        $nombre=trim($datos[$columnaNombre]);
        $NIF=trim($datos[$columnaNIF]);
        $pass=password();
        $p=password_hash($pass,PASSWORD_BCRYPT);
        $emailAlumno=trim($datos[$columnaEmailAlumno]);
        $emailTutor1=trim($datos[$columnaEmailTutor1]);
        $emailTutor2=trim($datos[$columnaEmailTutor2]);
        
        //Se da de alta al alumno si no existe, y se actualizan los datos que estén en blanco (si existen)
        $consulta=$mysqli->query("select * from usuarios where id_nie='$id_nie'");
        if ($consulta->num_rows>0){
            $usuario_nuevo=false;
            $r=$consulta->fetch_array(MYSQLI_ASSOC);
            if($r["no_ha_entrado"]==1) $no_ha_entrado="V";
            else $no_ha_entrado="F";
            if($no_ha_entrado=="F"){
                $nuevoArray[$fila-1]=Array($id_nie,"No usuario nuevo",$apellidos,$nombre,$NIF,$emailAlumno,$emailTutor1,$emailTutor2,"");
            }
            else if($no_ha_entrado=="V") {
                $nuevoArray[$fila-1]=Array($id_nie,$pass,$apellidos,$nombre,$NIF,$emailAlumno,$emailTutor1,$emailTutor2,"");
            }
            $__campos="";
            //$no_ha_entrado==$r["no_ha_entrado"];
            if(trim($r["apellidos"])=="" && $apellidos!="") $__campos="apellidos='$apellidos'";
            if(trim($r["nombre"])==""  && $nombre!=""){
                if ($__campos=="") $__campos="nombre='$nombre'";
                else $__campos.=",nombre='$nombre'";
            }
            if(trim($r["id_nif"])=="" && $NIF!=""){
                if ($__campos=="") $__campos="id_nif='$NIF'";
                else $__campos.=",id_nif='$NIF'";
            }
            if ($__campos!=""){
                $mysqli->query("update usuarios set ".$__campos." where id_nie='$id_nie'");
            } 
        }
        else{
            $usuario_nuevo=true;
            $no_ha_entrado="V";
            $nuevoArray[$fila-1]=Array($id_nie,$pass,$apellidos,$nombre,$NIF,$emailAlumno,$emailTutor1,$emailTutor2,"");
            $mysqli->query("insert into usuarios (id_nie,id_nif,nombre,apellidos,password,no_ha_entrado) values ('$id_nie','$NIF','$nombre','$apellidos','$p',1)");
            $mysqli->query("insert into usuarios_dat (id_nie) values ('$id_nie')");

            if(!is_dir("../docs/".$id_nie)) mkdir("../docs/".$id_nie,0777);
            if (!is_dir("../docs/".$id_nie."/seguro")) mkdir("../docs/".$id_nie."/seguro",0777);
            if (!is_dir("../docs/".$id_nie."/dni")) mkdir("../docs/".$id_nie."/dni",0777);
            if (!is_dir("../docs/".$id_nie."/certificado_notas")) mkdir("../docs/".$id_nie."/certificado_notas",0777);
        } 
        
        $mattricula=$mysqli->query("select * from matriculas_delphos where id_nie='$id_nie' and curso='$curso_actual'");
        if ($mattricula->num_rows>0){ //Sólo se envían emails de aviso si existen y no fueron enviados en el momento del registro de la matrícula
            $r=$mattricula->fetch_array(MYSQLI_ASSOC);
            if ($r["avisado_email"]==0 && $emailAlumno=="" && $emailTutor1=="" && $emailTutor2==""){
                $nuevoArray[$fila-1][8].=utf8_decode("Matricula registrada. No existen emails para envío de instrucciones.");
            }
            elseif($r["avisado_email"]==1){
                $nuevoArray[$fila-1][8].=utf8_decode("Matricula registrada. Ya se han enviado instrucciones por email.");
                $nuevoArray[$fila-1][1]="No generado";
            }
            else {
                if($no_ha_entrado=="F"){
                    $cuerpo.="Recuerde que su usuario (NIE) es " . $id_nie . " y su contraseña puede recuperarla, si no la acuerda, en el enlace 'Recuperar contraseña' de la página de entrada.";
                }
                else if($no_ha_entrado=="V") {
                    $cuerpo.="Recuerde que su usuario (NIE) es " . $id_nie . " y su contraseña ".$pass;
                    $mysqli->query("update usuarios set password='$p' where id_nie='$id_nie'");
                }
                $mail->Body =$cuerpo."<br><br>".$body_html;
                if($emailAlumno!=""){
                    $mail->clearAllRecipients();
                    $mail->addAddress($emailAlumno, '');
                    try{
                        $mail->send();
                    } catch (Exception  $e){
                        $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_ALUMNO.");
                        $emailAlumnoSend=false;
                    }
                }
                if ($emailTutor1!=""){
                    $mail->clearAllRecipients();
                    $mail->addAddress($emailTutor1, '');
                    try{
                        $mail->send();
                    } catch (Exception  $e){
                        $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_TUTOR1.");
                        $emailTutor1Send=false;
                    }
                }
                if ($emailTutor2!=""){
                    $mail->clearAllRecipients();
                    $mail->addAddress($emailTutor2, '');
                    try{
                        $mail->send();
                    } catch (Exception  $e){
                        $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_TUTOR2.");
                        $emailTutor2Send=false;
                    }
                }
                if ($emailAlumnoSend && $emailTutor1Send && $emailTutor2Send){
                    $mysqli->query("update matriculas_delphos set avisado_email=1 where id_nie='$id_nie' and curso='$curso_actual'");
                }
            }
        }
        else { 
            
            //Se da de alta el registro y se envían los emails de aviso.
            if($mysqli->query("insert into matriculas_delphos (id_nie,curso) values ('$id_nie','$curso_actual')")!=true){
                $nuevoArray[$fila-1][8].=utf8_decode("Fallo en alta de matrícula: Error " . $mysqli->error .".");
            }
            else{
                if ($emailAlumno!="" || $emailTutor1!="" || $emailTutor2!=""){
                    if($no_ha_entrado=="F"){
                        $cuerpo.="Recuerde que su usuario es " . $id_nie . " y su contraseña puede recuperarla, si no la acuerda, en el enlace 'Recuperar contraseña' de la página de entrada";
                    }
                    else if($no_ha_entrado=="V"){
                        $cuerpo.="Recuerde que su usuario es " . $id_nie . " y su contraseña ".$pass;
                        $mysqli->query("update usuarios set password='$p' where id_nie='$id_nie'");
                    }
                    $mail->Body =$cuerpo."<br><br>".$body_html;
                    if($emailAlumno!=""){
                        $mail->clearAllRecipients();
                        $mail->addAddress($emailAlumno, '');
                        try{
                            $mail->send();
                        } catch (Exception  $e){
                            $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_ALUMNO.");
                            $emailAlumnoSend=false;
                        }
                    }
                    if ($emailTutor1!=""){
                        $mail->clearAllRecipients();
                        $mail->addAddress($emailTutor1, '');
                        try{
                            $mail->send();
                        } catch (Exception  $e){
                            $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_TUTOR1.");
                            $emailTutor1Send=false;
                        }
                    }
                    if ($emailTutor2!=""){
                        $mail->clearAllRecipients();
                        $mail->addAddress($emailTutor2, '');
                        try{
                            $mail->send();
                        } catch (Exception  $e){
                            $nuevoArray[$fila-1][8].=utf8_decode("Fallo envío EMAIL_TUTOR2.");
                            $emailTutor2Send=false;
                        }
                    }
                    if ($emailAlumnoSend || $emailTutor1Send || $emailTutor2Send){
                        $mysqli->query("update matriculas_delphos set avisado_email=1 where id_nie='$id_nie' and curso='$curso_actual'");
                    }
                }
                else {
                    $nuevoArray[$fila-1][8].=utf8_decode("Matricula registrada. No existen emails para envío de instrucciones.");
                }
            }
        } 
               
        $fila++;
    }
    fclose($csv);
    foreach ($nuevoArray as $fields) { 
        fputcsv($csv_descarga, $fields); 
    } 
    fclose($csv_descarga);
    exit($ruta_descarga);
}
else exit("abrir");


function password(){
    $mayus="ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $minus="abcdefghijklmnopqrstuvwxyz";
    $nums="0123456789";
    $array=array("","","","","","","","");
    $password="";
    $array[0]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[1]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[2]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[3]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[4]=substr($minus,mt_rand(0,strlen("minus")-1),1);
    $array[5]=substr($nums,mt_rand(0,strlen("nums")-1),1);
    $array[6]=substr($mayus,mt_rand(0,strlen("mayus")-1),1);
    $array[7]=substr($minus,mt_rand(0,strlen("signos")-1),1);
    shuffle($array);
    $password=$array[0].$array[1].$array[2].$array[3].$array[4].$array[5].$array[6].$array[7];
    return $password;
}




