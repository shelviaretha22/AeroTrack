<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\Element\AbstractContainer;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Style\Table as TableStyle;

/**
 * Pengolah dokumen Berita Acara (HTML hasil editor).
 *
 *  - bersihkanHtml(): membersihkan HTML dari editor sebelum disimpan.
 *  - buatWord():      mengubah HTML BA menjadi dokumen Word (.docx).
 */
class BeritaAcaraDokumen
{
    /** Lebar area tulis A4 dalam twips (11906 - margin kiri/kanan). */
    private const LEBAR_HALAMAN = 9306;

    private const TAG_DIIZINKAN = [
        'p', 'br', 'div', 'hr',
        'strong', 'b', 'em', 'i', 'u',
        'h1', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li',
        'table', 'thead', 'tbody', 'tr', 'td', 'th',
    ];

    private const TAG_DIBUANG = [
        'script', 'style', 'iframe', 'object', 'embed',
        'svg', 'form', 'link', 'meta', 'base', 'img',
        'input', 'button', 'textarea', 'select',
    ];

    private const TAG_BLOK = [
        'p', 'div', 'h1', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li', 'table', 'hr',
    ];

    // =====================================================================
    // PEMBERSIH HTML
    // =====================================================================

    /**
     * Membersihkan HTML dari elemen dan atribut berbahaya.
     * Atribut yang aman (alignment, lebar kolom, border tabel, dsb.)
     * dipertahankan agar format dokumen tidak hilang saat disimpan.
     */
    public static function bersihkanHtml(string $html): string
    {
        $dom = self::muatDom($html);
        $wadah = $dom->getElementsByTagName('div')->item(0);

        if (!$wadah) {
            return '';
        }

        self::bersihkanNode($wadah);

        $hasil = '';

        foreach ($wadah->childNodes as $anak) {
            $hasil .= $dom->saveHTML($anak);
        }

        // br dibuat XHTML agar aman dipakai kembali di DomPDF.
        return preg_replace('/<br\s*\/?>/i', '<br/>', $hasil) ?? $hasil;
    }

    private static function bersihkanNode(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $anak) {
            // Komentar, CDATA, dan processing instruction dibuang.
            if (!in_array($anak->nodeType, [XML_ELEMENT_NODE, XML_TEXT_NODE], true)) {
                $node->removeChild($anak);
                continue;
            }

            if ($anak->nodeType === XML_TEXT_NODE) {
                continue;
            }

            /** @var DOMElement $anak */
            $tag = strtolower($anak->nodeName);

            if (in_array($tag, self::TAG_DIBUANG, true)) {
                $node->removeChild($anak);
                continue;
            }

            self::bersihkanNode($anak);

            // Tag yang tidak diizinkan dibuang, teks di dalamnya dipertahankan.
            if (!in_array($tag, self::TAG_DIIZINKAN, true)) {
                while ($anak->firstChild) {
                    $node->insertBefore($anak->firstChild, $anak);
                }

                $node->removeChild($anak);
                continue;
            }

            self::bersihkanAtribut($anak, $tag);
        }
    }

    private static function bersihkanAtribut(DOMElement $el, string $tag): void
    {
        $aman = [];

        foreach (iterator_to_array($el->attributes) as $atribut) {
            $nama = strtolower($atribut->nodeName);
            $nilai = trim($atribut->nodeValue ?? '');

            if ($nama === 'style') {
                $style = self::bersihkanStyle($nilai);

                if ($style !== '') {
                    $aman['style'] = $style;
                }

                continue;
            }

            if (
                in_array($tag, ['td', 'th'], true)
                && in_array($nama, ['colspan', 'rowspan'], true)
                && preg_match('/^\d{1,2}$/', $nilai)
            ) {
                $aman[$nama] = $nilai;
                continue;
            }

            if (
                $tag === 'table'
                && in_array($nama, ['border', 'cellspacing', 'cellpadding'], true)
                && preg_match('/^\d{1,2}$/', $nilai)
            ) {
                $aman[$nama] = $nilai;
                continue;
            }

            if ($tag === 'ol' && $nama === 'type' && preg_match('/^[1aAiI]$/', $nilai)) {
                $aman[$nama] = $nilai;
                continue;
            }

            if ($tag === 'ol' && $nama === 'start' && preg_match('/^\d{1,3}$/', $nilai)) {
                $aman[$nama] = $nilai;
            }
        }

        while ($el->attributes->length > 0) {
            $el->removeAttributeNode($el->attributes->item(0));
        }

        foreach ($aman as $nama => $nilai) {
            $el->setAttribute($nama, $nilai);
        }
    }

    /**
     * Hanya properti CSS yang aman dan relevan untuk dokumen yang
     * dipertahankan. Nilai harus lolos pola ketat (tanpa url(), dll).
     */
    private static function bersihkanStyle(string $style): string
    {
        $aturan = [
            'text-align'         => '/^(left|right|center|justify)$/i',
            'font-weight'        => '/^(normal|bold|[1-9]00)$/i',
            'font-style'         => '/^(normal|italic)$/i',
            'text-decoration'    => '/^(none|underline|line-through)$/i',
            'vertical-align'     => '/^(top|middle|bottom)$/i',
            'width'              => '/^\d{1,3}(\.\d+)?(%|px|pt)?$/i',
            'height'             => '/^\d{1,4}(\.\d+)?(px|pt)?$/i',
            'page-break-before'  => '/^(always|auto)$/i',
            'border-collapse'    => '/^(collapse|separate)$/i',
        ];

        $hasil = [];

        foreach (explode(';', $style) as $deklarasi) {
            if (!str_contains($deklarasi, ':')) {
                continue;
            }

            [$properti, $nilai] = array_map('trim', explode(':', $deklarasi, 2));
            $properti = strtolower($properti);

            if (isset($aturan[$properti]) && preg_match($aturan[$properti], $nilai)) {
                $hasil[$properti] = $nilai;
            }
        }

        $teks = [];

        foreach ($hasil as $properti => $nilai) {
            $teks[] = $properti . ': ' . $nilai;
        }

        return implode('; ', $teks);
    }

    /**
     * Memuat HTML ke DOM. Seluruh konten dibungkus <div> agar
     * teks di luar tag tidak ikut dibuang parser.
     */
    private static function muatDom(string $html): DOMDocument
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $sebelumnya = libxml_use_internal_errors(true);

        $dom->loadHTML(
            '<?xml encoding="UTF-8"><div>' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );

        libxml_clear_errors();
        libxml_use_internal_errors($sebelumnya);

        return $dom;
    }

    // =====================================================================
    // KONVERSI KE WORD
    // =====================================================================

    public static function buatWord(string $html): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10.5);

        $section = $phpWord->addSection([
            'paperSize'    => 'A4',
            'marginTop'    => 1200,
            'marginBottom' => 1200,
            'marginLeft'   => 1300,
            'marginRight'  => 1300,
        ]);

        $dom = self::muatDom($html);
        $wadah = $dom->getElementsByTagName('div')->item(0);

        if (!$wadah) {
            throw new \RuntimeException('Konten BA tidak dapat dibaca.');
        }

        self::prosesAnak($section, $wadah, self::konteksAwal());

        return $phpWord;
    }

    private static function konteksAwal(): array
    {
        return [
            'align'       => null,   // left|center|right|both
            'left'        => 0,      // indentasi kiri (twips)
            'spaceAfter'  => 100,    // jarak setelah paragraf (twips)
            'marker'      => null,   // \stdClass berisi teks penanda daftar
            'lebar'       => self::LEBAR_HALAMAN,
            'font'        => [],
        ];
    }

    /**
     * Memproses anak-anak sebuah elemen. Elemen inline dikumpulkan
     * menjadi satu paragraf, elemen blok diproses terpisah.
     */
    private static function prosesAnak(AbstractContainer $parent, DOMNode $node, array $ctx): void
    {
        $inline = [];

        $siram = function () use (&$inline, $parent, $ctx) {
            if ($inline) {
                self::buatParagraf($parent, $inline, $ctx);
                $inline = [];
            }
        };

        foreach ($node->childNodes as $anak) {
            if (self::apakahBlok($anak)) {
                $siram();
                self::prosesBlok($parent, $anak, $ctx);
            } else {
                $inline[] = $anak;
            }
        }

        $siram();
    }

    private static function apakahBlok(DOMNode $node): bool
    {
        return $node->nodeType === XML_ELEMENT_NODE
            && in_array(strtolower($node->nodeName), self::TAG_BLOK, true);
    }

    private static function prosesBlok(AbstractContainer $parent, DOMElement $el, array $ctx): void
    {
        $tag = strtolower($el->nodeName);
        $style = self::bacaStyle($el);

        $ctx['align'] = self::petaAlign($style['text-align'] ?? null) ?? $ctx['align'];

        switch ($tag) {
            case 'table':
                self::buatTabel($parent, $el, $ctx);
                return;

            case 'ul':
            case 'ol':
                self::buatDaftar($parent, $el, $ctx);
                return;

            case 'hr':
                return;

            case 'h1':
            case 'h2':
            case 'h3':
            case 'h4':
                $ukuran = ['h1' => 16, 'h2' => 14, 'h3' => 12, 'h4' => 11][$tag];
                $ctx['font'] = array_merge($ctx['font'], ['bold' => true, 'size' => $ukuran]);
                $ctx['spaceBefore'] = 120;
                self::prosesAnak($parent, $el, $ctx);
                return;

            case 'li':
                // li di luar ul/ol diperlakukan sebagai paragraf biasa.
                self::prosesAnak($parent, $el, $ctx);
                return;

            default: // p, div
                if (
                    isset($style['page-break-before'])
                    && strtolower($style['page-break-before']) === 'always'
                    && $parent instanceof \PhpOffice\PhpWord\Element\Section
                ) {
                    $parent->addPageBreak();
                }

                self::prosesAnak($parent, $el, $ctx);
        }
    }

    // ---------------------------------------------------------------------
    // Paragraf dan teks inline
    // ---------------------------------------------------------------------

    private static function buatParagraf(AbstractContainer $parent, array $nodes, array $ctx): void
    {
        $segmen = [];

        foreach ($nodes as $node) {
            self::kumpulkanSegmen($node, $ctx['font'], $segmen);
        }

        // Rapikan spasi di awal dan akhir baris.
        $awalBaris = true;

        foreach ($segmen as $i => &$s) {
            if ($s['br']) {
                $awalBaris = true;
                continue;
            }

            if ($awalBaris) {
                $s['teks'] = ltrim($s['teks']);
            }

            if ($s['teks'] !== '') {
                $awalBaris = false;
            }
        }
        unset($s);

        for ($i = count($segmen) - 1; $i >= 0; $i--) {
            if ($segmen[$i]['br']) {
                continue;
            }

            $segmen[$i]['teks'] = rtrim($segmen[$i]['teks']);

            if ($segmen[$i]['teks'] !== '') {
                break;
            }
        }

        $segmen = array_values(array_filter(
            $segmen,
            fn ($s) => $s['br'] || $s['teks'] !== ''
        ));

        if (!$segmen) {
            return;
        }

        $gayaParagraf = [
            'spaceAfter' => $ctx['spaceAfter'],
        ];

        if (!empty($ctx['spaceBefore'])) {
            $gayaParagraf['spaceBefore'] = $ctx['spaceBefore'];
        }

        if ($ctx['align']) {
            $gayaParagraf['alignment'] = $ctx['align'];
        }

        $marker = $ctx['marker'] ?? null;
        $adaMarker = $marker && $marker->teks !== null;

        if ($ctx['left'] || $adaMarker) {
            $indentasi = ['left' => $ctx['left']];

            if ($adaMarker) {
                $indentasi['hanging'] = 360;
            }

            $gayaParagraf['indentation'] = $indentasi;
        }

        $run = $parent->addTextRun($gayaParagraf);

        if ($adaMarker) {
            $run->addText($marker->teks . "\u{00A0}\u{00A0}", $ctx['font']);
            $marker->teks = null; // hanya dipakai paragraf pertama.
        }

        foreach ($segmen as $s) {
            if ($s['br']) {
                $run->addTextBreak();
            } else {
                $run->addText($s['teks'], $s['font']);
            }
        }
    }

    /**
     * Mengumpulkan potongan teks beserta gaya hurufnya (tebal, miring,
     * garis bawah) dari node inline.
     */
    private static function kumpulkanSegmen(DOMNode $node, array $font, array &$segmen): void
    {
        if ($node->nodeType === XML_TEXT_NODE) {
            $teks = preg_replace('/\s+/u', ' ', $node->nodeValue ?? '');

            if ($teks !== '') {
                $segmen[] = ['br' => false, 'teks' => $teks, 'font' => $font];
            }

            return;
        }

        if ($node->nodeType !== XML_ELEMENT_NODE) {
            return;
        }

        $tag = strtolower($node->nodeName);

        if ($tag === 'br') {
            $segmen[] = ['br' => true, 'teks' => '', 'font' => $font];
            return;
        }

        if ($tag === 'strong' || $tag === 'b') {
            $font['bold'] = true;
        } elseif ($tag === 'em' || $tag === 'i') {
            $font['italic'] = true;
        } elseif ($tag === 'u') {
            $font['underline'] = 'single';
        }

        foreach ($node->childNodes as $anak) {
            self::kumpulkanSegmen($anak, $font, $segmen);
        }
    }

    // ---------------------------------------------------------------------
    // Daftar
    // ---------------------------------------------------------------------

    private static function buatDaftar(AbstractContainer $parent, DOMElement $daftar, array $ctx): void
    {
        $urut = strtolower($daftar->nodeName) === 'ol';
        $jenis = $daftar->getAttribute('type') ?: '1';
        $nomor = (int) ($daftar->getAttribute('start') ?: 1);

        foreach ($daftar->childNodes as $item) {
            if (
                $item->nodeType !== XML_ELEMENT_NODE
                || strtolower($item->nodeName) !== 'li'
            ) {
                continue;
            }

            $penanda = new \stdClass();
            $penanda->teks = $urut
                ? self::formatNomor($nomor++, $jenis)
                : '•';

            $ctxItem = $ctx;
            $ctxItem['left'] = $ctx['left'] + 360;
            $ctxItem['marker'] = $penanda;

            self::prosesAnak($parent, $item, $ctxItem);

            // Item kosong tetap diberi penanda.
            if ($penanda->teks !== null && $item->textContent !== null && trim($item->textContent) === '') {
                continue;
            }
        }
    }

    private static function formatNomor(int $n, string $jenis): string
    {
        switch ($jenis) {
            case 'a':
            case 'A':
                $huruf = '';
                $x = $n;

                while ($x > 0) {
                    $x--;
                    $huruf = chr(97 + ($x % 26)) . $huruf;
                    $x = intdiv($x, 26);
                }

                return ($jenis === 'A' ? strtoupper($huruf) : $huruf) . '.';

            case 'i':
            case 'I':
                $romawi = [
                    1000 => 'm', 900 => 'cm', 500 => 'd', 400 => 'cd',
                    100 => 'c', 90 => 'xc', 50 => 'l', 40 => 'xl',
                    10 => 'x', 9 => 'ix', 5 => 'v', 4 => 'iv', 1 => 'i',
                ];
                $hasil = '';

                foreach ($romawi as $nilai => $simbol) {
                    while ($n >= $nilai) {
                        $hasil .= $simbol;
                        $n -= $nilai;
                    }
                }

                return ($jenis === 'I' ? strtoupper($hasil) : $hasil) . '.';

            default:
                return $n . '.';
        }
    }

    // ---------------------------------------------------------------------
    // Tabel
    // ---------------------------------------------------------------------

    private static function buatTabel(AbstractContainer $parent, DOMElement $tabel, array $ctx): void
    {
        $baris = self::ambilBaris($tabel);

        if (!$baris) {
            return;
        }

        $adaBorder = (int) $tabel->getAttribute('border') > 0;
        $styleTabel = self::bacaStyle($tabel);

        $gaya = [
            'layout'     => TableStyle::LAYOUT_FIXED,
            'cellMargin' => 60,
            'width'      => 5000,
            'unit'       => TblWidth::PERCENT,
        ];

        if ($adaBorder) {
            $gaya['borderSize'] = 6;
            $gaya['borderColor'] = '000000';
        }

        $lebarTabel = max(1000, $ctx['lebar'] - $ctx['left']);
        $lebarKolom = self::hitungLebarKolom($baris, $lebarTabel);

        $ctxTabel = $ctx;
        $ctxTabel['align'] = self::petaAlign($styleTabel['text-align'] ?? null) ?? $ctx['align'];

        $tabelWord = $parent->addTable($gaya);

        foreach ($baris as $tr) {
            $styleTr = self::bacaStyle($tr);
            $ctxBaris = $ctxTabel;
            $ctxBaris['align'] = self::petaAlign($styleTr['text-align'] ?? null) ?? $ctxTabel['align'];

            $sel = self::ambilSel($tr);
            $tinggi = 0;

            foreach ($sel as $td) {
                $tinggi = max($tinggi, self::keTwips(self::bacaStyle($td)['height'] ?? null));
            }

            $barisWord = $tabelWord->addRow($tinggi > 0 ? $tinggi : null);
            $kolom = 0;

            foreach ($sel as $td) {
                $span = max(1, (int) $td->getAttribute('colspan'));
                $lebar = 0;

                for ($k = $kolom; $k < $kolom + $span; $k++) {
                    $lebar += $lebarKolom[$k] ?? 0;
                }

                $kolom += $span;

                $gayaSel = ['valign' => 'top'];

                if ($span > 1) {
                    $gayaSel['gridSpan'] = $span;
                }

                $selWord = $barisWord->addCell($lebar > 0 ? $lebar : null, $gayaSel);

                $styleSel = self::bacaStyle($td);
                $ctxSel = $ctxBaris;
                $ctxSel['align'] = self::petaAlign($styleSel['text-align'] ?? null) ?? $ctxBaris['align'];
                $ctxSel['left'] = 0;
                $ctxSel['spaceAfter'] = 0;
                $ctxSel['marker'] = null;
                $ctxSel['lebar'] = max(500, $lebar - 120);
                $ctxSel['font'] = strtolower($td->nodeName) === 'th'
                    ? ['bold' => true]
                    : [];

                self::prosesAnak($selWord, $td, $ctxSel);
            }
        }

        // Jarak kecil setelah tabel.
        $parent->addTextBreak(0, null, ['spaceAfter' => 0]);
    }

    /**
     * Baris milik tabel ini saja (bukan tabel bersarang).
     *
     * @return DOMElement[]
     */
    private static function ambilBaris(DOMNode $node): array
    {
        $hasil = [];

        foreach ($node->childNodes as $anak) {
            if ($anak->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $tag = strtolower($anak->nodeName);

            if ($tag === 'tr') {
                $hasil[] = $anak;
            } elseif (in_array($tag, ['thead', 'tbody', 'tfoot'], true)) {
                $hasil = array_merge($hasil, self::ambilBaris($anak));
            }
        }

        return $hasil;
    }

    /** @return DOMElement[] */
    private static function ambilSel(DOMElement $tr): array
    {
        $hasil = [];

        foreach ($tr->childNodes as $anak) {
            if (
                $anak->nodeType === XML_ELEMENT_NODE
                && in_array(strtolower($anak->nodeName), ['td', 'th'], true)
            ) {
                $hasil[] = $anak;
            }
        }

        return $hasil;
    }

    /**
     * Menentukan lebar tiap kolom (twips). Lebar diambil dari baris
     * pertama; kolom tanpa lebar membagi sisa ruang secara merata.
     */
    private static function hitungLebarKolom(array $baris, int $lebarTabel): array
    {
        $jumlahKolom = 0;

        foreach ($baris as $tr) {
            $n = 0;

            foreach (self::ambilSel($tr) as $td) {
                $n += max(1, (int) $td->getAttribute('colspan'));
            }

            $jumlahKolom = max($jumlahKolom, $n);
        }

        if ($jumlahKolom === 0) {
            return [];
        }

        $lebar = array_fill(0, $jumlahKolom, null);
        $kolom = 0;

        foreach (self::ambilSel($baris[0]) as $td) {
            $span = max(1, (int) $td->getAttribute('colspan'));
            $w = self::bacaStyle($td)['width'] ?? null;

            if ($w !== null && $span === 1) {
                if (str_ends_with($w, '%')) {
                    $lebar[$kolom] = (int) round($lebarTabel * (float) $w / 100);
                } else {
                    $lebar[$kolom] = self::keTwips($w);
                }
            }

            $kolom += $span;
        }

        $terpakai = array_sum(array_filter($lebar, fn ($x) => $x !== null));
        $kosong = count(array_filter($lebar, fn ($x) => $x === null));

        if ($kosong > 0) {
            $sisa = max(500 * $kosong, $lebarTabel - $terpakai);

            foreach ($lebar as $i => $x) {
                if ($x === null) {
                    $lebar[$i] = (int) floor($sisa / $kosong);
                }
            }
        }

        return $lebar;
    }

    // ---------------------------------------------------------------------
    // Utilitas
    // ---------------------------------------------------------------------

    /** @return array<string,string> */
    private static function bacaStyle(DOMNode $node): array
    {
        if (!$node instanceof DOMElement || !$node->hasAttribute('style')) {
            return [];
        }

        $hasil = [];

        foreach (explode(';', $node->getAttribute('style')) as $deklarasi) {
            if (!str_contains($deklarasi, ':')) {
                continue;
            }

            [$properti, $nilai] = array_map('trim', explode(':', $deklarasi, 2));
            $hasil[strtolower($properti)] = $nilai;
        }

        return $hasil;
    }

    private static function petaAlign(?string $nilai): ?string
    {
        return match (strtolower((string) $nilai)) {
            'left'    => 'left',
            'right'   => 'right',
            'center'  => 'center',
            'justify' => 'both',
            default   => null,
        };
    }

    /** Mengubah "100px" / "72pt" / "50" menjadi twips. */
    private static function keTwips(?string $nilai): int
    {
        if ($nilai === null || $nilai === '' || str_ends_with($nilai, '%')) {
            return 0;
        }

        $angka = (float) $nilai;

        return (int) round(str_ends_with($nilai, 'pt') ? $angka * 20 : $angka * 15);
    }
}
