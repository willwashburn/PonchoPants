<?php
	date_default_timezone_set('America/Chicago');
	

	$conn = mysql_connect("69.164.222.13", "citibike", "citibikewoo");
	mysql_select_db("citi_bike",$conn);	
	mysql_set_charset("UTF8", $conn);

?>