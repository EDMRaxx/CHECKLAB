<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!$usuario || !$password) {
        $error = "❌ Usuario o contraseña vacíos.";
    } else {
        // Validar alumnos
        $stmt = $pdo->prepare("SELECT * FROM alumnos WHERE matricula = ?");
        $stmt->execute([$usuario]);
        $alumno = $stmt->fetch(PDO::FETCH_ASSOC);

     if ($alumno && $alumno['password'] === $password) {
    $_SESSION['user'] = [
        'id' => $alumno['id'],
        'nombre' => $alumno['nombre'],
        'tipo' => 'alumno',
        'usuario' => $usuario,
        'password' => $password
    ];
    $stmt = $pdo->prepare("INSERT INTO ingresos_usuarios (usuario_id, tipo) VALUES (?, ?)");
    $stmt->execute([$alumno['id'], 'alumno']);

    header("Location: perfil.php");
    exit();
}

        // Validar maestros
        $stmt = $pdo->prepare("SELECT * FROM maestros WHERE empleado = ?");
        $stmt->execute([$usuario]);
        $maestro = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($maestro && $maestro['password'] === $password) {
    $_SESSION['user'] = [
        'id' => $maestro['id'],
        'nombre' => $maestro['nombre'],
        'tipo' => 'maestro',
        'usuario' => $usuario,
        'password' => $password
    ];
    $stmt = $pdo->prepare("INSERT INTO ingresos_usuarios (usuario_id, tipo) VALUES (?, ?)");
    $stmt->execute([$maestro['id'], 'maestro']);

    header("Location: perfil.php");
    exit();
}

        // Admin
        if ($usuario === 'admin' && $password === 'admin') {
            $_SESSION['user'] = [
                'nombre' => 'Administrador',
                'tipo' => 'administrador',
                'usuario' => $usuario,
                'password' => $password
            ];
            header("Location: admin.php");
            exit();
        }

        $error = "❌ Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Iniciar Sesión</title>
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <body id="page-login">
    <link rel="stylesheet" href="estilo.css" />
</head>
<body>
    <h1>Sistema de Laboratorio</h1>

    <div class="form-container active">
        <h2>Iniciar Sesión</h2>

        <?php if (!empty($error)) echo "<p style='color: #ff4d4d; margin-bottom: 10px;'>$error</p>"; ?>

        <form id="login-form" method="POST" action="login.php">
            <input type="text" id="usuario" name="usuario" placeholder="Clave" required />
            <input type="password" id="password" name="password" placeholder="Contraseña" required />
            <input type="submit" value="Iniciar Sesión" />
        </form>

        <button onclick="startQRScanner()">Iniciar con QR</button>
        <div id="reader" style="margin-top: 20px;"></div>

        <p style="margin-top: 20px;">¿No tienes cuenta?</p>
        <a href="register.php" style="color: #4caf50; text-decoration: none; font-weight: bold;">Regístrate aquí</a>
    </div>

<script>
let qrScanner = null;

function startQRScanner() {
    const reader = document.getElementById("reader");

    if (qrScanner !== null) {
        qrScanner.clear();
        qrScanner = null;
        reader.innerHTML = "";
        return;
    }

    qrScanner = new Html5Qrcode("reader");
    qrScanner.start(
        { facingMode: "environment" },
        { fps: 10, qrbox: 250 },
        qrCodeMessage => {
            try {
                const data = JSON.parse(qrCodeMessage);

                if (!data.tipo) {
                    alert("QR inválido");
                    return;
                }

                if (data.tipo === "alumno") {
    fetch("login_qr_alumno.php", { 
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "qrData=" + encodeURIComponent(qrCodeMessage)
    })
        .then(res => res.text())
        .then(resp => {
            alert(resp);
            if (resp.startsWith("✅")) {
                window.location.href = "perfil.php";
            }
        });

} else if (data.tipo === "maestro") {
    fetch("login_qr_maestro.php", { 
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "qrData=" + encodeURIComponent(qrCodeMessage)
    })
        .then(res => res.text())
        .then(resp => {
            alert(resp);
            if (resp.startsWith("✅")) {
                window.location.href = "perfil.php#reporte";
            }
        });
}
 else {
                    alert("QR desconocido");
                }

                qrScanner.stop().then(() => {
                    document.getElementById("reader").innerHTML = "";
                    qrScanner = null;
                });

            } catch (e) {
                alert("QR no válido o malformado");
            }
        },
        errorMessage => {}
    );
}
</script>


</body>
</html>