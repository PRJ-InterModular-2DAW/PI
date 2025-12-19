<?php
// backend/api/login.php
session_set_cookie_params(0, '/'); // Cookie válida para toda la web
session_start();
header('Content-Type: application/json');

require_once '../includes/json_connect.php';

// Leer entrada JSON
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$nomUsuari = trim($input["nom_usuari"] ?? "");
$contrasenya_raw = $input["contrasenya"] ?? "";

if (empty($nomUsuari) || empty($contrasenya_raw)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan datos']);
    exit;
}

// Buscar usuario (usando la lógica actual de json_connect)
$usuari = buscarUsuarioPorNombre($nomUsuari);

if (!$usuari) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Usuario no encontrado']);
    exit;
}

// Verificar contraseña
if (password_verify($contrasenya_raw, $usuari['contrasenya'])) {

    // Login Exitoso
    $_SESSION['user_id'] = $usuari['id'];
    $_SESSION['nom_usuari'] = $usuari['nom_usuari'];

    // Forzar escritura de sesión
    session_write_close();

    echo json_encode([
        'success' => true,
        'message' => 'Login correcto',
        'user' => [
            'id' => $usuari['id'],
            'nom' => $usuari['nom_usuari']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Contraseña incorrecta']);
}
?>