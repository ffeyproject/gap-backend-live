<?php
namespace backend\components;

use kartik\mpdf\Pdf;

/**
 * Mencari layout (jumlah kolom tabel + ukuran font) terbesar yang masih muat dalam 1 halaman PDF.
 */
class SinglePagePdf
{
    /**
     * @param callable $createPdf function(int $columns, float $fontSize): Pdf
     * @param float $maxFontSize ukuran font normal (dipakai jika data sedikit)
     * @param float $minColumnFontSize di bawah ukuran ini, lebih baik tambah kolom daripada mengecilkan font
     * @param float $minFontSize batas font paling kecil
     * @param int $maxColumns
     * @return string isi PDF
     */
    public static function render(callable $createPdf, $maxFontSize, $minColumnFontSize = 7, $minFontSize = 2, $maxColumns = 6)
    {
        for ($columns = 2; $columns <= $maxColumns; $columns++) {
            if (($output = self::tryRender($createPdf, $columns, $maxFontSize)) !== null) {
                return $output;
            }

            $lo = $columns < $maxColumns ? $minColumnFontSize : $minFontSize;
            if (($best = self::tryRender($createPdf, $columns, $lo)) === null) {
                continue;
            }

            // binary search ukuran font terbesar yang masih 1 halaman, agar halaman terisi penuh
            $hi = $maxFontSize;
            while ($hi - $lo > 0.2) {
                $mid = round(($lo + $hi) / 2, 2);
                if (($output = self::tryRender($createPdf, $columns, $mid)) !== null) {
                    $lo = $mid;
                    $best = $output;
                } else {
                    $hi = $mid;
                }
            }
            return $best;
        }

        // data terlalu banyak, tidak ada yang muat 1 halaman: pakai layout paling rapat
        $pdf = $createPdf($maxColumns, $minFontSize);
        return $pdf->output($pdf->content, '', Pdf::DEST_STRING);
    }

    /**
     * @return string|null isi PDF jika muat 1 halaman, null jika tidak
     */
    private static function tryRender(callable $createPdf, $columns, $fontSize)
    {
        /* @var $pdf Pdf */
        $pdf = $createPdf($columns, $fontSize);
        $output = $pdf->output($pdf->content, '', Pdf::DEST_STRING);
        return $pdf->getApi()->page <= 1 ? $output : null;
    }
}
