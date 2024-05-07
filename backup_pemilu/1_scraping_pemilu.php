<?php
// URL JSON yang akan di-scrap
$json_url = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/0.json';

// Mengambil data dari URL JSON
$json_data = file_get_contents($json_url);
// var_dump($json_data);
// end();
// Mendecode data JSON menjadi array PHP
$data = json_decode($json_data, true);

// Memeriksa apakah pengambilan data berhasil
if ($data === null) {
    echo "Gagal mendapatkan atau mengurai data JSON.";
} else {
    // Menyimpan data ke dalam file JSON lokal
    $file_path = 'data_ppwp/ppwp_0.json';
    // $json_string = json_encode($data, JSON_PRETTY_PRINT);
    
    // Menyimpan data ke dalam file
    if (file_put_contents($file_path, $json_data)) {
        echo "Data telah disimpan ke dalam file JSON.";
    } else {
        echo "Gagal menyimpan data ke dalam file JSON.";
    }
}
?>
