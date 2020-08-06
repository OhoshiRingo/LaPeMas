<?php

	$host = "localhost";
	$username = "database";
	$password = "root";
	$db_name = "laporankerja";

	$sql = mysqli_connect($host, $username, $password, $db_name) or die("ERROR CONNECTION");

?>