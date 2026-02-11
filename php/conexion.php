<?php
$dominio=$_SERVER["HTTP_HOST"];
if ($dominio=="rotest.ulaboral.org"){
	$servidor="localhost";
	$usuario="ulaboral_myslq_dimi_fer";
	$password="=WOyu;J6B&I6";
	$db="ulaboral_imp_sec_online_test";
}
elseif ($dominio=="registro.ulaboral.org"){
	$servidor="localhost";
	$usuario="ulaboral_myslq_dimi_fer";
	$password="=WOyu;J6B&I6";
	$db="ulaboral_imp_sec_online";
}
else {
	$servidor="localhost";
	$usuario="root";
	$password="#Ulab0ral@2223#";
	$db="ulaboral_imp_sec_online";
}

if (!isset($mysqli)){		
	$mysqli = new MySQLi($servidor, $usuario, $password, $db);
	if ($mysqli==false) {
		return false;
	}
	else{
		$mysqli->set_charset('utf8');
		return true;
	}
}
