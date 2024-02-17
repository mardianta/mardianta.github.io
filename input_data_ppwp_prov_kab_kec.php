<?php
// Mengecek apakah parameter 'nama' disertakan dalam URL
$folder_utama = 'data_ppwp';

// Menginisialisasi array untuk menyimpan nama subfolder
$subfolder_names = array();
if(isset($_GET['kode_prov'])&isset($_GET['kode_kab'])) {
        
    $kode_prov = $_GET['kode_prov'];
    $kode_kab = $_GET['kode_kab'];
    $kode_kec = scandir($folder_utama.'/'. $kode_prov.'/'. $kode_kab);

    foreach ($kode_kec as $kode) {
        if ($kode != '.' && $kode != '..' && $kode != '.DS_Store' && $kode != 'ppwp_0_'.$kode_prov.'_'.$kode_kab.'.json' ) {
            $path_kab = $folder_utama.'/'. $kode_prov.'/'. $kode;
            $kode_kel = scandir($folder_utama.'/'. $kode_prov.'/'. $kode_kab.'/'. $kode);
            if (count($kode_kel)<4) {
                $status = "ambil data";
                echo $kode." : "."<a target='_blank' href='http://localhost:8081/?kode_prov=$kode_prov&kode_kab=$kode_kab&kode_kec=$kode'>: ".$status." </a><br>";
            }
            else{
                $status = "perbarui data";
                echo $kode." : "."<a target='_blank' href='http://localhost:8081/?kode_prov=$kode_prov&kode_kab=$kode_kab&kode_kec=$kode'>: ".$status." </a><br>";
            }
        
        }
        
    }

    echo "Kode Prov, " . $kode_prov .", Kode Kab, " . $kode_kab . "!";
    // var_dump($kode);
        end();
}

    if(isset($_GET['kode_prov'])&isset($_GET['kode_kab'])) {
        
        $kode_prov = $_GET['kode_prov'];
        $kode_kab = $_GET['kode_kab'];
        $kode_kec = scandir($folder_utama.'/'. $kode_prov.'/'. $kode_kab);

        foreach ($kode_kec as $kode) {
            if ($kode != '.' && $kode != '..' && $kode != '.DS_Store' && $kode != 'ppwp_0_'.$kode_prov.'_'.$kode_kab.'.json' ) {
                $path_kab = $folder_utama.'/'. $kode_prov.'/'. $kode;
                echo $kode." : "."<a target='_blank' href='http://localhost:8080/?kode_prov=$kode_prov&kode_kab=$kode_kab&kode_kec=$kode'>$kode</a><br>";
            
            }
            
        }

        echo "Kode Prov, " . $kode_prov .", Kode Kab, " . $kode_kab . "!";
        // var_dump($kode);
            end();
    }

    
    // $kode_kab = $_GET['nama'];
    
    // Menampilkan pesan dengan nilai parameter 'nama'
   
 else {
    if (isset($_GET['kode_prov'])) {
        if ($_GET['kode_prov']!="") {
            $kode_prov = $_GET['kode_prov'];
    $kode_kab = scandir($folder_utama.'/'.$kode_prov);

        foreach ($kode_kab as $kode) {
            if ($kode != '.' && $kode != '..' && $kode != '.DS_Store' && $kode != 'ppwp_0_.json' ) {
                // $path_kab = $folder_utama.'/'. $kode_prov.'/'. $kode;
                // if (is_dir($path_kec)){
                //     $kode_prov = scandir($folder_utama.'/'. $kode_prov.'/'. $kode);
                // }
            // if (count($kode_prov) < 4) {
                echo $kode." : "."<a target='_blank' href='http://localhost:8080/?kode_prov=$kode_prov&kode_kab=$kode'>$kode</a><br>";
            // }
            // var_dump($kode);
            // ednd();
            }
            
        }
        var_dump($kode);
            ednd();
    // Jika parameter 'nama' tidak disertakan, menampilkan pesan kesalahan
    echo "Parameter 'kode_prov' tidak ditemukan dalam URL.";
        }
        # code...
    }
}
$kode_prov = scandir($folder_utama.'/');

        foreach ($kode_prov as $kode) {
            if ($kode != '.' && $kode != '..' && $kode != '.DS_Store' && $kode != 'ppwp_0_.json' ) {
                // $path_kab = $folder_utama.'/'. $kode_prov.'/'. $kode;
                // if (is_dir($path_kec)){
                //     $kode_prov = scandir($folder_utama.'/'. $kode_prov.'/'. $kode);
                // }
            // if (count($kode_prov) < 4) {
                echo $kode." : "."<a target='_blank' href='http://localhost:8080/?kode_prov=$kode'>$kode</a><br>";
            // }
            // var_dump($kode);
            // ednd();
            }
            
        }
?>
