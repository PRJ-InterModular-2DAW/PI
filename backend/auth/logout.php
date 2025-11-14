<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$_SESSION = array(); 
session_destroy(); 

if (isset($_COOKIE['user_id'])) {
    setcookie('user_id', '', time() - 3600, "/");
}

header("Location: index.html"); 
exit;

?>
