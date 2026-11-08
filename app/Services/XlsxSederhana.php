<?php

namespace App\Services;

use RuntimeException;
use ZipArchive;

/**
 * Penulis file .xlsx minimal tanpa library tambahan (memakai ZipArchive
 * bawaan PHP). Cukup untuk laporan tabel: banyak sheet, baris judul tebal,
 * sel teks dan angka.
 *
 * Contoh:
 *   $path = XlsxSederhana::buat([
 *       'Okupansi' => [
 *           ['Terminal', 'Total'],   // baris pertama = judul kolom
 *           ['T1', 20],
 *       ],
 *   ]);
 */
class XlsxSederhana
{
    private const NS = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';

    /**
     * @param array<string, array<int, array<int, mixed>>> $sheets
     * @return string path file sementara (hapus setelah dikirim)
     */
    public static function buat(array $sheets): string
    {
        if (! class_exists(ZipArchive::class)) {
            throw new RuntimeException(
                'Ekstensi PHP "zip" belum aktif, sehingga file Excel tidak bisa dibuat.'
            );
        }

        if ($sheets === []) {
            throw new RuntimeException('Tidak ada data untuk diekspor.');
        }

        $path = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new ZipArchive();

        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('File Excel sementara tidak bisa dibuat.');
        }

        $nama = array_keys($sheets);
        $jumlah = count($nama);

        $zip->addFromString('[Content_Types].xml', self::contentTypes($jumlah));
        $zip->addFromString('_rels/.rels', self::relsUtama());
        $zip->addFromString('xl/workbook.xml', self::workbook($nama));
        $zip->addFromString('xl/_rels/workbook.xml.rels', self::relsWorkbook($jumlah));
        $zip->addFromString('xl/styles.xml', self::styles());

        foreach (array_values($sheets) as $i => $baris) {
            $zip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', self::sheet($baris));
        }

        $zip->close();

        return $path;
    }

    private static function header(): string
    {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . "\n";
    }

    private static function contentTypes(int $jumlah): string
    {
        $sheets = '';
        for ($i = 1; $i <= $jumlah; $i++) {
            $sheets .= '<Override PartName="/xl/worksheets/sheet' . $i . '.xml"'
                . ' ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        }

        return self::header()
            . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>'
            . $sheets
            . '</Types>';
    }

    private static function relsUtama(): string
    {
        return self::header()
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
            . '</Relationships>';
    }

    private static function workbook(array $nama): string
    {
        $sheets = '';
        foreach ($nama as $i => $n) {
            $sheets .= '<sheet name="' . self::esc(self::namaSheet((string) $n, $i + 1))
                . '" sheetId="' . ($i + 1) . '" r:id="rId' . ($i + 1) . '"/>';
        }

        return self::header()
            . '<workbook xmlns="' . self::NS . '"'
            . ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
            . '<sheets>' . $sheets . '</sheets></workbook>';
    }

    private static function relsWorkbook(int $jumlah): string
    {
        $rel = '';
        for ($i = 1; $i <= $jumlah; $i++) {
            $rel .= '<Relationship Id="rId' . $i . '"'
                . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"'
                . ' Target="worksheets/sheet' . $i . '.xml"/>';
        }
        $rel .= '<Relationship Id="rId' . ($jumlah + 1) . '"'
            . ' Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"'
            . ' Target="styles.xml"/>';

        return self::header()
            . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
            . $rel . '</Relationships>';
    }

    private static function styles(): string
    {
        return self::header()
            . '<styleSheet xmlns="' . self::NS . '">'
            . '<fonts count="2">'
            . '<font><sz val="11"/><name val="Calibri"/></font>'
            . '<font><b/><sz val="11"/><name val="Calibri"/></font>'
            . '</fonts>'
            . '<fills count="2">'
            . '<fill><patternFill patternType="none"/></fill>'
            . '<fill><patternFill patternType="gray125"/></fill>'
            . '</fills>'
            . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
            . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
            . '<cellXfs count="2">'
            . '<xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/>'
            . '<xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/>'
            . '</cellXfs>'
            . '</styleSheet>';
    }

    private static function sheet(array $baris): string
    {
        $xml = '';

        foreach (array_values($baris) as $r => $sel) {
            $nomorBaris = $r + 1;
            $xml .= '<row r="' . $nomorBaris . '">';

            foreach (array_values($sel) as $c => $nilai) {
                $ref = self::kolom($c) . $nomorBaris;
                $gaya = $r === 0 ? ' s="1"' : '';

                if (is_int($nilai) || is_float($nilai)) {
                    $xml .= '<c r="' . $ref . '"' . $gaya . '><v>' . $nilai . '</v></c>';
                } elseif ($nilai === null || $nilai === '') {
                    $xml .= '<c r="' . $ref . '"' . $gaya . '/>';
                } else {
                    $xml .= '<c r="' . $ref . '" t="inlineStr"' . $gaya . '>'
                        . '<is><t xml:space="preserve">' . self::esc((string) $nilai) . '</t></is></c>';
                }
            }

            $xml .= '</row>';
        }

        return self::header()
            . '<worksheet xmlns="' . self::NS . '"><sheetData>' . $xml . '</sheetData></worksheet>';
    }

    /** 0 → A, 25 → Z, 26 → AA. */
    private static function kolom(int $indeks): string
    {
        $huruf = '';
        $indeks++;

        while ($indeks > 0) {
            $sisa = ($indeks - 1) % 26;
            $huruf = chr(65 + $sisa) . $huruf;
            $indeks = intdiv($indeks - 1, 26);
        }

        return $huruf;
    }

    /** Nama sheet Excel: maksimal 31 karakter dan tanpa karakter terlarang. */
    private static function namaSheet(string $nama, int $urutan): string
    {
        $nama = trim(str_replace(['\\', '/', '?', '*', '[', ']', ':'], ' ', $nama));
        $nama = mb_substr($nama, 0, 31);

        return $nama !== '' ? $nama : 'Sheet' . $urutan;
    }

    private static function esc(string $teks): string
    {
        // Buang karakter kontrol yang tidak sah di XML 1.0.
        $teks = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $teks) ?? '';

        return htmlspecialchars($teks, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
