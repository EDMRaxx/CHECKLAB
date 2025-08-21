<?php 
session_start();
require 'db.php';

if (isset($_SESSION['user'])) {
    header("Location: perfil.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio - Sistema de Laboratorio</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <h1>Sistema de Revisión de Laboratorios</h1>
    <a href="login.php"><button>Iniciar Sesión</button></a>
    <a href="register.php"><button>Registrarse</button></a>
</body>
</html>
