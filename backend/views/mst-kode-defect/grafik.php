<?php
use yii\helpers\Json;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $defectPerMonth array */

$this->title = 'Grafik Defect per Tahun '.date('Y');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile('https://cdn.jsdelivr.net/npm/chart.js', ['position' => View::POS_HEAD]);

?>

<h1><?= $this->title ?></h1>

<canvas id="myChart" width="400" height="200"></canvas>
<!-- Memastikan Chart.js dan plugin Chart.js DataLabels dimuat terlebih dahulu -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0/dist/chartjs-plugin-datalabels.min.js">
</script>

<script>
// Fungsi untuk menghasilkan warna acak
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

var ctx = document.getElementById('myChart').getContext('2d');
var defectPerMonth = <?= Json::encode($defectPerMonth) ?>; // Data defect per bulan

// Array nama bulan
var monthNames = [
    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];

// Menyusun bulan, no_urut, dan jumlah defect per no_urut
var months = [];
var defects = {}; // Menyusun data berdasarkan no_urut

defectPerMonth.forEach(function(item) {
    // Menyusun bulan berdasarkan nama bulan
    if (!months.includes(monthNames[item.month - 1])) {
        months.push(monthNames[item.month - 1]);
    }

    // Menyusun data defect berdasarkan no_urut
    if (!defects[item.no_urut]) {
        defects[item.no_urut] = {
            nama_defect: item.nama_defect,
            counts: new Array(12).fill(0) // Inisialisasi array untuk 12 bulan
        };
    }

    // Menyimpan count untuk setiap no_urut dan bulan
    defects[item.no_urut].counts[item.month - 1] = item.count;
});

// Membuat dataset untuk setiap no_urut dengan warna unik
var datasets = [];
var usedColors = [];

for (var noUrut in defects) {
    var color = getRandomColor();

    // Pastikan warna yang dihasilkan tidak duplikat
    while (usedColors.includes(color)) {
        color = getRandomColor();
    }

    // Menambahkan warna ke daftar yang telah digunakan
    usedColors.push(color);

    datasets.push({
        label: noUrut + ' - ' + defects[noUrut]
        .nama_defect, // Menggunakan nama defect sebagai label (tetap digunakan untuk keperluan legenda)
        data: defects[noUrut].counts, // Data berdasarkan no_urut
        backgroundColor: color + '80', // Menggunakan warna yang unik (dengan transparansi)
        borderColor: color, // Warna border lebih terang
        borderWidth: 1
    });
}

// Membuat chart
var chart = new Chart(ctx, {
    type: 'bar', // Tipe grafik (bar, line, pie, dll)
    data: {
        labels: months, // Label berdasarkan nama bulan
        datasets: datasets // Dataset berdasarkan no_urut
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true, // Menampilkan legenda
                position: 'top' // Posisi legenda di atas grafik
            },
            datalabels: {
                display: true, // Menampilkan datalabel
                color: 'black', // Warna label
                font: {
                    weight: 'bold', // Menebalkan font
                    size: 12 // Ukuran font
                },
                formatter: function(value, context) {
                    // Jika tidak ada data untuk bulan ini, tidak tampilkan label
                    if (value === 0) {
                        return ''; // Tidak tampilkan label untuk bar yang tidak ada data
                    }

                    // Format untuk menampilkan no_urut di atas setiap bar
                    return context.dataset.label.split(' - ')[0]; // Menggunakan no_urut dari label dataset
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    },
    plugins: [ChartDataLabels] // Pastikan plugin datalabels diaktifkan
});
</script>