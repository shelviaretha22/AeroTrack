
import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

function inisialisasiGrafikKontrak() {
    const canvasStatus = document.getElementById('grafikStatusKontrak');
    const canvasPerpanjangan = document.getElementById('grafikPerpanjanganKontrak');
    const canvasTahunan = document.getElementById('grafikKontrakTahunan');

    if (!canvasStatus || !canvasPerpanjangan || !canvasTahunan) return;

    // Hapus grafik lama jika inisialisasi berjalan kembali
    if (Chart.getChart(canvasStatus)) {
        Chart.getChart(canvasStatus).destroy();
    }

    if (Chart.getChart(canvasPerpanjangan)) {
        Chart.getChart(canvasPerpanjangan).destroy();
    }

    if (Chart.getChart(canvasTahunan)) {
        Chart.getChart(canvasTahunan).destroy();
    }

    const dataStatus = window.dataStatusKontrak || {};
    const dataPerpanjangan = window.dataPerpanjanganKontrak || {};
    const dataTahunan = window.dataKontrakTahunan || {};

    // Grafik komposisi status kontrak
    new Chart(canvasStatus, {
        type: 'doughnut',
        data: {
            labels: Object.keys(dataStatus),
            datasets: [{
                data: Object.values(dataStatus),
                backgroundColor: ['#087F73', '#A47720', '#91A9A5', '#527D82'],
                borderColor: '#FFFFFF',
                borderWidth: 3,
                hoverOffset: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 20
                    }
                }
            }
        }
    });

    // Grafik status perpanjangan kontrak
    new Chart(canvasPerpanjangan, {
        type: 'doughnut',
        data: {
            labels: Object.keys(dataPerpanjangan),
            datasets: [{
                data: Object.values(dataPerpanjangan),
                backgroundColor: ['#087F73', '#C45B50', '#91A9A5'],
                borderColor: '#FFFFFF',
                borderWidth: 3,
                hoverOffset: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '66%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        padding: 12
                    }
                }
            }
        }
    });

    // Grafik jumlah kontrak per tahun
    new Chart(canvasTahunan, {
        type: 'bar',
        data: {
            labels: Object.keys(dataTahunan),
            datasets: [{
                label: 'Jumlah Kontrak',
                data: Object.values(dataTahunan),
                backgroundColor: '#527D82',
                borderRadius: 7,
                maxBarThickness: 48
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Jalankan inisialisasi grafik setelah halaman dimuat
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inisialisasiGrafikKontrak);
} else {
    inisialisasiGrafikKontrak();
}
