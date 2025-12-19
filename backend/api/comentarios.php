<?php
header('Content-Type: application/json');
session_start();
require_once '../includes/json_connect.php'; // Usa la config (local o api) definida centralizadamente

// Helper para convertir lo que devuelve json-server a formato compatible si fuera necesario
// Pero json-server ya devuelve array de objetos

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $producto_id = isset($_GET['producto_id']) ? (int) $_GET['producto_id'] : 0;

    if ($producto_id === 0) {
        http_response_code(400);
        echo json_encode(['error' => 'ID de producto requerido']);
        exit;
    }

    // Usar la función de json_connect (API)
    // endpoint: /comentaris?producto_id=X
    $comentarios = makeApiRequest("/comentaris?producto_id=" . $producto_id, 'GET');

    if ($comentarios === false) {
        $comentarios = []; // Si falla o no hay, devolver vacío
    }

    echo json_encode($comentarios);
} elseif ($method === 'POST') {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Debes iniciar sesión para comentar']);
        exit;
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['producto_id']) || !isset($input['comentario'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos incompletos']);
        exit;
    }

    $nuevoComentario = [
        'id' => uniqid(),
        'producto_id' => (int) $input['producto_id'],
        'usuari_id' => $_SESSION['user_id'],
        // Usamos el nombre de sesión si existe
        'nom_usuari' => $_SESSION['nom_usuari'] ?? 'Usuario',
        'comentario' => htmlspecialchars($input['comentario']),
        'puntuacio' => isset($input['puntuacio']) ? (int) $input['puntuacio'] : 0,
        'fecha' => date('Y-m-d H:i:s')
    ];

    // Guardar vía API
    $result = makeApiRequest("/comentaris", 'POST', $nuevoComentario);

    if ($result) {
        echo json_encode($result);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Error al guardar en servidor']);
    }
}
?>