<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    $stmt = $pdo->prepare("DELETE FROM reportes WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: perfil.php");
    exit();
}
?>
