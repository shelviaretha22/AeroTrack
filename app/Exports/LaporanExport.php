<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanExport implements FromArray, ShouldAutoSize, WithStyles, WithTitle
{
    protected array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function title(): string
    {
        return 'Laporan AeroTrack';
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = ['LAPORAN AEROTRACK'];
        $rows[] = ['Periode Tahun', $this->data['tahun']];
        $rows[] = [];

        // RINGKASAN KONTRAK
        $rows[] = ['RINGKASAN KONTRAK'];
        $rows[] = ['Total Kontrak', $this->data['totalKontrak']];
        $rows[] = ['Kontrak Aktif', $this->data['kontrakAktif']];
        $rows[] = ['Kontrak Nonaktif', $this->data['kontrakNonaktif']];
        $rows[] = ['Kontrak Proses', $this->data['kontrakProses']];
        $rows[] = [];

        // AKTIVASI
        $rows[] = ['RINGKASAN AKTIVASI'];
        $rows[] = ['Total Kerja Sama', $this->data['totalKerjaSama']];
        $rows[] = ['Aktivasi Disetujui', $this->data['totalSistemAktif']];
        $rows[] = [
            'Progress Aktivasi (%)',
            $this->data['progressAktivasi'],
        ];
        $rows[] = [];

        // TERMINAL
        $rows[] = ['PROGRESS AKTIVASI PER TERMINAL'];
        $rows[] = ['Terminal', 'Total Kerja Sama', 'Aktif', 'Persentase (%)'];

        foreach (['T1', 'T2'] as $terminal) {
            $item = $this->data['progressTerminal'][$terminal] ?? [];

            $rows[] = [
                $terminal,
                $item['total'] ?? 0,
                $item['aktif'] ?? 0,
                $item['persentase'] ?? 0,
            ];
        }

        $rows[] = [];

        // PERIODE KONTRAK
        $rows[] = ['REKAPITULASI PERIODE KONTRAK'];
        $rows[] = ['Tidak Memperpanjang', $this->data['kontrakLintas']];
        $rows[] = ['Ingin Memperpanjang', $this->data['kontrakPerpanjangan']];
        $rows[] = [];

        // DOKUMEN
        $rows[] = ['KELENGKAPAN DOKUMEN'];
        $rows[] = [
            'Total Dokumen Kontrak',
            $this->data['totalDokumenKontrak'],
        ];
        $rows[] = [
            'Dokumen Kontrak Lengkap',
            $this->data['dokumenKontrakLengkap'],
        ];
        $rows[] = [
            'Dokumen Kontrak Belum Lengkap',
            $this->data['dokumenKontrakBelumLengkap'],
        ];
        $rows[] = ['Total Berita Acara', $this->data['totalBA']];
        $rows[] = ['Berita Acara Lengkap', $this->data['baLengkap']];
        $rows[] = [
            'Berita Acara Belum Lengkap',
            $this->data['baBelumLengkap'],
        ];
        $rows[] = [];

        // TABEL LAPORAN
        $rows[] = ['DAFTAR LAPORAN KERJA SAMA'];
        $rows[] = [
            'No.',
            'No. Kerja Sama',
            'Mitra Usaha',
            'Brand',
            'Jenis Usaha',
            'Terminal',
            'Kode Ruang',
            'No. Kontrak Mitra',
            'Status Kontrak',
            'Kelengkapan Dokumen',
        ];

        foreach ($this->data['laporan'] as $index => $item) {
            $ks = $item['kerja_sama'];
            $lokasi = $item['lokasi'];
            $kontrak = $item['kontrak'];

            $rows[] = [
                $index + 1,
                $ks->nomor_kerja_sama ?? '-',
                $ks->mitra_usaha ?? '-',
                $ks->brand ?? '-',
                $ks->jenis_usaha ?? '-',
                $lokasi?->terminal ?? '-',
                $lokasi?->kode_ruang ?? '-',
                $kontrak?->nomor_kontrak_mitra ?? '-',
                $item['status_laporan'],
                $item['dokumen_lengkap'] ? 'Lengkap' : 'Belum Lengkap',
            ];
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
{
    $lastRow = $sheet->getHighestRow();

    $sectionTitles = [
        'RINGKASAN KONTRAK',
        'RINGKASAN AKTIVASI',
        'PROGRESS AKTIVASI PER TERMINAL',
        'REKAPITULASI PERIODE KONTRAK',
        'KELENGKAPAN DOKUMEN',
        'DAFTAR LAPORAN KERJA SAMA',
    ];

    for ($row = 1; $row <= $lastRow; $row++) {
        $value = $sheet->getCell('A' . $row)->getValue();

        if (in_array($value, $sectionTitles, true)) {
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '365F65'],
                ],
            ]);
        }

        if ($value === 'No.') {
            $sheet->getStyle('A' . $row . ':J' . $row)->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '527D82'],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ]);
        }
    }

    return [
        1 => [
            'font' => [
                'bold' => true,
                'size' => 16,
                'color' => ['rgb' => '365F65'],
            ],
        ],
    ];
}
}