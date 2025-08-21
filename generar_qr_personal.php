<

?php
require_once 'libs/qrlib.php'; // Asegúrate que esta ruta es correcta

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? '';
$pass = $_GET['pass'] ?? '';

if (!$tipo || !$id || !$pass) {
    http_response_code(400);
    exit('❌ Faltan parámetros.');
}

// Datos codificados como JSON
$data = json_encode([
    'tipo' => $tipo,
    'id' => $id,
    'pass' => $pass
]);

// Ruta de carpeta donde se guardarán los QR personales
$directorio = __DIR__ . '/qrs_personales/';
if (!file_exists($directorio)) {
    mkdir($directorio, 0777, true); // Crea carpeta si no existe
}

$archivoQR = $directorio . "{$tipo}_{$id}_personal.png";

// Generar el QR con tamaño más grande
$tamaño = 10; // Puedes ajustar este valor (8-12 es buen tamaño)
QRcode::png($data, $archivoQR, QR_ECLEVEL_L, $tamaño);

echo "✅ QR personal generado: qrs_personales/{$tipo}_{$id}_personal.png";
