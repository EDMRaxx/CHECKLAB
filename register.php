<?php
session_start();
require 'db.php';
require_once 'libs/qrlib.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identificador = $_POST['identificador'] ?? '';
    $nombre = $_POST['nombre'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$identificador || !$nombre || !$password) {
        http_response_code(400);
        echo "❌ Todos los campos son obligatorios.";
        exit();
    }

    // Validar contraseña
    if (!preg_match('/^[A-Za-z0-9]{1,10}$/', $password)) {
        http_response_code(400);
        echo "❌ La contraseña debe tener solo letras o números, máximo 10 caracteres.";
        exit();
    }

    // 🔹 ALUMNOS
    if (preg_match('/^(21|22|23|24|25)\d{6}$/', $identificador)) {
        $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
        $stmt->execute([$identificador]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo "⚠️ Matrícula ya registrada.";
            exit();
        }

        // Insertar alumno
        $stmt = $pdo->prepare("INSERT INTO alumnos (matricula, nombre, password) VALUES (?, ?, ?)");
        $stmt->execute([$identificador, $nombre, $password]);
        $id = $pdo->lastInsertId();

        // Auto-login después del registro
        $_SESSION['user'] = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => 'alumno',
            'usuario' => $identificador
        ];

        echo "✅ Alumno registrado correctamente.";

    // 🔹 MAESTROS
    } elseif (preg_match('/^\d{6}$/', $identificador)) {
        $stmt = $pdo->prepare("SELECT * FROM maestros WHERE empleado = ?");
        $stmt->execute([$identificador]);
        if ($stmt->fetch()) {
            http_response_code(409);
            echo "⚠️ Número de empleado ya registrado.";
            exit();
        }

        $stmt = $pdo->prepare("INSERT INTO maestros (empleado, nombre, password) VALUES (?, ?, ?)");
        $stmt->execute([$identificador, $nombre, $password]);
        $id = $pdo->lastInsertId();

        // Auto-login después del registro
        $_SESSION['user'] = [
            'id' => $id,
            'nombre' => $nombre,
            'tipo' => 'maestro',
            'usuario' => $identificador
        ];

        echo "✅ Maestro registrado correctamente.";
    } else {
        http_response_code(400);
        echo "❌ Identificador no válido.";
        exit();
    }

    exit();
}
?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro</title>
    <body id="page-register">
    <link rel="stylesheet" href="estilo.css" />
</head>
<body>
    <h1>Sistema de Laboratorio</h1>

    <div class="form-container active">
        <h2>Registro</h2>

        <form id="registro-form" method="POST" action="register.php">
            <input type="text" name="identificador" placeholder="Clave" required>
            <input type="text" name="nombre" placeholder="Nombre completo" required>
            <input type="password" name="password" placeholder="Contraseña (Max 10, solo letras y números)" 
                   pattern="[A-Za-z0-9]{1,10}" maxlength="10" 
                   title="Máximo 10 caracteres, solo letras o números" required>
            <input type="submit" value="Registrarse">
        </form>

        <p id="mensaje-registro" style="margin-top: 10px;"></p>

        <p style="margin-top: 20px;">¿Ya tienes cuenta?</p>
        <a href="login.php" style="color: #0c8512ff; font-weight: bold; text-decoration: none;">Inicia sesión aquí</a>
    </div>

    <script>
        document.getElementById('registro-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            fetch('register.php', {
                method: 'POST',
                body: formData
            })
            .then(async response => {
                const mensaje = document.getElementById('mensaje-registro');
                const text = await response.text();
                mensaje.textContent = text;
                mensaje.style.color = response.ok ? '#4caf50' : 'red';

                if (response.ok) {
                    setTimeout(() => {
                        window.location.href = 'login.php';
                    }, 1500);
                }
            })
            .catch(() => {
                const mensaje = document.getElementById('mensaje-registro');
                mensaje.textContent = '❌ Error al registrar.';
                mensaje.style.color = 'red';
            });
        });
    </script>
</body>
</html>
