<?php

	include "koneksi.php";

	if($_SERVER["REQUEST_METHOD"] == "POST"){

		if($_POST["request"] == "kirim_tanggapan"){
				$tanggapan = new Tanggapan($sql, $_POST["id_pengaduan"], $_POST["tgl_tanggapan"],$_POST["tanggapan"], $_POST["id_petugas"], $_POST["status"]);
			}else if($_POST["request"] == "ambil_tanggapan"){
				$tanggapan = new GetTanggapan($sql, $_POST["id_pengaduan"]);
			}else if($_POST["request"] == "update_tanggapan"){
				$tanggapan = new UpdateTanggapan($sql, $_POST["id_pengaduan"], $_POST["tanggapan"], $_POST["status"]);
			}

	
	


	}


	class UpdateTanggapan{
		public $id_pengaduan;
		public $connection;
		public $status;
		public $tanggapan;
		public function __construct($connection, $id_pengaduan, $tanggapan, $status){
			$this->id_pengaduan = $id_pengaduan;
			$this->connection = $connection;
			$this->tanggapan = $tanggapan;
			$this->status = $status;
			$this->update();
			$this->up();
			$this->succes();
		}
		
		public function succes(){
			echo "Berhasil di update.";
		}
		public function update(){
			$query = "update pengaduan set `status`='".$this->status."' where id_pengaduan='".$this->id_pengaduan."'";
			$sql =mysqli_query($this->connection, $query);
		}
		public function up(){
			$que = "update tanggapan set `tanggapan`='".$this->tanggapan."' where id_pengaduan='".$this->id_pengaduan."'";
			$sq = mysqli_query($this->connection, $que);
		}
	}


	class GetTanggapan{
		public $id_pengaduan;
		public $connection;

		public function __construct($connection, $id_pengaduan){
			$this->id_pengaduan = $id_pengaduan;
			$this->connection = $connection;
			$this->check();
		}


		public function check(){
			$tang = array();
			$tang["tanggapan"] = array();

			$sql = mysqli_query($this->connection, $this->query());
			if($sql){
				$row = mysqli_fetch_assoc($sql);
				if(!$row["id_tanggapan"]){
					$re["res_tanggapan"] = "error";
					$re["nama_petugas"] = null;
					$re["tgl_tanggapan"] = null;
					$re["tanggapan"] = null;
					$re["foto_petugas"] = null;
					$re["id_tanggapan"] = null;
					$re["available"] = null;
					array_push($tang["tanggapan"], $re);
					echo json_decode($tang);
				}else{
					$re["res_tanggapan"] = "success";
					$re["nama_petugas"] = $row["nama_petugas"];
					$re["tgl_tanggapan"] = $row["tgl_tanggapan"];
					$re["tanggapan"] = $row["tanggapan"];
					$re["foto_petugas"] = $row["foto_petugas"];
					$re["id_tanggapan"] = $row["id_tanggapan"];
					$re["available"] = "true";
					array_push($tang["tanggapan"] , $re);
					echo json_encode($tang);
				}
				
			}

		}


		function query(){
			$query = "SELECT * FROM pengaduan INNER JOIN tanggapan USING (id_pengaduan) INNER JOIN petugas USING (id_petugas) WHERE id_pengaduan = '".$this->id_pengaduan."' ORDER BY id_pengaduan desc";

			return $query;
		}
	}

	

	class Tanggapan{
		public $id_pengaduan;
		public $tanggal;
		public $id_petugas;
		public $tanggapan;
		public $connection;
		public $status;
		public function __construct($connection, $id_pengaduan, $tanggal, $tanggapan, $id_petugas, $status){
			$this->id_pengaduan = $id_pengaduan;
			$this->tanggal = $tanggal;
			$this->id_petugas = $id_petugas;
			$this->tanggapan = $tanggapan;
			$this->connection = $connection;
			$this->status = $status;
			$this->insert();
			$this->insert_juga();
		}

		public function insert_juga(){
			$sql = mysqli_query($this->connection, $this->insert_juga_query());
			if($sql){

			}else{

			}
		}

		public function insert(){
			$response = array();
			$response["tanggapan"] = array();
			$sql = mysqli_query($this->connection, $this->insert_query());
				if($sql){
					echo "Tanggapan berhasil ditambahkan.";
				}else{
					echo "Tanggapan gagal ditambahkan.";
				}
			}
		
		function insert_juga_query(){
			$query = "update pengaduan set `status` = '".$this->status."' where id_pengaduan = '".$this->id_pengaduan."'";

			return $query;
		}

		function insert_query(){
			$query = "insert into tanggapan (`id_pengaduan`,`tgl_tanggapan`,`tanggapan`,`id_petugas`) values ('".$this->id_pengaduan."','".$this->tanggal."','".$this->tanggapan."','".$this->id_petugas."')";

			return $query;
		}
	}

?>