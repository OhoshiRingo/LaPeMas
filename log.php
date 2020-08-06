<?php

	
	include "koneksi.php";	

	if($_SERVER["REQUEST_METHOD"] == "POST"){
		if(isset($_POST["username"]) && isset($_POST["password"])){
			$username = $_POST["username"];
			$password = $_POST["password"];
			$request = $_POST["request"];

			if($request == "login"){
				$login = new Login($username, $password, $sql);
				$login->login_queries();
			}else if($request == "signup"){
				$name = $_POST["name"];
				$email = $_POST["email"];
				$signup = new Signup($username, $password, $name, $email, $sql);
			}else if($request == "get_profile_data"){
				$profile = new Profile($username, $password, $sql);
				$profile->fetch_data();
			}
		
		}
	}

	class Profile{
		public $username;
		public $password;
		public $koneksi;
		public function __construct($username, $password, $koneksi){
			$this->username = $username;
			$this->password = $password;
			$this->koneksi = $koneksi;
		}

		function fetch_data(){
			$query = "select * from users where username='".$this->username."'";
			$array = array();
			$array["profile_data"] = array();

			if($this->query($query, "username") == null){
				$data["username"] = "null";
				$data["name"] = "null";
				$data["email"] = "null";
				$data["response"] = "Pengguna tidak terdaftar.";
			}else{
				$data["username"] = $this->query($query, "username");
				$data["name"] = $this->query($query, "name");
				$data["email"] = $this->query($query, "email");
				$data["response"] = "Pengguna terdaftar.";
			}

			

			array_push($array["profile_data"], $data);
			echo json_encode($array);
		}

		function query($arg0, $arg1){
			$array = array();
			$array["profile"] = array();
			$call_back = null;
			if(isset($arg0) && !isset($arg1) || $arg0 == null){
				if($row = mysqli_fetch_assoc($this->sql($arg0))){
					if($row["username"]){
						$db["response"] = "Ada";
						$db["username"] = $this->username;
						$db["name"] = $row["name"];
						$db["email"] = $row["email"];
					}else if(!$row["username"]){
						$db["response"] = "Tidak ada";
						$db["username"] = "null";
						$db["name"] = "null";
						$db["email"] = "null";
					}
					array_push($array["profile"], $db);
					echo json_encode($array);
				}
			}else if(isset($arg0) && isset($arg1)){
				if($row = mysqli_fetch_assoc($this->sql($arg0))){
					if($arg1 == "username"){
						$call_back= $row[$arg1];
					}else if($arg1 == "name"){
						$call_back = $row[$arg1];
					}else if($arg1 == "email"){
						$call_back = $row[$arg1];
					}
				}
			}
			return $call_back;
		}

		function sql($que){
			return $sql = mysqli_query($this->koneksi, $que);
		}
	}

	class Signup{
		public $username;
		public $password;
		public $name;
		public $email;
		public $koneksi;
		public function __construct($username, $password, $name, $email, $koneksi){
			$this->username = $username;
			$this->password = $password;
			$this->name = $name;
			$this->email = $email;
			$this->koneksi = $koneksi;
			$this->_check();
		}

		function _check(){
			if(strlen($this->username) <= 5){
				$this->sendResponse(false, "Username harus lebih dari 5 huruf.", "username");
			}else if(strlen($this->password) <= 5){
				$this->sendResponse(false, "Kata sandi harus lebih dari 5 huruf.", "password");
			}else if(!$this->name){
				$this->sendResponse(false, "Isi nama dengan benar.","name");
			}else if(!$this->email){
				$this->sendResponse(false, "Isi email dengan benar.","email");
			}else{
				$this->check_user();
			}	
		}

		function do_signup(){
			$query = "insert into users (`username`,`password`,`name`,`email`,`role`,`session`) values ('".$this->username."','".$this->password."','".$this->name."','".$this->email."','member','no_session')";
			$sql = mysqli_query($this->koneksi, $query);
			if($sql){
				$this->is_success();
			}
		}

		function is_success(){
			$query = "select * from users where username='".$this->username."'";
			$sql = mysqli_query($this->koneksi, $query);
			if($sql){
				$row = mysqli_fetch_assoc($sql);
				if($row["username"]){
					$this->signupResponse(true, "Dafter berhasil.","signup_success");
				}else{
					$this->signupResponse(false, "Daftar gagal.","signup_failed");
				}
			}
		}

		function signupResponse($boolean, $log, $state){
			$array = array();
			$array["signup_response"] = array();
			if($boolean !== true){
				if($state == "signup_failed"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
					$msg["session"] = "null";
					$msg["role"] = "null";
				}
				array_push($array["signup_response"] , $msg);
			}else if($boolean !== false){
				if($state == "signup_success"){
					$msg["response"] = $log;
					$msg["username"] = $this->username;
					$msg["name"] = $this->name;
					$msg["email"] = $this->email;
					$msg["session"] = "no_session";
					$msg["role"] = "member";

				}
				array_push($array["signup_response"], $msg);
			}
			echo json_encode($array);
		}

		function check_user(){
			$query = "select * from users where username='".$this->username."'";
			$ex = mysqli_query($this->koneksi, $query);
			if($ex){
				$row = mysqli_fetch_assoc($ex);
				if($row["username"] !== null || $row["username"]){
					$this->sendResponse(false, "Username telah digunakan oleh orang lain","user_exists");
				}else if($row["username"] == null || !$row["username"]){
					$this->do_signup();
				}
			}
		}

		function sendResponse($boolean, $log, $state){
			$array = array();
			$array["signup_response"] = array();

			if($boolean !== true){
				if($state == "username"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}else if($state == "password"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}else if($state == "name"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}else if($state == "email"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}else if($state == "user_exists"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}

				array_push($array["signup_response"], $msg);

			}

			else if($boolean !== false){
				if($state == "user_exists"){
					$msg["response"] = $log;
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
				}else if($state == "doesnt_exists"){
					$msg["response"] = $log;
					$msg["username"] = $this->username;
					$msg["name"] = $this->name;
					$msg["email"] = $this->email;
				}

			array_push($array["signup_response"] , $msg);
			}
                                                                 
			echo json_encode($array);
		}
	}

	class Login{
		public $username;
		public $password;
		public $koneksi;
		public $table_name;
		public function __construct($username, $password, $koneksi){
			$this->username = $username;
			$this->password = $password;
			$this->table_name = "users";
			$this->koneksi = $koneksi;
		}

		function login_queries(){
			$this->execute("select * from ".$this->table_name." where username='".$this->username."'");
		}
		
		function execute($values){
			$sql = mysqli_query($this->koneksi, $values);
			$response = array();
			$response["login_response"] = array();

			if($sql){
				$row = mysqli_fetch_assoc($sql);
				if(!$row["username"]){
					$msg["response"] = "User doesn't exists.";
					$msg["login"] = "error.";
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
					$msg["role"] = "null";
					$msg["session"] = "null";

					array_push($response["login_response"], $msg);
				}else if($row["password"] === $this->password){
					$msg["response"] = "Login successfuly.";
					$msg["login"] = "success";
					$msg["username"] = $this->username;
					$msg["name"] = $row["name"];
					$msg["email"] = $row["email"];
					$msg["role"] = $row["role"];
					$msg["session"] = $row["session"];

					array_push($response["login_response"], $msg);
				}else{
					$msg["response"] = "Username / Password doesn't match.";
					$msg["login"] = "error";
					$msg["username"] = "null";
					$msg["name"] = "null";
					$msg["email"] = "null";
					$msg["role"] = "null";
					$msg["session"] = "null";

					array_push($response["login_response"], $msg);
				}
			}else{
				return "SQL Error.";
			}
			$this->print(json_encode($response));
		}

		public function print($values){
			echo $values;
		}

	}







?>