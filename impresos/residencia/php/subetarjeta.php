<?php

if(is_uploaded_file($_FILES['tarjeta_sanitaria']['tmp_name'])){
    $ruta=__DIR__."/"."tmp/" . $_FILES['tarjeta_sanitaria']['name'];
    if(!move_uploaded_file($_FILES['tarjeta_sanitaria']['tmp_name'], $ruta)) exit("almacenar");
    chmod($ruta, 0777);
    exit("ok");
}
else exit("archivo");

