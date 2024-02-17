
document.addEventListener("DOMContentLoaded", function() {
    // Ambil data JSON dari URL
    fetch("https://sirekap-obj-data.kpu.go.id/pemilu/hhcw/ppwp.json")
        .then(response => response.json())
        .then(data => {
            // Panggil fungsi untuk membuat grafik setelah menerima data
            drawChart(data.chart);
        })
        .catch(error => console.error('Error fetching JSON:', error));

    // Fungsi untuk membuat grafik Donat menggunakan Google Charts
    function drawChart(chartData) {
        // Objek untuk menyimpan kunci yang diubah
        var renamedKeys = {
            "100025": "Anies",
            "100026": "Prabowo",
            "100027": "Ganjar"
        };

        // Buat objek baru dengan kunci yang diubah
        var newData = {};
        for (var key in chartData) {
            if (chartData.hasOwnProperty(key) && key !== "persen") {
                newData[renamedKeys[key]] = chartData[key];
            }
        }

        // Load library Google Charts
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(function() {
            // Buat data table dari objek yang dibuat sebelumnya
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'ID');
            data.addColumn('number', 'Suara');
            data.addRows(Object.entries(newData));

            // Konfigurasi opsi grafik
            var options = {
                title: 'Suara Pemilu (' + chartData['persen'] + '%)',
                pieHole: 0.3 // Mengatur lebar donat
            };

            // Buat instance grafik Donat
            var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        });
    }
});