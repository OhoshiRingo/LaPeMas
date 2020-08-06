<?php

include "koneksi.php";


if($_SERVER["REQUEST_METHOD"] == "POST"){
	$request = $_POST["request"];
	$report =  new ReportData($sql);
	if($request == "masyarakat"){
		$report->setNik($_POST["nik"]);
		$report->setStatement($request);
		$report->masyarakatGetData();
	}else if($request == "petugas"){
		$report->setStatement($request);
		$report->petugasGetData();
	}else if($request == "admin"){
		$report->setStatement($request);
		$report->petugasGetData();
	}
}


class ReportData{
	public $nik;
	public $statement;
	public $koneksi;
	public function __construct($koneksi){
		$this->koneksi = $koneksi;
	}
	public function setStatement($statement){
		$this->statement = $statement;
	}
	public function setNik($nik){
		$this->nik = $nik;
	}

	public function petugasGetData(){
		$data = array();
		$data["laporan"] = array();
		if($sql = mysqli_query($this->koneksi, $this->query())){
			if($sql)
			{
				while($row = mysqli_fetch_assoc($sql)){
					$re["id_pengaduan"] = $row["id_pengaduan"];
					$re["tgl_pengaduan"] = $row["tgl_pengaduan"];
					$re["judul_laporan"] = $row["judul_laporan"];
					$re["isi_laporan"] = $row["isi_laporan"];
					$re["foto"] = $row["foto"];
					$re["status"] = $row["status"];
					$re["foto_masyarakat"] = $row["foto_masyarakat"];
					$re["nama_masyarakat"] = $row["name"];
					$re["nik"] = $row["nik"];
					$re["username"] = $row["username"];
					array_push($data["laporan"], $re);
				}
				echo json_encode($data);
			}
		}
	}

	public function masyarakatGetData(){
		$data = array();
		$data["laporan"] = array();
		if($sql = mysqli_query($this->koneksi, $this->query())){
			if($sql){
				while($row = mysqli_fetch_assoc($sql)){
					$re["id_pengaduan"] = $row["id_pengaduan"];
					$re["tgl_pengaduan"] = $row["tgl_pengaduan"];
					$re["judul_laporan"] = $row["judul_laporan"];
					$re["isi_laporan"]= $row["isi_laporan"];
					$re["foto"] = $row["foto"];
					$re["status"] = $row["status"];
					$re["foto_masyarakat"] = $row["foto_masyarakat"];
					$re["nama_masyarakat"] = $row["name"];
					$re["nik"] = $row["nik"];
					$re["username"] = $row["username"];

					array_push($data["laporan"], $re);
				}
				echo json_encode($data);
			}
		}
	}

	function query(){
		$query = "";
		if($this->statement == "masyarakat"){
			$query .= "select * from pengaduan inner join users using (nik) where nik='".$this->nik."' order by pengaduan.id_pengaduan desc";
		}else if($this->statement == "petugas"){
			$query .= "select * from pengaduan inner join users using (nik) order by id_pengaduan desc";
		}else if($this->statement == "admin"){
			$query .= "select * from pengaduan inner join users using (nik) order by id_pengaduan desc";
		}


		return $query;
	}
}


?>

<!-- SELECT * FROM pengaduan INNER JOIN users USING (nik) INNER JOIN tanggapan USING (id_pengaduan) -->