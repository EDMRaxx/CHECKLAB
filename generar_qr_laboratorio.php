<?php
require_once 'libs/qrlib.php';

// Limpiar salida previa
if (ob_get_length()) ob_end_clean();

// Obtener parámetros
$tipo = $_GET['tipo'] ?? '';
$lab  = $_GET['lab'] ?? '';
$id   = $_GET['id'] ?? '';

if (!$tipo || !$lab || !$id) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "❌ Parámetros inválidos"]);
    exit;
}

// Carpeta ya existente (ajusta el nombre si es diferente)
$directorio = __DIR__ . '/qr_lab/';

// Asegurarse que realmente exista
if (!is_dir($directorio)) {
    header('Content-Type: application/json');
    echo json_encode(["error" => "❌ La carpeta 'qr_lab' no existe."]);
    exit;
}

// Nombre y ruta del archivo
$nombreArchivo = "{$tipo}_{$id}_{$lab}.png";
$rutaArchivo = $directorio . $nombreArchivo;

// URL que contendrá el QR
$contenido = "https://tusitio.com/laboratorio.php?tipo=$tipo&id=$id&lab=$lab";

// Generar solo si no existe
if (!file_exists($rutaArchivo)) {
    QRcode::png($contenido, $rutaArchivo, QR_ECLEVEL_L, 10);
}

// Devolver respuesta JSON
header('Content-Type: application/json');
echo json_encode([
    "mensaje" => "✅ QR generado correctamente.",
    "archivo" => "qr_lab/" . $nombreArchivo,
    "url" => $contenido
]);

// Log del evento
file_put_contents(
    __DIR__ . '/log.txt',
    date('Y-m-d H:i:s') . " -> QR generado para tipo=$tipo, id=$id, lab=$lab\n",
    FILE_APPEND
);
exit;
