<?php
// PHP Script untuk memproses input URL bulk dan menganalisis elemen form

// Fungsi untuk menganalisis konten HTML dan mengekstrak nama input dari semua form
function analyze_html_for_inputs($html_content) {
    $dom = new DOMDocument();
    
    // @ digunakan untuk menekan peringatan (warning) dari HTML parsing yang tidak sempurna
    @$dom->loadHTML($html_content);
    
    $forms_data = [];
    
    // Cari semua elemen <form>
    $forms = $dom->getElementsByTagName('form');
    
    if ($forms->length === 0) {
        return false; // Tidak ada form yang ditemukan
    }

    foreach ($forms as $form_index => $form) {
        $input_names = [];
        
        // Atribut penting dari form
        $form_action = $form->hasAttribute('action') ? $form->getAttribute('action') : 'Tidak Ada Action (Mungkin Self-Submit)';
        $form_method = $form->hasAttribute('method') ? strtoupper($form->getAttribute('method')) : 'GET';
        
        // Elemen yang membawa data ke server: input, textarea, select
        $target_tags = ['input', 'textarea', 'select'];

        foreach ($target_tags as $tag_name) {
            foreach ($form->getElementsByTagName($tag_name) as $element) {
                if ($element->hasAttribute('name')) {
                    // Simpan nama input dan jenis tag
                    $input_names[] = [
                        'name' => $element->getAttribute('name'),
                        'type' => $tag_name . ($tag_name === 'input' && $element->hasAttribute('type') ? " (".$element->getAttribute('type').")" : '')
                    ];
                }
            }
        }

        // Simpan data form dan inputnya
        $forms_data[] = [
            'action' => $form_action,
            'method' => $form_method,
            'inputs' => array_unique($input_names, SORT_REGULAR) // Hapus duplikasi input
        ];
    }
    
    return $forms_data;
}


$input_list = '';
$results = [];

// Cek apakah formulir telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['url_list'])) {
    
    $input_list = $_POST['url_list'];
    
    // Pecah input list menjadi array, hapus baris kosong, dan bersihkan spasi
    $urls = array_filter(array_map('trim', explode("\n", $input_list)));

    if (!empty($urls)) {
        foreach ($urls as $url) {
            $original_url = htmlspecialchars($url);
            $processed_url = $url;
            $form_data = null;
            $status_message = '';
            $http_code = 0;

            // 1. Tambahkan protokol jika hanya domain yang diinput
            if (!preg_match('#^https?://#i', $processed_url)) {
                $processed_url = 'https://' . $processed_url;
            }

            // 2. Validasi URL
            if (!filter_var($processed_url, FILTER_VALIDATE_URL)) {
                $status_message = "URL/Domain **TIDAK VALID**.";
            } else {
                // 3. Ambil konten halaman menggunakan cURL
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $processed_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Timeout 15 detik
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Connection timeout 5 detik
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 5); // Batasi redirect
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (UrlChecker/1.0)');

                $html_content = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curl_error = curl_error($ch);
                curl_close($ch);

                if ($html_content === false || $http_code >= 400) {
                    $status_message = "Gagal mengambil konten (HTTP Code: $http_code / Error: $curl_error).";
                } else {
                    // 4. Analisis konten HTML untuk form dan input
                    $form_data = analyze_html_for_inputs($html_content);
                    
                    if ($form_data !== false) {
                        $status_message = "✅ **Ditemukan** " . count($form_data) . " form. (HTTP Code: $http_code)";
                    } else {
                        $status_message = "❌ **TIDAK Ditemukan** elemen `<form>`. (HTTP Code: $http_code)";
                    }
                }
            }
            
            // Simpan hasil ke array
            $results[] = [
                'original' => $original_url,
                'processed_url' => $processed_url,
                'status' => $status_message,
                'http_code' => $http_code,
                'form_data' => $form_data
            ];
        }
    } else {
        $results[] = [
            'original' => '',
            'status' => 'Silakan masukkan minimal satu URL atau domain.',
            'form_data' => null
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Advanced Cek Form dan Input Bulk</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 20px; background-color: #f4f7f6; color: #333; }
        .container { max-width: 900px; margin: auto; padding: 25px; background-color: #fff; border-radius: 10px; box-shadow: 0 6px 12px rgba(0,0,0,0.15); }
        h2 { color: #1e88e5; border-bottom: 2px solid #e0e0e0; padding-bottom: 10px; margin-bottom: 20px; }
        textarea { width: 100%; height: 180px; padding: 12px; margin-bottom: 15px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 6px; resize: vertical; font-size: 14px; }
        input[type="submit"] { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; transition: background-color 0.3s; }
        input[type="submit"]:hover { background-color: #0056b3; }
        .results-section { margin-top: 30px; }
        .result-item { padding: 15px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .result-item.success { background-color: #e6fff0; border-color: #4CAF50; }
        .result-item.error { background-color: #ffe6e6; border-color: #f44336; }
        .result-item.info { background-color: #e6f7ff; border-color: #2196F3; }
        .url-input { font-weight: bold; color: #1e88e5; margin-bottom: 5px; }
        .status-msg { margin-top: 5px; font-size: 1em; }
        .form-detail { margin-top: 10px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9; }
        .form-detail h5 { margin-top: 0; color: #555; }
        .input-list { list-style-type: none; padding-left: 0; }
        .input-list li { background-color: #fff; margin-bottom: 3px; padding: 5px 8px; border-radius: 3px; border: 1px dotted #e0e0e0; font-family: monospace; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Advanced Cek Form dan Input Bulk</h2>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="url_list">Masukkan Daftar URL atau Domain (satu per baris):</label>
            <textarea id="url_list" name="url_list" placeholder="example.com
https://anotherwebsite.org/login" required><?php echo $input_list; ?></textarea>
            <input type="submit" value="Cek Semua Halaman">
        </form>

        <hr>

        <div class="results-section">
            <h3>Hasil Pengecekan (Total Item: <?php echo count($results); ?>)</h3>
            <?php if (!empty($results)): ?>
                <?php foreach ($results as $item): 
                    $class = 'info';
                    if ($item['form_data'] !== null && $item['form_data'] !== false) $class = 'success';
                    else if ($item['form_data'] === false || $item['http_code'] >= 400) $class = 'error';
                ?>
                    <div class="result-item <?php echo $class; ?>">
                        <div class="url-input">Input: <?php echo $item['original']; ?></div>
                        <?php if ($item['original'] !== $item['processed_url']): ?>
                            <small style="color: #666;">Diproses sebagai: <?php echo $item['processed_url']; ?></small>
                        <?php endif; ?>
                        
                        <div class="status-msg">Status: <?php echo $item['status']; ?></div>

                        <?php 
                        // Tampilkan detail form jika ditemukan
                        if (is_array($item['form_data'])): ?>
                            <?php foreach ($item['form_data'] as $index => $form): ?>
                                <div class="form-detail">
                                    <h5>Form #<?php echo $index + 1; ?> (Method: **<?php echo $form['method']; ?>**)</h5>
                                    <p style="font-size: 0.9em;">**Action:** `<?php echo htmlspecialchars($form['action']); ?>`</p>
                                    
                                    <?php if (!empty($form['inputs'])): ?>
                                        <p><strong>Ditemukan Input Field (Name):</strong></p>
                                        <ul class="input-list">
                                            <?php foreach ($form['inputs'] as $input): ?>
                                                <li>
                                                    `<?php echo htmlspecialchars($input['name']); ?>` 
                                                    <span style="color: #777;">(Tipe: <?php echo htmlspecialchars($input['type']); ?>)</span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p style="color: orange;">Form ini tidak memiliki input field dengan atribut `name`.</p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="result-item info">
                    <p>Masukkan daftar URL atau domain di atas (satu per baris) untuk memulai pengecekan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>