<?php
header('Content-Type: application/json');
require_once '../../backend/includes/json_connect.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id > 0) {
    // Buscar prod específico en la API
    // Endpoint: /productes?id=X (devuelve array) o /productes/X (devuelve objeto)
    // json-server soporta ambos. /productes/id es mejor
    $producto = makeApiRequest("/productes/{$id}", 'GET');

    if ($producto) {
        echo json_encode($producto);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Producto no encontrado']);
    }
} else {
    // Todos
    $productos = makeApiRequest("/productes", 'GET');
    echo json_encode($productos ?: []);
}
?>