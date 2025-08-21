<?php
require 'db.php';
require 'libs/qrlib.php';

$tipo = $_GET['tipo'] ?? '';

if (!in_array($tipo, ['alumno', 'maestro'])) {
    http_response_code(400);
    echo "❌ Tipo inválido.";
    exit();
}

$usuarios = [];

if ($tipo === 'alumno') {
    $stmt = $pdo->query("SELECT matricula AS id FROM alumnos");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmt = $pdo->query("SELECT empleado AS id FROM maestros");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Carpeta donde guardar los QR individuales
$dir = __DIR__ . '/temp/';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
}

$base_url = 'http://30.30.12.241'; // Tu IP local

foreach ($usuarios as $usuario) {
    $id = $usuario['id'];
    $url = $base_url . "/perfil.php?tipo=$tipo&id=$id";
    $filename = $dir . $tipo . '_' . $id . '.png';
    QRcode::png($url, $filename, QR_ECLEVEL_L, 4);
}

echo "✅ QR individuales generados para todos los $tipo(s)";
