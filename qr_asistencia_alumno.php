<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Método inválido";
    exit;
}

$qrData = $_POST['qrData'] ?? '';
if (!$qrData) {
    echo "QR vacío";
    exit;
}

$data = json_decode($qrData, true);
if (!$data || !isset($data['id'])) {
    echo "QR inválido";
    exit;
}

$idAlumno = $data['id'];

// Guardar asistencia (puedes cambiar el nombre de la tabla)
$stmt = $pdo->prepare("INSERT INTO asistencias_alumnos (alumno_id, fecha) VALUES (?, NOW())");
$stmt->execute([$idAlumno]);

echo "✅ Asistencia registrada para alumno ID: " . htmlspecialchars($idAlumno);

