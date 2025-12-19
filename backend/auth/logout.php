<?php
session_start();
session_unset();
session_destroy();

// Eliminar cookie si existe
if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/");
}

// Redirigir a la página de donde vino, o al index si no hay referencia
$redirect = $_SERVER['HTTP_REFERER'] ?? '../../web/index.html';
header("Location: $redirect");
exit;
?>