<?php

include "koneksi.php";


if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST["username"];
	$password = $_POST["password"];

	if($_POST["request"] == "login"){
		$login = new LoginClass($sql, $_POST["username"], $_POST["password"]);
		$login->login();
	}else if($_POST["request"] == "signup"){
		$signup = new SignupClass($sql);
		$signup->setData($username, $password, $_POST["nik"], $_POST["name"], $_POST["telp"], $_POST["email"]);
	}
}

class SignupClass{
	public $username;
	public $password;
	public $nik;
	public $name;
	public $telp;
	public $email;
	public $koneksi;
	public function __construct($koneksi){
		$this->koneksi = $koneksi;
	}


	public function setData($username, $password, $nik, $name, $telp, $email){
		$this->username = $username;
		$this->password = $password;
		$this->nik = $nik;
		$this->name = $name;
		$this->telp = $telp;
		$this->email = $email;
		$this->check();
	}

	function check(){
		$query_check = "select * from users where username='".$this->username."'";
		$s = mysqli_query($this->koneksi, $query_check);
		if($s){
			$r = mysqli_fetch_assoc($s);
			if(!$r["username"]){
				$this->execute();
			}else if($r["username"] === $this->username){
				$this->error_exception("user_exists");
			}else{
				$this->error_exception("cannot_signup");
			}
		}
	}

	function error_exception($exception){
		$array = array();
		$array["signup_response"] = array();
		if($exception == "user_exists"){
			$re["response"] = $exception;
			$re["username"] = null;
			array_push($array["signup_response"], $re);
			echo json_encode($array);
		}else if($exception == "signup_error"){
			$re["response"] = "signup_error";
			$re["username"] = null;
			array_push($array["signup_response"], $re);
			echo json_encode($array);
		}else if($exception == "cannot_signup"){
			$re["response"] = $exception;
			$re["username"] = null;
			array_push($array["signup_response"], $re);
			echo json_encode($array);
		}
	}

	function execute(){
		$array = array();
		$array["signup_response"] = array();
		if($sql = mysqli_query($this->koneksi, $this->query())){
			$re["response"] = "signup_success";
			$re["username"] = $this->username;
			$re["password"] = $this->password;
			$re["nik"] = $this->nik;
			array_push($array["signup_response"], $re);
			echo json_encode($array);
		}
	}
	function query(){
		$query = "insert into users (`username`,`password`,`name`,`email`,`nik`,`telp`,`foto_masyarakat`,`session`) values ('".$this->username."','".$this->password."','".$this->name."','".$this->email."','".$this->nik."','".$this->telp."','img_masyarakat/default.jpg','no_session')";


		return $query;
	}

}

class LoginClass{

	public $koneksi;
	public $username;
	public $password;

	public function __construct($koneksi, $username, $password){
		$this->koneksi= $koneksi;
		$this->username = $username;
		$this->password = $password;
	}


	public function login(){
		$sql = mysqli_query($this->koneksi, $this->login_query());
		$array = array();
		$array["login_response"] = array();


		if($sql){
			$row = mysqli_fetch_assoc($sql);
			if($row["username"]){
				if($row["password"] === $this->password){
					$re["response"] = "login_success";
					$re["name"] = $row["name"];
					$re["username"] = $this->username;
					$re["password"] = $this->password;
					$re["email"] = $row["email"];
					$re["telp"] = $row["telp"];
					$re["nik"] = $row["nik"];
					$re["session"] = $row["session"];
					$re["login_as"] = "masyarakat";
					$re["foto_masyarakat"] = $row["foto_masyarakat"];
					array_push($array["login_response"], $re);
					echo json_encode($array);
				}else if($row["password"] !== $this->password){
					$re["response"] = "password_doesnt_match";
					$re["name"] = null;
					$re["username"] = null;
					$re["password"] = null;
					$re["email"] = null;
					$re["telp"] = null;
					$re["nik"] = null;
					$re["session"] = null;
					$re["login_as"] = null;
					array_push($array["login_response"], $re);
					echo json_encode($array);
				}
			}else{
				$this->check_employee();
			}
		}
	}

	public function check_employee(){
		$array = array();
		$array["login_response"] = array();
		if($sql = mysqli_query($this->koneksi, $this->login_query_2())){
			if($sql){
				$row = mysqli_fetch_assoc($sql);
				if($row["password"] === $this->password){
					$re["response"] = "login_success";
					$re["nama_petugas"] = $row["nama_petugas"];
					$re["username"] = $this->username;
					$re["password"] = $this->password;
					$re["level"] = $row["level"];
					$re["telp"] =$row["telp"];
					$re["foto_petugas"] = $row["foto_petugas"];
					$re["login_as"] = "petugas";
					$re["id_petugas"] = $row["id_petugas"];
					array_push($array["login_response"], $re);
					echo json_encode($array);
				}else if($row["password"] !== $this->password){
					$re["response"] = "password_doesnt_match";
					$re["nama_petugas"] = null;
					$re["username"] = null;
					$re["password"] = null;
					$re["level"] = null;
					$re["telp"] = null;
					$re["foto_petugas"] = null;
					$re["login_as"] = null;
					$re["id_petugas"] = null;
					array_push($array["login_response"], $re);
					echo json_encode($array);
				}else{
					$re["response"] = "login_error";
					$re["nama_petugas"] = null;
					$re["username"] = null;
					$re["password"] = null;
					$re["level"] = null;
					$re["telp"] = null;
					$re["foto_petugas"] = null;
					$re["login_as"] = null;
					$re["id_petugas"] = null;
					array_push($array["login_response"], $re);
					echo json_encode($array);
				}
			}
		}
	}

	function login_query_2(){
		$query = "select * from petugas where username='".$this->username."'";

		return $query;
	}
	function login_query(){
		$query = "select * from users where username='".$this->username."'";

		return $query;
	}



}

?>