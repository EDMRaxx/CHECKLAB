<?php
include "phpqrcode/qrlib.php"; // Asegúrate de que la ruta esté bien

// IP de tu PC en la red local (esto es importante para que funcione en el celular)
$ip_local = "30.30.5.152"; // Cámbialo por el IP real de tu PC en la red Wi-Fi
$matricula = "21045156"; // Esto puedes obtenerlo de la sesión o BD
$url = "http://$ip_local/catalogo/perfil.php?matricula=$matricula";

// Ruta donde se guardará el QR
$archivoQR = "qrs/perfil_$matricula.png";
QRcode::png($url, $archivoQR, QR_ECLEVEL_H, 6);

echo "<h3>QR generado</h3>";
echo "<img src='$archivoQR'>";

