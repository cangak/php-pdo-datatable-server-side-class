<?php
session_start();
include "lib/config.php";

//kolom apa saja yang akan ditampilkan
$columns = array(
	'nama_prov',
	'nama_kab',
	'nama_kec',
	);

//lakukan query data dari 3 table dengan inner join
	$query = $datatable->get_custom("select provinsi.nama_prov,kabupaten.nama_kab, kecamatan.nama_kec,id_kec
from provinsi inner join kabupaten 
on provinsi.id_prov=kabupaten.id_prov
inner join kecamatan on kabupaten.id_kab=kecamatan.id_kab",$columns);

	//buat inisialisasi array data
	$data = array();

	foreach ($query	as $value) {

	//array sementara data
	$ResultData = array();
	//masukan data ke array sesuai kolom table
	$ResultData[] = $value->nama_prov;
	$ResultData[] = $value->nama_kab;
	$ResultData[] = $value->nama_kec;

	//bisa juga pake logic misal jika value tertentu maka outputnya

	//kita bisa buat tombol untuk keperluan edit, delete, dll, 
	$ResultData[] = "<a href='url_edit/".$value->id_kec."' class='btn btn-primary'>Edit</a> <a href='url_edit/".$value->id_kec."' class='btn btn-danger'>Hapus</a> <a href='url_edit/".$value->id_kec."' class='btn btn-primary'>tombol 3</a>";

	//memasukan array ke variable $data

	$data[] = $ResultData;
}

//set data
$datatable->set_data($data);
//create our json
$datatable->create_data();
