<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['tipo'] !== 'maestro') {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM alumnos");
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lista de Alumnos</title>
</head>
<body>
    <h1>Lista de Alumnos Registrados</h1>
    <ul>
        <?php foreach ($alumnos as $a): ?>
            <li><?php echo htmlspecialchars($a['matricula']) . ' - ' . htmlspecialchars($a['nombre']); ?></li>
        <?php endforeach; ?>
    </ul>

    <br><br>
    <a href="perfil.php"><button>Volver al Perfil</button></a>
</body>
</html>
