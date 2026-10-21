
import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.start();

function inisialisasiGrafikKontrak() {
    const canvasStatus = document.getElementById('grafikStatusKontrak');
    const canvasTahunan = document.getElementById('grafikKontrakTahunan');

    if (!canvasStatus || !canvasTahunan) return;

    // Hapus grafik lama jika inisialisasi berjalan kembali
    if (Chart.getChart(canvasStatus)) {
        Chart.getChart(canvasStatus).destroy();
    }

    if (Chart.getChart(canvasTahunan)) {
        Chart.getChart(canvasTahunan).destroy();
    }

    const dataStatus = window.dataStatusKontrak || {};
    const dataTahunan = window.dataKontrakTahunan || {};

    // Grafik status kontrak
    new Chart(canvasStatus, {
        type: 'doughnut',
        data: {
            labels: Object.keys(dataStatus),
            datasets: [{
                data: Object.values(dataStatus),
                backgroundColor: ['#087F73', '#A47720', '#91A9A5'],
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

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inisialisasiGrafikKontrak);
} else {
    inisialisasiGrafikKontrak();
}
