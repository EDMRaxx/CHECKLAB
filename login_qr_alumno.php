<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit('❌ Método inválido');
}

$data = json_decode($_POST['qrData'] ?? '', true);

// Verificamos que el QR tenga los datos correctos
if (!$data || $data['tipo'] !== 'alumno' || !isset($data['lab'])) {
    exit('QR inválido');
}

$laboratorio = $data['lab'];

// Tomar alumno de la sesión (ya que el registro los loguea automáticamente)
if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'alumno') {
    exit('❌ Debes estar logueado como alumno primero.');
}

$alumno = $_SESSION['user'];

// Después de validar al alumno y antes de redirigir a perfil.php:

$alumno_id = $alumno['id'];  // Asegúrate de que $alumno viene del SELECT

// Si el laboratorio viene del QR o POST
$laboratorioNombre = $_POST['laboratorio'] ?? 'Laboratorio Cisco';

// Guardar el laboratorio en el alumno
$stmt = $pdo->prepare("UPDATE alumnos SET laboratorio = ? WHERE id = ?");
$stmt->execute([$laboratorioNombre, $alumno['id']]);

// Notificación visual
echo '✅ Alumno logueado: ' . htmlspecialchars($alumno['nombre']) . ' en ' . htmlspecialchars($laboratorioNombre);






