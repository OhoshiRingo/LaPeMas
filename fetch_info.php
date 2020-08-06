<?php

	include "koneksi.php";

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		$username = $_POST["username"];
		$view_as = $_POST["get"];
		$fetch = new Fetching($sql, $username, $view_as);
	}




	class Fetching{
		public $username;
		public $connection;
		public $view_as;
		public function __construct($connection, $username, $view_as){
			$this->connection = $connection;
			$this->username = $username;
			$this->view_as = $view_as;
			$this->check();
		}

		public function check(){
			$fetch_response = array();
			$fetch_response["fetch_info"] = array();

			if($sql = mysqli_query($this->connection, $this->query_petugas())){
				if($sql){
					$row = mysqli_fetch_assoc($sql);
					if($row["username"]){
						$re["nama_petugas"] = $row["nama_petugas"];
						$re["username_petugas"] = $row["username"];
						$re["telp_petugas"] = $row["telp"];
						$re["level_petugas"] = $row["level"];
						$re["foto_petugas"] = $row["foto_petugas"];
						array_push($fetch_response["fetch_info"], $re);
						echo json_encode($fetch_response);
					}else{
						if($this->view_as == "masyarakat"){
							$this->check_2();
						}
					}
				}
			}
		}

		public function check_2(){
			$fetch_response = array();
			$fetch_response["fetch_info"] = array();

			if($sql = mysqli_query($this->connection, $this->query_masyarakat())){
				if($sql){
					$row = mysqli_fetch_assoc($sql);
					$re["nama_masyarakat"] = $row["name"];
					$re["username_masyarakat"] = $row["username"];
					$re["email_masyarakat"] = $row["email"];
					$re["telp_masyarakat"] = $row["telp"];
					$re["nik_masyarakat"] = $row["nik"];
					$re["foto_masyarakat"] = $row["foto_masyarakat"];
					array_push($fetch_response["fetch_info"], $re);
					echo json_encode($fetch_response);
				}
			}
		}

		function query_petugas(){
			$query = "select * from petugas where username='".$this->username."'";
			return $query;
		}
		function query_masyarakat(){
			$query = "select * from users where username='".$this->username."'";
			return $query;
		}
	}


?>