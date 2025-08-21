<?php
session_start();
require 'db.php';

/* ──────── validar que exista una sesión de maestro ──────── */
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    http_response_code(401);           // 401 → No autorizado
    echo "Sesión no válida";
    exit();
}

$maestro_id   = (int)($_SESSION['user']['id'] ?? 0);
$laboratorio  = (int)($_POST['laboratorio'] ?? 0);
$observaciones = trim($_POST['observaciones'] ?? '');
$componentes   = $_POST['componentes'] ?? [];

/* ──────── validaciones básicas ──────── */
if (!$maestro_id || !$laboratorio || empty($componentes)) {
    http_response_code(400);           // 400 → Petición incorrecta
    echo "Datos incompletos";
    exit();
}

/* ──────── sanitizar cantidades a enteros ──────── */
$clean = [];
foreach ($componentes as $nombre => $cantidad) {
    $clean[$nombre] = (int)$cantidad;
}

/* ──────── convertir a JSON para almacenar ──────── */
$componentesJSON = json_encode($clean, JSON_UNESCAPED_UNICODE);

/* ──────── guardar en la base de datos ──────── */
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

/* ──────── respuesta para el fetch() del frontend ──────── */
if ($ok) {
    echo "Éxito";                      // **el JS busca exactamente esta palabra**
} else {
    http_response_code(500);           // 500 → Error del servidor
    echo "Error al guardar";
}