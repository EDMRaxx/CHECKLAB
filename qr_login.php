<?php
session_start();
require_once 'db.php';

$matricula = $_GET['matricula'] ?? '';
$password = $_GET['password'] ?? '';

$stmt = $pdo->prepare("SELECT 'alumno' AS tipo, id, nombre, matricula AS usuario, matricula, password
    FROM alumnos 
    WHERE matricula = :matricula AND password = :password
    UNION
    SELECT 'maestro' AS tipo, id, nombre, empleado AS usuario, empleado AS matricula, password
    FROM maestros 
    WHERE empleado = :matricula AND password = :password");

$stmt->execute([
    ':matricula' => $matricula,
    ':password' => $password
]);

if ($stmt->rowCount() > 0) {
    $fila = $stmt->fetch(PDO::FETCH_ASSOC);

    // Guardar igual que el login normal
    $_SESSION['user'] = [
        'id' => $fila['id'],
        'nombre' => $fila['nombre'],
        'tipo' => $fila['tipo'],
        'usuario' => $fila['usuario'],   // campo genérico para login
        'matricula' => $fila['matricula'], // para alumnos o maestros
        'password' => $fila['password']
    ];

    header("Location: perfil.php");
    exit();
} else {
    echo "Acceso no válido.";
    exit();
}

