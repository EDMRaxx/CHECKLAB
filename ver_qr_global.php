<?php
require 'libs/qrlib.php'; // Asegúrate de tener QRcode::png disponible

$tipo = $_GET['tipo'] ?? '';

if ($tipo === 'alumno') {
    $data = 'QR global para todos los alumnos';
} elseif ($tipo === 'maestro') {
    $data = 'QR global para todos los maestros';
} else {
    http_response_code(400);
    exit('Tipo no válido');
}

// Encabezado para imagen PNG
header('Content-Type: image/png');

// Parámetros para mejor calidad del QR
$tamaño = 10;        // Tamaño de cada "pixel" del módulo (entre 4 y 12 se ve bien)
$errorLevel = 'H';   // Nivel de corrección de errores (L, M, Q, H)
$margen = 1;         // Margen alrededor del código QR

// Generar el código QR directamente al navegador con mejor calidad
QRcode::png($data, false, $errorLevel, $tamaño, $margen);
?>
