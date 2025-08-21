<?php
class QRcode {
    public static function png($text, $outfile = false, $level = 'L', $size = 3, $margin = 4) {
        $url = "https://api.qrserver.com/v1/create-qr-code/?" .
               "data=" . urlencode($text) .
               "&size=" . ($size * 10) . "x" . ($size * 10) .
               "&margin=$margin";

        $image = @file_get_contents($url);

        if (!$image) {
            die("❌ No se pudo obtener el QR desde la API externa.");
        }

        if (!$outfile) {
            header('Content-Type: image/png');
            echo $image;
        } else {
            $success = @file_put_contents($outfile, $image);
            if (!$success) {
                die("❌ No se pudo guardar el QR en el archivo '$outfile'.");
            }
        }
    }
}
