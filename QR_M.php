<?php
include "phpqrcode/qrlib.php";

//$ip = "30.30.5.152"; // Cambia por IP o dominio
$baseUrl = "https://b1df1c84685c.ngrok-free.app/catalogo/login_qr_maestro.php"; // Nuevo archivo

$urlQR = "$baseUrl?lab=" . urlencode("Laboratorio Servidor");

$archivoQR = "qrs/qr_universal_maestro.png";
QRcode::png($urlQR, $archivoQR, QR_ECLEVEL_H, 6);

echo "<h3>QR Universal generado para Maestros</h3>";
echo "<img src='$archivoQR'>";
