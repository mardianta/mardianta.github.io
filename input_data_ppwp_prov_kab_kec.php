<?php
// Mengecek apakah parameter 'nama' disertakan dalam URL
$folder_utama = 'data_ppwp';

// Mendapatkan daftar file dan folder di dalam folder utama

// Menginisialisasi array untuk menyimpan nama subfolder
$subfolder_names = array();
if(isset($_GET['kode_prov'])) {
    // Mengambil nilai parameter 'nama' dari URL
    $kode_prov = $_GET['kode_prov'];
    $kode_kab = $_GET['kode_kab'];
    $kode_kec = scandir($folder_utama.'/'. $kode_prov.'/'. $kode_kab);

    foreach ($kode_kec as $kode) {
        if ($kode != '.' && $kode != '..' && $kode != '.DS_Store' && $kode != 'ppwp_0_'.$kode_prov.'.json' ) {
            $path_kec = $folder_utama.'/'. $kode_prov.'/'. $kode;
            var_dump($path_kec);
            ednd();
            if (is_dir($path_kec)){
                $kode_kec = scandir($folder_utama.'/'. $kode_prov.'/'. $kode);
            }
        if (count($kode_kec) < 4) {
            echo $kode." : "."<a target='_blank' href='http://localhost:8081/?kode_prov=$kode_prov&kode_kab=$kode_kab&kode_kec=$kode'>$kode</a><br>";
        }
        var_dump($kode);
        ednd();
    }
        
    }
    // $kode_kab = $_GET['nama'];
    
    // Menampilkan pesan dengan nilai parameter 'nama'
    echo "Kode Prov, " . $kode_prov . "!";
} else {
    // Jika parameter 'nama' tidak disertakan, menampilkan pesan kesalahan
    echo "Parameter 'kode_prov' tidak ditemukan dalam URL.";
}
?>
