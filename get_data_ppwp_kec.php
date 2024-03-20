<?php
// URL JSON yang akan di-scrap
// $json_url = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/0.json';
$kode_prov = $_GET['kode_prov'];
$kode_kab = $_GET['kode_kab'];
$kode_kec = $_GET['kode_kec'];
// var_dump($kode_kab);
// end();
// $json_url = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/'.$kode_prov.'.json';
// 
// Mengambil data dari URL JSON
// $json_data = file_get_contents($json_url);

$data = [];

// Konversi data menjadi format JSON
// $json = json_encode($data);

// Mengatur header untuk menandakan bahwa response adalah JSON
header('Content-Type: application/json');

// Mengembalikan JSON
// echo $json;

        // $folder_path_prov = "data_ppwp/".$kode_prov;
        // if (!is_dir($folder_path_prov)) {
        //     mkdir($folder_path_prov);
        // }

// Mendecode data JSON menjadi array PHP
// $data = json_decode($json_data, true);

// Memeriksa apakah pengambilan data berhasil
// if ($data === null) {
//     echo "Gagal mendapatkan atau mengurai data JSON.<br>";
// } else {
//     foreach ($data as $kab) {
//         // Membuat folder berdasarkan nama kode
        // if (!is_dir($folder_path_kab)) {
        //     mkdir($folder_path_kab);
        // }

        // $json_url_kab = 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/'.$kode_prov.'/'.$kode_kab.'.json';
        // $json_data_kab = file_get_contents($json_url_kab);
        // $json_kab_string = json_decode($json_data_kab, true);

        // $file_path_kab = $folder_path_kab.'/ppwp_0_'.$kode_prov.'_'.$kode_kab.'.json';
    
        // Menyimpan data ke dalam file
        // if (file_put_contents($file_path_kab, $json_data_kab)) {
            // echo "Data Kab. ".$kode_kab." telah disimpan ke dalam file JSON.<br>";
            
        // } else {
            // echo "Gagal menyimpan data ke dalam file JSON.<br>";
            // var_dump($file_path_prov);
            // end();
        // }

        // foreach ($json_kab_string as $kec) {
            // Membuat folder berdasarkan nama kode
            // $folder_path_kec = "data_ppwp/".$kode_prov.'/'.$kode_kab.'/'.$kode_kec;
            // if (!is_dir($folder_path_kec)) {
            //     mkdir($folder_path_kec);
            // }
    
            $json_url= 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/'.$kode_prov.'/'.$kode_kab.'/'.$kode_kec.'.json';
            $json_data = file_get_contents($json_url);
            $json_data_string = json_decode($json_data, true);
    
            // $file_path_kec = $folder_path_kec.'/ppwp_0_'.$kode_prov.'_'.$kode_kab.'_'.$kode_kec.'.json';
        
            // Menyimpan data ke dalam file
            // if (file_put_contents($file_path_kec, $json_data)) {
                // echo "Data Kec.".$kec['nama'].$kode_kec." Kab. ".$kode_kab."telah disimpan ke dalam file JSON.<br>";
                // var_dump($json_data);
                // end();
            // } else {
                // echo "Gagal menyimpan data ke dalam file JSON.<br>";
                // var_dump($file_path_kec);
                // end();
            // }
            foreach ($json_data_string as $kel) {
                // Membuat folder berdasarkan nama kode
                // $folder_path_kel = "data_json/".$kode_prov.'/'.$kode_kab.'/'.$kode_kec.'/'.$kel['kode'];
                // if (!is_dir($folder_path_kel)) {
                    // mkdir($folder_path_kel);
                // }
        
                $json_url= 'https://sirekap-obj-data.kpu.go.id/wilayah/pemilu/ppwp/'.$kode_prov.'/'.$kode_kab.'/'.$kode_kec.'/'.$kel['kode'].'.json';
                $json_data = file_get_contents($json_url);
                $json_data_string = json_decode($json_data, true);
        
                // $file_path_kel = $folder_path_kel.'/ppwp_0_'.$kode_prov.'_'.$kode_kab.'_'.$kode_kec.'_'.$kel['kode'].'.json';
            
                // Menyimpan data ke dalam file
                // if (file_put_contents($file_path_kel, $json_data)) {
                    // echo "Data telah disimpan ke dalam file JSON.<br>";
                    // var_dump($file_path_prov);
                    // end();
                // } else {
                    // echo "Gagal menyimpan data ke dalam file JSON.<br>";
                    // var_dump($file_path_kel);
                    // end();
                // }

                foreach ($json_data_string as $tps) {
                    // Membuat folder berdasarkan nama kode
                    // $folder_path_tps = "data_json/".$kode_prov.'/'.$kode_kab.'/'.$kode_kec.'/'.$kel['kode'].'/'.$tps['kode'];
                    // if (!is_dir($folder_path_tps)) {
                        // mkdir($folder_path_tps);
                    // }
                        // echo $kode_prov.'/'.$kode_kab.'/'.$kode_kec.'/'.$kel['kode'].'/'.$tps['kode'].'.json'.';<br>';
                        // var_dump();
                        // end();

                    $json_url= 'https://sirekap-obj-data.kpu.go.id/pemilu/hhcw/ppwp/'.$kode_prov.'/'.$kode_kab.'/'.$kode_kec.'/'.$kel['kode'].'/'.$tps['kode'].'.json';
                    // var_dump($json_url);
                    // end();
                    $json_data = file_get_contents($json_url);
                    $json_data_string = json_decode($json_data, true);
                    $data[$tps['kode']]['kode_prov'] = $kode_prov;
                    $data[$tps['kode']]['kode_kab'] = $kode_kab;
                    $data[$tps['kode']]['kode_kec'] = $kode_kec;
                    $data[$tps['kode']]['kode_kel'] = $kel['kode'];
                    $data[$tps['kode']]['kode_tps'] = $tps['kode'];
                    $data[$tps['kode']]['data_tps'] = $json_data_string;
                    
                    // $file_path_tps = $folder_path_tps.'/ppwp_0_'.$kode_prov.'_'.$kode_kab.'_'.$kode_kec.'_'.$kel['kode'].'_'.$tps['kode'].'.json';
                
                    // Menyimpan data ke dalam file
                    // if (file_put_contents($file_path_tps, $json_data)) {
                        // echo "Data telah disimpan ke dalam file JSON.<br>";
                        // var_dump($file_path_prov);
                        // end();
                    // } else {
                        // echo "Gagal menyimpan data ke dalam file JSON.<br>";
                        // var_dump($file_path_tps);
                        // end();
                    // }
                }
            }
        // }
        $folder_path_kab = "data_ppwp/".$kode_prov.'/'.$kode_kab;
        $folder_path_kec_data=$folder_path_kab."/".$kode_kec."/"."data_suara_ppwp_".$kode_kec.".json";
        // var_dump($folder_path_kec_data);
        // end();

                    $json_data = json_encode($data);

                    // Menyimpan data ke dalam file
                    if (file_put_contents($folder_path_kec_data, $json_data)) {
                        echo "Data telah disimpan ke dalam file data_suara_ppwp_".$kode_kec." JSON.<br>";
                        // var_dump($file_path_prov);
                        // end();
                    } else {
                        echo "Gagal menyimpan data ke dalam file JSON.<br>";
                        // var_dump($file_path_tps);
                        end();
                    }


    // }
// }
?>
