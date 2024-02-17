<?php
// URL JSON yang akan di-scrap
$file_path = 'data_ppwp/ppwp_0.json';

// Membaca isi file JSON
$json_data = file_get_contents($file_path);

// Mendecode data JSON menjadi array PHP
$data_ppwp_0 = json_decode($json_data, true);

// Memeriksa apakah penguraian data berhasil
if ($data_ppwp_0 === null) {
    echo "Gagal mengurai data JSON.";
    // var_dump($data_ppwp_0);
    end();
} else {
    // Menampilkan data yang dibaca dari file JSON
    echo "Berhasil mengurai data JSON ppwp_.json <br>";
    // var_dump($data_ppwp_0);
}

foreach ($data_ppwp_0 as $item) {
    // Membuat folder berdasarkan nama kode
    echo $item['kode'];

    $folder_path_prov = "data_ppwp/".$item['kode'];
    $json_url_prov = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/'.$item['kode'].'.json';
    $json_data_prov = file_get_contents($json_url_prov);
    $json_prov_string = json_encode($json_data_prov, JSON_PRETTY_PRINT);

    $file_path_prov = $folder_path_prov.'/ppwp_0_'.$item['kode'].'.json';
 
    // Menyimpan data ke dalam file
    if (file_put_contents($file_path_prov, $json_data_prov)) {
        echo "Data telah disimpan ke dalam file JSON.";
        // var_dump($file_path_prov);
        // end();
    } else {
        echo "Gagal menyimpan data ke dalam file JSON.";
        var_dump($file_path_prov);
        end();
    }
}

?>
