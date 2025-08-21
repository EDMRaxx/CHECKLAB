<?php
require_once 'libs/qrlib.php';

ini_set('allow_url_fopen', 1); // Asegura que file_get_contents funcione

// Limpiar salida previa
if (ob_get_length()) ob_end_clean();

// Obtener y validar parámetros
$tipo = $_GET['tipo'] ?? '';
$lab  = $_GET['lab'] ?? '';
$id   = $_GET['id'] ?? '';

if (empty($tipo) || empty($lab) || empty($id)) {
    header("Content-Type: text/plain");
    echo '❌ Parámetros inválidos. Faltan tipo, lab o id.';
    exit;
}

// Carpeta donde se guardan los QR
$carpeta = 'qr_lab';
if (!is_dir($carpeta)) {
    mkdir($carpeta, 0777, true);
}

// Construir nombre de archivo
$nombreArchivo = "{$tipo}_{$id}_{$lab}.png";
$rutaArchivo = "$carpeta/$nombreArchivo";

// Contenido del QR
$contenido = "https://tusitio.com/laboratorio.php?tipo=$tipo&id=$id&lab=$lab";

// Generar QR si no existe
if (!file_exists($rutaArchivo)) {
    QRcode::png($contenido, $rutaArchivo, 'L', 10);
}

// Mostrar imagen
if (file_exists($rutaArchivo)) {
    header("Content-Type: image/png");
    header("Expires: 0");
    header("Cache-Control: no-cache, must-revalidate");
    readfile($rutaArchivo);
    exit;
} else {
    echo "❌ No se pudo generar el QR.";
    exit;
}
