<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $observaciones = $_POST['observaciones'];
    $componentes = $_POST['componentes'];

    $componentes_json = json_encode($componentes);

    $stmt = $pdo->prepare("UPDATE reportes SET observaciones = ?, componentes = ? WHERE id = ?");
    $stmt->execute([$observaciones, $componentes_json, $id]);

    header("Location: perfil.php");
    exit();
}
?>
