<?php
session_start();
require 'db.php';

$tipo = $_GET['tipo'] ?? '';
$laboratorio = $_GET['lab'] ?? '';
$token = $_COOKIE['auto_login_token'] ?? '';

if (empty($tipo)) {
    echo "❌ Tipo de usuario no especificado.";
    exit;
}

if (empty($token)) {
    echo "❌ No hay sesión guardada para iniciar automáticamente.";
    exit;
}

if ($tipo === 'alumno') {
    $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE auto_login_token = ?");
} elseif ($tipo === 'maestro') {
    $stmt = $pdo->prepare("SELECT * FROM maestros WHERE auto_login_token = ?");
} else {
    echo "❌ Tipo de usuario inválido.";
    exit;
}

$stmt->execute([$token]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    $_SESSION['user'] = [
        'id' => $data['id'],
        'nombre' => $data['nombre'],
        'tipo' => $tipo,
        'usuario' => $tipo === 'alumno' ? $data['matricula'] : $data['empleado'],
        'matricula' => $tipo === 'alumno' ? $data['matricula'] : $data['empleado'],
        'password' => $data['password']
    ];
    header("Location: perfil.php?laboratorio=" . urlencode($laboratorio));
    exit;
} else {
    echo "❌ Token no válido o sesión expirada.";
    exit;
}
