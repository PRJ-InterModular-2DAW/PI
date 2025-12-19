<?php
// backend/auth/logout.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Destruir la sessió
$_SESSION = array(); 
session_destroy(); 

// Eliminar la cookie d'identificació
if (isset($_COOKIE['user_id'])) {
    // Establece el valor a vacío y la caducidad en el pasado
    setcookie('user_id', '', time() - 3600, "/");
}

// Redirigir a la página de inicio (sube dos niveles: ../../)
header("Location: ../../index.html"); 
exit;
?>