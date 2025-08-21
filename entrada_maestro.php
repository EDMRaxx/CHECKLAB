<?php
session_start();
require("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $empleado = $_POST['empleado'] ?? '';
    $nombre = $_POST['nombre'] ?? '';

    $stmt = $conexion->prepare("SELECT * FROM maestros WHERE empleado = :empleado AND nombre = :nombre");
    $stmt->execute([
        ':empleado' => $empleado,
        ':nombre' => $nombre
    ]);

    if ($stmt->rowCount() > 0) {
        $fila = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['user'] = $fila;
        $_SESSION['rol'] = 'maestro';
        header("Location: perfil.php");
        exit();
    } else {
        $error = "Datos no válidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso Maestro</title>
</head>
<body>
    <h2>Ingreso de Maestro</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="empleado" placeholder="No. de Empleado" required><br>
        <input type="text" name="nombre" placeholder="Nombre completo" required><br>
        <button type="submit">Ingresar</button>
    </form>
</body>
</html>
