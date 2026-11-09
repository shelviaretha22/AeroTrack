<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KontrakReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected Collection $kontraks;

    protected int $nomor = 0;

    public function __construct(Collection $kontraks)
    {
        $this->kontraks = $kontraks;
    }

    /**
     * Mengambil data Kontrak yang sudah difilter.
     */
    public function collection()
    {
        return $this->kontraks;
    }

    /**
     * Membuat judul kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No.',
            'Nomor Kontrak',
            'Nomor Kerja Sama',
            'Mitra Usaha',
            'Brand',
            'Nama Tenant',
            'Jenis Usaha',
            'Terminal',
            'Lokasi',
            'Tanggal Pengajuan',
            'Tanggal Mulai',
            'Tanggal Berakhir',
            'Status Kontrak',
            'Status Perpanjangan',
        ];
    }

    /**
     * Memetakan data Kontrak ke kolom Excel.
     */
    public function map($kontrak): array
    {
        $this->nomor++;

        $kerjaSama = $kontrak->kerjaSama;
        $tenant = $kontrak->tenant;
        $lokasi = $kontrak->lokasi;

        $formatTanggal = function ($tanggal) {
            return $tanggal
                ? \Carbon\Carbon::parse($tanggal)->format('d/m/Y')
                : '-';
        };

        return [
            $this->nomor,
            $kontrak->nomor_kontrak ?: '-',
            $kerjaSama?->nomor_kerja_sama ?: '-',
            $kontrak->nama_mitra_snapshot
                ?: $kerjaSama?->mitra_usaha
                ?: '-',
            $kerjaSama?->brand ?: '-',
            $tenant?->nama_tenant ?: '-',
            $kerjaSama?->jenis_usaha ?: '-',
            $lokasi?->terminal ?: '-',
            $lokasi?->lokasi ?: '-',
            $formatTanggal($kontrak->tanggal_pengajuan),
            $formatTanggal($kerjaSama?->tanggal_mulai),
            $formatTanggal($kerjaSama?->tanggal_berakhir),
            $kontrak->status_laporan ?? $kontrak->status ?? '-',
            $kontrak->status_perpanjangan ?: 'Belum Dikonfirmasi',
        ];
    }

    /**
     * Mengatur tampilan header Excel.
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => [
                        'rgb' => '365F65',
                    ],
                ],
                'alignment' => [
                    'horizontal' => 'center',
                    'vertical' => 'center',
                ],
            ],
        ];
    }
}
