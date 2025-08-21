<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('❌ Método inválido');
}

$data = json_decode($_POST['qrData'] ?? '', true);

if (!$data || $data['tipo'] !== 'maestro' || !isset($data['lab'])) {
    exit('QR inválido');
}

$laboratorio = $data['lab'];

// Tomar maestro de la sesión (porque ya se loguean en el registro)
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    exit('❌ Debes estar logueado como maestro primero.');
}

$maestro = $_SESSION['user'];

// Guardar asistencia con laboratorio
$pdo->prepare("INSERT INTO asistencias_maestros (maestro_id, fecha, laboratorio) VALUES (?, NOW(), ?)")
    ->execute([$maestro['id'], $laboratorio]);

echo '✅ Maestro logueado: ' . $maestro['nombre'] . ' en ' . $laboratorio;

$_SESSION['laboratorio'] = $laboratorio;

