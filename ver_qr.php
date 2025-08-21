<?php
require_once 'libs/qrlib.php';
require_once 'db.php'; // Asegúrate de que aquí haces la conexión PDO

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? '';

if (!$tipo || !$id) {
    http_response_code(400);
    exit('Faltan parámetros.');
}

// Obtener datos del usuario según tipo
if ($tipo === 'alumno') {
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE id = ?");
} elseif ($tipo === 'maestro') {
    $stmt = $pdo->prepare("SELECT * FROM maestros WHERE id = ?");
} else {
    http_response_code(400);
    exit('Tipo inválido.');
}

$stmt->execute([$id]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    http_response_code(404);
    exit('Usuario no encontrado.');
}

// Crear datos para el QR
$usuario_id = $usuario['id'];
$password = $usuario['password'] ?? '';

// Convertir datos a JSON y generar QR temporal
$qrData = json_encode(['usuario' => $usuario_id, 'password' => $password]);
$filename = 'temp/' . $usuario_id . '_qr.png';
QRcode::png($qrData, $filename, 'H', 5);

// Mostrar imagen como respuesta
header('Content-Type: image/png');
readfile($filename);

// Opcional: eliminar después de mostrar (para evitar acumulación de archivos temporales)
// unlink($filename);
