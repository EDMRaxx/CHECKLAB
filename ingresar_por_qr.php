<?php
require 'db.php';

if (!isset($_GET['id'])) {
    die('ID no recibido');
}

$id = trim($_GET['id']);

// Buscar en maestros
$stmt = $pdo->prepare("SELECT * FROM maestros WHERE usuario = ?");
$stmt->execute([$id]);
$maestro = $stmt->fetch(PDO::FETCH_ASSOC);

if ($maestro) {
    session_start();
    $_SESSION['user'] = [
        'id' => $maestro['id'],
        'nombre' => $maestro['nombre'],
        'usuario' => $maestro['usuario'],
        'password' => $maestro['password'],
        'tipo' => 'maestro'
    ];
    header("Location: perfil.php");
    exit();
}

// Buscar en alumnos
$stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
$stmt->execute([$id]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

if ($alumno) {
    session_start();
    $_SESSION['user'] = [
        'id' => $alumno['id'],
        'nombre' => $alumno['nombre'],
        'usuario' => $alumno['matricula'],
        'password' => null,
        'tipo' => 'alumno'
    ];
    header("Location: perfil.php");
    exit();
}

echo "ID no encontrado";
