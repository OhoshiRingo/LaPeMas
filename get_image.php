<?php


include "koneksi.php";


$query = "select * from img";
$sqls = mysqli_query($sql, $query);
$array = array();
$array["image"] = array();
if($sqls){
	while($row = mysqli_fetch_assoc($sqls)){
		$re["values"] = $row["base64"];
		array_push($array["image"], $re);
	}
	echo json_encode($array);
}

?>