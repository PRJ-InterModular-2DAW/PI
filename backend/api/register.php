<?php
// backend/api/register.php
header('Content-Type: application/json');
require_once '../includes/json_connect.php';

// Recibir JSON o POST normal
$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

// Validar datos mínimos
if (empty($input['nom_usuari']) || empty($input['contrasenya']) || empty($input['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Faltan campos obligatorios']);
    exit;
}

// Comprobar si ya existe el usuario
$existe = buscarUsuarioPorNombre($input['nom_usuari']);
if ($existe) {
    http_response_code(409); // Conflict
    echo json_encode(['success' => false, 'error' => 'El nombre de usuario ya existe']);
    exit;
}

// Preparar datos para registro
$nuevoId = obtenerSiguienteIdUsuario(); // Función de json_connect.php
$hashPassword = password_hash($input['contrasenya'], PASSWORD_BCRYPT);

$nuevoUsuario = [
    'id' => (string) $nuevoId,
    'nom_usuari' => trim($input['nom_usuari']),
    'contrasenya' => $hashPassword,
    'email' => trim($input['email']),
    'nom' => trim($input['nom'] ?? ''),
    'cognoms' => trim($input['cognoms'] ?? ''),
    'data_registre' => date('c') // ISO 8601
];

// Guardar
try {
    $resultado = registrarUsuario($nuevoUsuario);
    if ($resultado) {
        echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Error al guardar usuario en base de datos']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error del servidor: ' . $e->getMessage()]);
}
?>