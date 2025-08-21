<?php
require 'db.php';

$tipo = $_GET['tipo'] ?? '';
$id = $_GET['id'] ?? '';
$lab = $_GET['lab'] ?? '';

if (!$tipo || !$id || !$lab) {
    exit('❌ Datos incompletos.');
}

// Registrar en tabla de accesos
$stmt = $pdo->prepare("INSERT INTO accesos (id_usuario, tipo, laboratorio, fecha, hora) VALUES (?, ?, ?, CURDATE(), CURTIME())");
$stmt->execute([$id, $tipo, $lab]);

echo "✅ Acceso registrado para $tipo $id en $lab.";
