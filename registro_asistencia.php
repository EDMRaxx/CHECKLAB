<?php
require 'db.php';

// Obtener matrícula desde un formulario rápido o desde sesión
// (en QR universal no tenemos matrícula, así que mejor pedirla)
$laboratorio = $_GET['lab'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = $_POST['matricula'] ?? '';

    if (!empty($matricula)) {
        $stmt = $pdo->prepare("INSERT INTO asistencias (matricula, laboratorio, fecha) VALUES (?, ?, NOW())");
        $stmt->execute([$matricula, $laboratorio]);

        echo "✅ Asistencia registrada para $matricula en laboratorio $laboratorio";
        exit;
    } else {
        echo "❌ Debes ingresar tu matrícula.";
    }
}
?>
<form method="POST">
    <h2>Registro de asistencia - <?php echo htmlspecialchars($laboratorio); ?></h2>
    <input type="text" name="matricula" placeholder="Ingresa tu matrícula" required>
    <button type="submit">Registrar</button>
</form>
