<?php
// PHP Script untuk memproses input URL bulk dan menganalisis elemen form
set_time_limit(90); // Tambah batas waktu eksekusi
// Aktifkan output buffering eksplisit dan nonaktifkan kompresi GZIP untuk memastikan flush bekerja
ob_implicit_flush(true);
ob_end_clean(); 
ignore_user_abort(true); // Lanjutkan eksekusi meskipun koneksi klien terputus

// Pastikan header dikirim sebelum flush
header("Content-Type: text/html; charset=UTF-8");

// Fungsi untuk menganalisis konten HTML dan mengekstrak nama input dari semua form
function analyze_html_for_inputs($html_content) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html_content);
    $forms_data = [];
    $forms = $dom->getElementsByTagName('form');
    
    if ($forms->length === 0) return false;

    foreach ($forms as $form_index => $form) {
        $input_names = [];
        $form_action = $form->hasAttribute('action') ? $form->getAttribute('action') : 'Tidak Ada Action (Mungkin Self-Submit)';
        $form_method = $form->hasAttribute('method') ? strtoupper($form->getAttribute('method')) : 'GET';
        $target_tags = ['input', 'textarea', 'select'];

        foreach ($target_tags as $tag_name) {
            foreach ($form->getElementsByTagName($tag_name) as $element) {
                if ($element->hasAttribute('name')) {
                    
                    $input_name = $element->getAttribute('name');
                    $input_type = $tag_name . ($tag_name === 'input' && $element->hasAttribute('type') ? " (".$element->getAttribute('type').")" : '');
                    
                    $marker = '';

                    // Ambil nilai huruf kecil untuk pengecekan non-sensitif huruf besar/kecil
                    $lower_name = strtolower($input_name);
                    $lower_type_attr = strtolower($element->hasAttribute('type') ? $element->getAttribute('type') : '');

                    // LOGIKA PENANDAAN BARU
                    
                    // Cek Password: jika type='password' ATAU name mengandung 'pass'
                    if ($lower_type_attr === 'password' || strpos($lower_name, 'pass') !== false) {
                        $marker = '🔑 PASSWORD';
                    }
                    
                    // Cek Captcha: jika name atau type mengandung 'captcha' atau 'recaptcha'
                    if (strpos($lower_name, 'captcha') !== false || strpos($lower_name, 'recaptcha') !== false || strpos($lower_type_attr, 'captcha') !== false) {
                        if ($marker) $marker .= ' | ';
                        $marker .= '🤖 CAPTCHA';
                    }
                    // AKHIR LOGIKA PENANDAAN BARU

                    $input_names[] = [
                        'name' => $input_name,
                        'type' => $input_type,
                        'marker' => $marker
                    ];
                }
            }
        }
        $forms_data[] = [
            'action' => $form_action,
            'method' => $form_method,
            'inputs' => array_unique($input_names, SORT_REGULAR)
        ];
    }
    return $forms_data;
}


$input_list = '';
$is_processing = false;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['url_list'])) {
    $input_list = $_POST['url_list'];
    $urls = array_filter(array_map('trim', explode("\n", $input_list)));
    $is_processing = !empty($urls);
}

// Inisialisasi penghitung untuk tombol filter
$counts = ['success' => 0, 'error' => 0, 'info' => 0, 'total' => 0];

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Advanced Cek Form dan Input Bulk (Realtime)</title>
    <style>
        /* Gaya CSS yang sudah ada */
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f4f7f6; color: #333; }
        .container { max-width: 900px; margin: auto; padding: 25px; background-color: #fff; border-radius: 10px; box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        h2 { color: #1e88e5; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px; }
        textarea { width: 100%; height: 180px; padding: 12px; margin-bottom: 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 6px; resize: vertical; font-size: 14px; }
        input[type="submit"] { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        input[type="submit"]:hover { background-color: #0056b3; }
        .results-section { margin-top: 30px; }
        #live-results { min-height: 50px; border: 1px solid #ddd; padding: 10px; border-radius: 4px; background-color: #f9f9f9; }
        .result-item { padding: 15px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .result-item.success { background-color: #e6fff0; border-color: #4CAF50; }
        .result-item.error { background-color: #ffe6e6; border-color: #f44336; }
        .result-item.info { background-color: #e6f7ff; border-color: #2196F3; }
        .url-input { font-weight: bold; color: #1e88e5; margin-bottom: 5px; }
        .status-msg { margin-top: 5px; font-size: 1em; }
        .form-detail { margin-top: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #fff; }
        .form-detail h5 { margin-top: 0; color: #555; }
        .input-list { list-style-type: none; padding-left: 0; }
        .input-list li { background-color: #f0f0f0; margin-bottom: 3px; padding: 5px 8px; border-radius: 3px; border: 1px dotted #e0e0e0; font-family: monospace; font-size: 0.9em; position: relative; }
        #loading-indicator { font-weight: bold; color: orange; display: none; }
        
        /* Gaya untuk Button Group */
        #filter-buttons { margin-bottom: 15px; display: none; }
        .filter-btn {
            padding: 8px 15px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;
            margin-right: 5px; background-color: #f0f0f0; transition: background-color 0.2s;
        }
        .filter-btn:hover { background-color: #e0e0e0; }
        .filter-btn.active { font-weight: bold; }
        #btn-total { background-color: #ccc; }
        #btn-success { background-color: #4CAF50; color: white; border-color: #4CAF50; }
        #btn-error { background-color: #f44336; color: white; border-color: #f44336; }
        #btn-info { background-color: #2196F3; color: white; border-color: #2196F3; }
        
        /* Gaya penanda sensitif */
        .marker {
            font-weight: bold;
            color: #fff;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 0.8em;
            margin-left: 10px;
        }
        .marker.password { background-color: #9c27b0; } /* Ungu untuk Password */
        .marker.captcha { background-color: #ff9800; } /* Oranye untuk Captcha */
    </style>
</head>
<body>
    <div class="container">
        <h2>Advanced Cek Form dan Input Bulk (Real-time)</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" onsubmit="document.getElementById('loading-indicator').style.display='block';">
            <label for="url_list">Masukkan Daftar URL atau Domain (satu per baris):</label>
            <textarea id="url_list" name="url_list" placeholder="example.com
https://anotherwebsite.org/login" required><?php echo htmlspecialchars($input_list); ?></textarea>
            <input type="submit" value="Mulai Cek Real-time">
        </form>

        <hr>

        <div class="results-section">
            <h3>Hasil Pengecekan <span id="loading-indicator">⏳ Sedang Memproses...</span></h3>
            
            <div id="filter-buttons">
                <span class="filter-btn active" id="btn-total" data-filter="all" onclick="filterResults('all')">Total (<span id="count-total">0</span>)</span>
                <span class="filter-btn" id="btn-success" data-filter="success" onclick="filterResults('success')">Sukses (<span id="count-success">0</span>)</span>
                <span class="filter-btn" id="btn-error" data-filter="error" onclick="filterResults('error')">Error (<span id="count-error">0</span>)</span>
                <span class="filter-btn" id="btn-info" data-filter="info" onclick="filterResults('info')">Info (<span id="count-info">0</span>)</span>
            </div>

            <div id="live-results">
            <?php 
            // =================================================================
            // BLOK PHP UNTUK PROSES REAL-TIME
            // =================================================================
            if ($is_processing) {
                $count = 0;
                $total_urls = count($urls);
                $counts = ['success' => 0, 'error' => 0, 'info' => 0, 'total' => 0];

                echo "<script>document.getElementById('filter-buttons').style.display='block';</script>";
                echo "<p style='margin-bottom: 15px;' class='info'>Memulai pengecekan $total_urls URL...</p>";
                flush();
                
                foreach ($urls as $url) {
                    $count++;
                    $counts['total']++;
                    
                    // Lakukan proses pengecekan untuk satu URL
                    $original_url = htmlspecialchars($url);
                    $processed_url = $url;
                    $final_url = '';
                    $form_data = null;
                    $status_message = '';
                    $http_code = 0;

                    if (!preg_match('#^https?://#i', $processed_url)) {
                        $processed_url = 'https://' . $processed_url;
                    }

                    if (!filter_var($processed_url, FILTER_VALIDATE_URL)) {
                        $status_message = "URL/Domain **TIDAK VALID**.";
                    } else {
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $processed_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (UrlChecker/1.0)');

                        $html_content = curl_exec($ch);
                        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $curl_error = curl_error($ch);
                        $final_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); 
                        curl_close($ch);

                        if ($html_content === false || $http_code >= 400) {
                            $status_message = "Gagal mengambil konten (HTTP Code: $http_code / Error: $curl_error).";
                        } else {
                            // Panggil fungsi yang telah dimodifikasi
                            $form_data = analyze_html_for_inputs($html_content);
                            
                            if ($form_data !== false) {
                                $status_message = "✅ **Ditemukan** " . count($form_data) . " form. (HTTP Code: $http_code)";
                            } else {
                                $status_message = "❌ **TIDAK Ditemukan** elemen `<form>`. (HTTP Code: $http_code)";
                            }
                        }
                    }
                    
                    // Tentukan class dan update counter
                    $class = 'info';
                    if ($form_data !== null && $form_data !== false) {
                        $class = 'success';
                        $counts['success']++;
                    } else if ($form_data === false || $http_code >= 400) {
                        $class = 'error';
                        $counts['error']++;
                    } else {
                        $counts['info']++;
                    }

                    // Update counter di frontend via JS saat ini juga
                    echo "<script>";
                    echo "document.getElementById('count-total').innerText = '{$counts['total']}';";
                    echo "document.getElementById('count-success').innerText = '{$counts['success']}';";
                    echo "document.getElementById('count-error').innerText = '{$counts['error']}';";
                    echo "document.getElementById('count-info').innerText = '{$counts['info']}';";
                    echo "</script>";

                    // Bangun HTML Output
                    $output_html = "
                    <div class='result-item $class' data-result-type='$class'> 
                        <div class='url-input'>#$count/$total_urls | Input: **$original_url**</div>";

                    if ($original_url !== htmlspecialchars($processed_url)) {
                        $output_html .= "<small style='color: #666;'>Diproses sebagai: " . htmlspecialchars($processed_url) . "</small>";
                    }

                    $is_redirected = !empty($final_url) && $final_url !== $processed_url;
                    if ($is_redirected) {
                        $output_html .= "<small style='color: #007bff; display: block; margin-top: 5px;'>
                            URL Terakhir (Efektif): **" . htmlspecialchars($final_url) . "**
                        </small>";
                    }
                    
                    $output_html .= "<div class='status-msg'>Status: $status_message</div>";

                    // Tampilkan detail form jika ditemukan
                    if (is_array($form_data)) {
                        foreach ($form_data as $index => $form) {
                            $output_html .= "
                            <div class='form-detail'>
                                <h5>Form #" . ($index + 1) . " (Method: **" . $form['method'] . "**)</h5>
                                <p style='font-size: 0.9em;'>**Action:** `" . htmlspecialchars($form['action']) . "`</p>";
                                
                            if (!empty($form['inputs'])) {
                                $output_html .= "<p><strong>Ditemukan Input Field (Name):</strong></p>
                                    <ul class='input-list'>";
                                foreach ($form['inputs'] as $input) {
                                    
                                    // LOGIKA OUTPUT PENANDA BARU DI SINI
                                    $marker_html = '';
                                    if ($input['marker']) {
                                        $parts = explode(' | ', $input['marker']);
                                        foreach ($parts as $part) {
                                            if (strpos($part, 'PASSWORD') !== false) {
                                                $marker_html .= "<span class='marker password'>🔑 PASSWORD</span>";
                                            } elseif (strpos($part, 'CAPTCHA') !== false) {
                                                $marker_html .= "<span class='marker captcha'>🤖 CAPTCHA</span>";
                                            }
                                        }
                                    }

                                    $output_html .= "
                                        <li>
                                            `" . htmlspecialchars($input['name']) . "` 
                                            <span style='color: #777;'>(Tipe: " . htmlspecialchars($input['type']) . ")</span>
                                            {$marker_html}
                                        </li>";
                                }
                                $output_html .= "</ul>";
                            } else {
                                $output_html .= "<p style='color: orange;'>Form ini tidak memiliki input field dengan atribut `name`.</p>";
                            }
                            $output_html .= "</div>";
                        }
                    }
                    $output_html .= "</div>";
                    
                    echo $output_html;
                    flush(); 
                }
                
                // Sinyal selesai
                echo "<script>document.getElementById('loading-indicator').style.display='none';</script>";
                flush();
            } else {
                echo "<p>Masukkan daftar URL di atas dan klik 'Mulai Cek Real-time' untuk memulai.</p>";
            }
            // =================================================================
            ?>
            </div>
        </div>
    </div>

    <script>
        // Fungsi JavaScript untuk memfilter hasil
        function filterResults(filterType) {
            const results = document.querySelectorAll('#live-results .result-item');
            const buttons = document.querySelectorAll('#filter-buttons .filter-btn');

            buttons.forEach(btn => btn.classList.remove('active'));
            document.querySelector(`[data-filter='${filterType}']`).classList.add('active');

            results.forEach(item => {
                const itemType = item.getAttribute('data-result-type');
                
                if (filterType === 'all' || itemType === filterType) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        filterResults('all');
    </script>
</body>
</html>