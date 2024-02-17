<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data JSON</title>
</head>
<body>
<?php
$kode_prov = $_GET['kode_prov'];
$kode_kab = $_GET['kode_kab'];
$data = [];
// $dir = 'data_ppwp/';
$dir = 'data_ppwp/'.$kode_prov; // Ganti dengan path direktori yang sesuai
// $dir = 'data_ppwp/'.$kode_prov.'/'.$kode_kab; // Ganti dengan path direktori yang sesuai
// Fungsi untuk mencari file yang berawalan "data_suara" di folder dan subfolder
function cariFile($dir) {
    // $result = [];
    $data = [];

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                // Jika $file adalah direktori, panggil kembali fungsi cariFile untuk mencari di dalamnya
                $data = array_merge($data, cariFile($filePath));
            } elseif (strpos($file, 'data_suara') === 0) {
                // Jika $file adalah file yang dicari, tambahkan ke dalam array result
                $json_data = file_get_contents($filePath);
                $data_ppwp = json_decode($json_data, true);
                foreach ($data_ppwp as $tps) {
                    if(isset($tps['data_tps']['chart'])){
                        if (!isset($tps['data_tps']['chart']['100025'])) {
                            $tps_data = $tps;
                            $data['suara_kosong'][$tps['kode_tps']]['detail'] = $tps_data;
                            $data['suara_kosong'][$tps['kode_tps']]['keterangan'] = "Tidak ada suara Anies";
                            $tps['data_tps']['chart']['100025'] = 0;
                        }
                        if (!isset($tps['data_tps']['chart']['100026'])) {
                            $tps['data_tps']['chart']['100026'] = 0;
                            $tps_data = $tps;
                            $data['suara_kosong'][$tps['kode_tps']]['detail'] = $tps_data;
                            $data['suara_kosong'][$tps['kode_tps']]['keterangan'] = "Tidak ada suara Prabowo";
                        }
                        if (!isset($tps['data_tps']['chart']['100027'])) {
                            $tps_data = $tps;
                            $data['suara_kosong'][$tps['kode_tps']]['detail'] = $tps_data;
                            $data['suara_kosong'][$tps['kode_tps']]['keterangan'] = "Tidak ada suara Ganjar";
                            $tps['data_tps']['chart']['100027'] = 0;
                        }
                        
                        // else{
                        //     echo "<br>Kode TPS: ".$tps['kode_tps']." tidak ada ganjar";
                        //     $tps_data = json_encode($tps);
                        //     $data[$tps['kode_tps']]['data_tps'] = $tps_data;
                        //     $data[$tps['kode_tps']]['status_masalah'] = "Tidak ada suara ganjar";
                        //     // continue;
                        //     echo $tps['data_tps']['images'][0];
                        //     // var_dump($tps['data_tps']['images']);
                        //     // end();

                        // }
                        $anies = $tps['data_tps']['chart']['100025'];
                        $prabowo = $tps['data_tps']['chart']['100026'];
                        $ganjar = $tps['data_tps']['chart']['100027'];
                        if ($anies+$prabowo+$ganjar>300) {
                            // echo "<br> Kode TPS: ".$tps['kode_tps'];
                            // $data = ;

                            $tps_data = $tps;
                            $data['masalah_jumlah_suara'][$tps['kode_tps']]['detail'] = $tps_data;
                            $data['masalah_jumlah_suara'][$tps['kode_tps']]['keterangan'] = "jumlah suara > 300";
                            // $data[$tps['kode_tps']]['status_masalah'] = "jumlah suara > 300";

                        }
                    }
                    
                }


                // var_dump($data_ppwp);
                // end();
                // var_dump($data);
                //             end();

                $result[] = $data;
            }
        }
    }

    return $data;
}
$hasilPencarian = cariFile($dir);

if (!empty($hasilPencarian)) {
    $folder_path_bermasalah="data_ppwp/data_tps_bermasalah.json";

    $json_data = json_encode($hasilPencarian);
    if (file_put_contents($folder_path_bermasalah, $json_data)) {
        echo "Data telah disimpan ke dalam file ".$kode_kab." JSON.<br>";
        // var_dump($file_path_prov);
        // end();
    } else {
        echo "Gagal menyimpan data ke dalam file JSON.<br>";
        // var_dump($file_path_tps);
        end();
    }
} else {
    echo "Tidak ada file yang ditemukan.";
}
?>
</body>
</html>
