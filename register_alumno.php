<?php
require 'db.php';

$matricula = $_POST['matricula'] ?? '';
$nombre = $_POST['nombre'] ?? '';
$password = $_POST['password'] ?? '';

if (!$matricula || !$nombre || !$password) {
    echo "❌ Todos los campos son obligatorios.";
    exit();
}

// Verificar si ya existe la matrícula
$stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
$stmt->execute([$matricula]);
if ($stmt->fetch()) {
    echo "⚠️ Matrícula ya registrada.";
    exit();
}

// Insertar nuevo alumno
$stmt = $pdo->prepare("INSERT INTO alumnos (matricula, nombre, password) VALUES (?, ?, ?)");
$stmt->execute([$matricula, $nombre, $password]);

echo "✅ Registro exitoso como alumno.";
echo '<br><a href="index.php"><button>Volver</button></a>';
