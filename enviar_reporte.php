<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    http_response_code(401);
    echo "Sesión no válida";
    exit();
}

$maestro_id = (int)($_SESSION['user']['id'] ?? 0);
$laboratorio = (int)($_POST['laboratorio'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');
$componentes = $_POST['componentes'] ?? [];

if (!$maestro_id || !$laboratorio || empty($componentes)) {
    http_response_code(400);
    echo "Datos incompletos";
    exit();
}

$clean = [];
foreach ($componentes as $nombre => $cantidad) {
    $clean[$nombre] = (int)$cantidad;
}

$componentesJSON = json_encode($clean, JSON_UNESCAPED_UNICODE);

$stmt = $pdo->prepare(
    "INSERT INTO reportes (maestro_id, laboratorio, componentes, observaciones)
     VALUES (?, ?, ?, ?)"
);
$ok = $stmt->execute([
    $maestro_id,
    $laboratorio,
    $componentesJSON,
    $observaciones
]);

if ($ok) {
    echo "Éxito";
} else {
    http_response_code(500);
    echo "Error al guardar";
}
