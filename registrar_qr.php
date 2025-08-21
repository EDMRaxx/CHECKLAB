<?php
require "qrs/phpqrcode/qrlib.php";

$ip = "30.30.5.152"; // Cambia por la IP real
$baseUrl = "http://$ip/catalogo/login_qr_universal.php";

$tipo = "alumno"; // o "maestro"
$usuario = "21045156"; // o número de empleado
$password = "maro21"; // la contraseña real
$laboratorio = "Cisco";

$urlQR = "$baseUrl?tipo=$tipo&usuario=$usuario&password=$password&lab=$laboratorio";

QRcode::png($urlQR, "qrs/$tipo" . "_" . $usuario . "_$laboratorio.png", QR_ECLEVEL_H, 6);
echo "<h3>QR generado:</h3><img src='qrs/$tipo" . "_" . $usuario . "_$laboratorio.png'>";
