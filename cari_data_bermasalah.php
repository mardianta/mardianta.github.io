<?php
// Fungsi untuk mencari file yang berawalan "data_suara" di folder dan subfolder
function cariFile($dir) {
    $result = [];

    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            $filePath = $dir . '/' . $file;
            if (is_dir($filePath)) {
                // Jika $file adalah direktori, panggil kembali fungsi cariFile untuk mencari di dalamnya
                $result = array_merge($result, cariFile($filePath));
            } elseif (strpos($file, 'data_suara') === 0) {
                // Jika $file adalah file yang dicari, tambahkan ke dalam array result
                $result[] = $filePath;
                
            }
        }
    }

    return $result;
}

// Path awal direktori
$dir = 'data_ppwp'; // Ganti dengan path direktori yang sesuai

$hasilPencarian = cariFile($dir);

$total_suara = 0;
$data_tps= [];
$suara_bermasalah_anies = 0;
$suara_bermasalah_prabowo = 0;
$suara_bermasalah_ganjar = 0;
if (!empty($hasilPencarian)) {
    echo "File yang ditemukan:<br>";
    foreach ($hasilPencarian as $filePath) {
        $json_data = file_get_contents($filePath);
                $data_ppwp = json_decode($json_data, true);
                foreach ($data_ppwp as $tps) {
                    // var_dump($tps['data_tps']['chart']['100025']);
                    // end();
                    // echo "<br>Kode TPS : ".$tps['kode_tps'];
                    if(isset($tps['data_tps']['chart'])){
                        if (!isset($tps['data_tps']['chart']['100025'])) {
                            $tps['data_tps']['chart']['100025'] = 0;
                        }
                        if (!isset($tps['data_tps']['chart']['100026'])) {
                            $tps['data_tps']['chart']['100026'] = 0;
                        }
                        if (!isset($tps['data_tps']['chart']['100027'])) {
                            $tps['data_tps']['chart']['100027'] = 0;
                        }
                    $anies = $tps['data_tps']['chart']['100025'];
                    $prabowo = $tps['data_tps']['chart']['100026'];
                    $ganjar = $tps['data_tps']['chart']['100027'];
                    
                    if($anies+$prabowo+$ganjar>300){
                        $data_tps[] =$tps; 
                        $suara_bermasalah_anies = $suara_bermasalah_anies +$anies;
                        $suara_bermasalah_prabowo = $suara_bermasalah_prabowo +$prabowo;
                        $suara_bermasalah_ganjar = $suara_bermasalah_ganjar +$ganjar;

                    }
                    }
                    
                }
    }
    $folder_path_bermasalah="data_ppwp/data_tps_bermasalah.json";
    $json_data = json_encode($data_tps);
    if (file_put_contents($folder_path_bermasalah, $json_data)) {
        echo "Data telah disimpan ke dalam file JSON.<br>";
        // var_dump($file_path_prov);
        // end();
    } else {
        echo "Gagal menyimpan data ke dalam file JSON.<br>";
        // var_dump($file_path_tps);
        end();
    }
    var_dump($json_data);
                        end();
} else {
    echo "Tidak ada file yang ditemukan.";
}

echo '<br>suara_bermasalah_anies: '.$suara_bermasalah_anies;
echo '<br>suara_bermasalah_prabowo: '.$suara_bermasalah_prabowo;
echo '<br>suara_bermasalah_ganjar: '.$suara_bermasalah_ganjar;


?>
