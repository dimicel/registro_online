<?php
session_start();
session_regenerate_id();
$_SESSION['visitado_index'] = true; 

$modo_obras=0;
if ($modo_obras==0) header("Location: inicio.php");
else header("Location: modo_obras.html");
