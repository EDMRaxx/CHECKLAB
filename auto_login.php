<?php
session_start();
require("db.php"); // Asegúrate de tener conexión aquí

$tipo = $_GET['tipo'] ?? '';
$usuario = $_GET['usuario'] ?? '';
$password = $_GET['password'] ?? '';

if ($tipo == 'alumno') {
    $sql = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ? AND password = ?");
    $sql->execute([$usuario, $password]);
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $_SESSION['tipo'] = 'alumno';
        $_SESSION['user'] = $data;
        header("Location: perfil.php");
        exit;
    }

} elseif ($tipo == 'maestro') {
    $sql = $pdo->prepare("SELECT * FROM maestros WHERE empleado = ? AND password = ?");
    $sql->execute([$usuario, $password]);
    $data = $sql->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        $_SESSION['tipo'] = 'maestro';
        $_SESSION['user'] = $data;
        header("Location: perfil.php");
        exit;
    }
}

echo "Acceso no válido.";
