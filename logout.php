<?php
session_start();
session_unset();  // Limpia las variables de sesión
session_destroy();  // Destruye la sesión

// Opcional: borra cookies si usas
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirige al login
header("Location: login.php");
exit();
