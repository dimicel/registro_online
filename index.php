<?php
session_start();
session_regenerate_id();
$_SESSION['visitado_index'] = true; 

$visitadoIndex = true; // O cualquier otro valor que desees pasar

// Codificar la información de la sesión en una cadena segura
$token = base64_encode(json_encode(['visitado_index' => $visitadoIndex]));

$modo_obras = 0;
if ($modo_obras == 0) header("Location: inicio.php?token=$token");
else header("Location: modo_obras.html?token=$token");