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

$suara_kpu_anies = 0;
$suara_kpu_prabowo = 0;
$suara_kpu_ganjar = 0;

$suara_ulang_anies = 0;
$suara_ulang_prabowo = 0;
$suara_ulang_ganjar = 0;

$suara_bermasalah_anies = 0;
$suara_bermasalah_prabowo = 0;
$suara_bermasalah_ganjar = 0;
$total_tps = 0;
$total_tps_kpu = 0;
$total_tps_ulang = 0;
$total_tps_bermasalah = 0;

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
                    if($anies+$prabowo+$ganjar<300){
                        $suara_ulang_anies = $suara_ulang_anies +$anies;
                        $suara_ulang_prabowo = $suara_ulang_prabowo +$prabowo;
                        $suara_ulang_ganjar = $suara_ulang_ganjar +$ganjar;
                        
                        $total_tps_ulang = $total_tps_ulang + 1;
                    }
                    if($anies+$prabowo+$ganjar>300){
                        $suara_bermasalah_anies = $suara_bermasalah_anies +$anies;
                        $suara_bermasalah_prabowo = $suara_bermasalah_prabowo +$prabowo;
                        $suara_bermasalah_ganjar = $suara_bermasalah_ganjar +$ganjar;

                        $total_tps_bermasalah = $total_tps_bermasalah + 1;
                    }
                        $suara_kpu_anies = $suara_kpu_anies +$anies;
                        $suara_kpu_prabowo = $suara_kpu_prabowo +$prabowo;
                        $suara_kpu_ganjar = $suara_kpu_ganjar +$ganjar;
                        $total_tps_kpu = $total_tps_kpu + 1;
                    }
                    
                }
    }
} else {
    echo "Tidak ada file yang ditemukan.";
}
$data_tps['suara_kpu']['anies'] = $suara_kpu_anies;
$data_tps['suara_kpu']['prabowo'] = $suara_kpu_prabowo;
$data_tps['suara_kpu']['ganjar'] = $suara_kpu_ganjar;

$data_tps['suara_hitung_ulang']['anies'] = $suara_ulang_anies;
$data_tps['suara_hitung_ulang']['prabowo'] = $suara_ulang_prabowo;
$data_tps['suara_hitung_ulang']['ganjar'] = $suara_ulang_ganjar;

$data_tps['suara_bermasalah']['anies'] = $suara_bermasalah_anies;
$data_tps['suara_bermasalah']['prabowo'] = $suara_bermasalah_prabowo;
$data_tps['suara_bermasalah']['ganjar'] = $suara_bermasalah_ganjar;

$data_tps['total_tps_kpu'] = $total_tps_kpu;
$data_tps['total_tps_ulang'] = $total_tps_ulang;
$data_tps['total_tps_bermasalah'] = $total_tps_bermasalah;

echo "<br>total_tps: ".$total_tps;
echo "<br>total_tps_kpu : ".$total_tps_kpu;
echo "<br>total_tps_ulang : ".$total_tps_ulang;
echo "<br>total_tps_bermasalah : ".$total_tps_bermasalah;

$folder_path_bermasalah="data_ppwp/hitung_ulang.json";
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

echo '<br>suara_kpu_anies: '.$suara_kpu_anies;
echo '<br>suara_kpu_prabowo: '.$suara_kpu_prabowo;
echo '<br>suara_kpu_ganjar: '.$suara_kpu_ganjar;

echo '<br>suara_ulang_anies: '.$suara_ulang_anies;
echo '<br>suara_ulang_prabowo: '.$suara_ulang_prabowo;
echo '<br>suara_ulang_ganjar: '.$suara_ulang_ganjar;

echo '<br>suara_bermasalah_anies: '.$suara_bermasalah_anies;
echo '<br>suara_bermasalah_prabowo: '.$suara_bermasalah_prabowo;
echo '<br>suara_bermasalah_ganjar: '.$suara_bermasalah_ganjar;


?>
