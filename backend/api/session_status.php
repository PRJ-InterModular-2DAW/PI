<?php
// backend/api/session_status.php
session_set_cookie_params(0, '/');
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'logged_in' => true,
        'user' => [
            'id' => $_SESSION['user_id'],
            'nom_usuari' => $_SESSION['nom_usuari'] ?? 'Usuario'
        ]
    ]);
} else {
    echo json_encode(['logged_in' => false, 'debug_session' => $_SESSION]);
}
?>