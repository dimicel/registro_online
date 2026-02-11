<?php
include("conexion.php");exit(0);
header("Content-Type: text/html;charset=utf-8");

$data=array();
if ($mysqli->errno>0) {
    $data["error"]="server";
    exit(json_encode($data));
}

$res=$mysqli->query("select * from config_centro");

$data["error"]="ok";
$data["registro"]=array();

while ($reg=$res->fetch_assoc()){
    $data["registro"]["centro"]= $reg["centro"];
}


exit(json_encode($data));

