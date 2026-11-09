<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantReportExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected Collection $tenants;

    protected int $nomor = 0;

    public function __construct(Collection $tenants)
    {
        $this->tenants = $tenants;
    }

    /**
     * Mengambil data Tenant yang sudah difilter.
     */
    public function collection()
    {
        return $this->tenants;
    }

    /**
     * Membuat judul kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No.',
            'Nama Tenant',
            'Mitra Usaha',
            'Brand',
            'Jenis Usaha',
            'Terminal',
            'Lokasi',
            'Tanggal Pengajuan',
            'Status Kerja Sama',
            'Status Aktivasi',
        ];
    }

    /**
     * Memetakan data Tenant ke kolom Excel.
     */
    public function map($tenant): array
    {
        $this->nomor++;

        $kerjaSama = $tenant->kerjaSama;
        $lokasi = $kerjaSama?->lokasi;

        $tanggalPengajuan = $kerjaSama?->tanggal_pengajuan;

        return [
            $this->nomor,
            $tenant->nama_tenant ?: '-',
            $tenant->mitra_usaha ?: '-',
            $kerjaSama?->brand ?: '-',
            $tenant->jenis_usaha ?: '-',
            $lokasi?->terminal ?: '-',
            $lokasi?->lokasi ?: '-',
            $tanggalPengajuan
                ? $tanggalPengajuan->format('d/m/Y')
                : '-',
            $kerjaSama?->status ?: '-',
            $tenant->status_aktivasi_laporan ?: '-',
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
