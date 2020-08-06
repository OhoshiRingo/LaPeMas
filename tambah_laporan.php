<?php

include "koneksi.php";




if($_SERVER["REQUEST_METHOD"] == "POST"){
	$isi_laporan = $_POST["isi"];
	$tanggal_laporan = $_POST["tanggal"];
	$judul_laporan = $_POST["judul"];
	$nik = $_POST["nik"];
	$img_path = $_POST["image_path"];
	$username = $_POST["username"];
	$image_bits = $_POST["image_bits"];

	$realpath = "";

	foreach(explode(" ", $judul_laporan) as $well){
		$realpath .= $username."_".$well;
	}

	$realpath .= ".jpg";

	$laporan = new Laporan($sql, $isi_laporan, $tanggal_laporan, $judul_laporan, $nik, $realpath);


	$laporanUpload = new LaporanUploadImage($sql, $isi_laporan, $tanggal_laporan, $judul_laporan, $image_bits, $realpath, $username);


}else{
	echo "HELLO WORLD";
}


class LaporanUploadImage{
	public $isi;
	public $tanggal;
	public $judul;
	public $image_bits;
	public $username;
	public $image_path;
	public $koneksi;

	public function __construct($koneksi, $isi, $tanggal, $judul, $image_bits, $image_path, $username){
		$this->koneksi = $koneksi;
		$this->isi = $isi;
		$this->tanggal = $tanggal;
		$this->judul = $judul;
		$this->image_bits = $image_bits;
		$this->username = $username;
		$this->image_path = $image_path;
		$this->startUpload();
	}



	public function startUpload(){
		$this->check_dir();
		$real = "";
		foreach(explode(" ",$this->judul) as $go){
			$real .= $this->username."_".$go;
		}
		$real .= ".jpg";

		if(file_put_contents($this->check_dir().$real, base64_decode($this->image_bits))){
			$this->sendResponse(true);

		}else{
			$this->sendResponse(false);
		}

	}

	function sendResponse($response)
	{
	
		if($response !== false){
			echo "Laporan berhasil di kirim.";
		}else if($response !== true){
			echo "Laporan gagal di kirim.";
		}
	}

	public function check_dir(){

		$directory = "img_upload/";
		if(!file_exists($directory)){
			mkdir($directory);
		}

		return $directory;
	}
}


class Laporan{
	public $isi;
	public $tanggal;
	public $judul;
	public $nik;
	public $foto;
	public $koneksi;
	public $image_path;

	public function __construct($koneksi, $isi, $tanggal, $judul, $nik, $image_path){
		$this->isi = $isi;
		$this->tanggal = $tanggal;
		$this->judul = $judul;
		$this->nik = $nik;
		$this->koneksi = $koneksi;
		$this->image_path = $image_path;
		$this->send();	
	}

	public function set_pict($pict){
		$this->foto = $pict;
	}


	public function send(){
		if($sql = mysqli_query($this->koneksi, $this->query())){
			if($sql){
				echo "Laporan berhasil dikirim.";
			}else{
				echo "Laporan gagal dikirim.";
			}
		}
	}
	function query(){
		$query = "insert into pengaduan (`tgl_pengaduan`,`nik`,`judul_laporan`,`isi_laporan`,`foto`,`status`) values ('".$this->tanggal."','".$this->nik."','".$this->judul."','".$this->isi."','".$this->image_path."','proses')";


		return $query;
	}


}

?>