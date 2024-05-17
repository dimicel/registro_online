<?php
if (!isset($mysqli)){		
	$mysqli = new MySQLi("localhost", "ulaboral_myslq_dimi_fer", "=WOyu;J6B&I6", "ulaboral_hosteleria")
	if ($mysqli==false) {
		return false;
	}
	else{
		return true;
	}
}
?>