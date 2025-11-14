<?php
// backend/includes/json_connect.php

// Define la URL base de tu JSON Server (¡AJUSTA ESTA URL!)
define('API_URL', 'http://jsonserver:3000'); 

// -----------------------------------------------------
// FUNCIÓN GENERAL PARA PETICIONES (GET, PATCH, POST)
// -----------------------------------------------------

/**
 * Función genérica para realizar peticiones cURL a la API.
 * * @param string $endpoint El endpoint de la API (ej: /usuaris, /usuaris/1).
 * @param string $method El método HTTP (GET, POST, PATCH).
 * @param array $data Los datos a enviar (solo para POST/PATCH).
 * @return array|false La respuesta decodificada del servidor o false en caso de error.
 */
function makeApiRequest(string $endpoint, string $method = 'GET', array $data = []) {
    $url = API_URL . $endpoint;
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

    if (!empty($data)) {
        $json_data = json_encode($data);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data)
        ]);
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Los códigos de éxito varían: 200 (GET/PATCH), 201 (POST)
    if (in_array($http_code, [200, 201]) && !empty($response)) {
        return json_decode($response, true);
    }
    
    return false;
}

// -----------------------------------------------------
// FUNCIONES ESPECÍFICAS DE AUTENTICACIÓN
// -----------------------------------------------------

/**
 * Busca un usuario en el servidor JSON por su nombre de usuario (usado en Login/Register).
 * @param string $nomUsuari El nombre de usuario a buscar.
 * @return array|false El array del usuario encontrado o false si no existe.
 */
function buscarUsuarioPorNombre(string $nomUsuari) {
    $nomUsuari_encoded = urlencode($nomUsuari);
    // Endpoint para buscar: /usuaris?nom_usuari=...
    $endpoint = "/usuaris?nom_usuari=" . $nomUsuari_encoded;

    $usuaris = makeApiRequest($endpoint, 'GET');

    // La función makeApiRequest devuelve false en caso de error de conexión
    if ($usuaris === false) {
        return false;
    }
    
    // Si la búsqueda fue exitosa (código 200), devolvemos el primer resultado
    return $usuaris[0] ?? false; 
}

/**
 * Obtiene los datos de un usuario por su ID (usado en Profile).
 * @param int $id ID del usuario.
 * @return array|false Datos del usuario o false.
 */
function obtenerUsuarioPorId(int $id) {
    // Endpoint: /usuaris/{id}
    return makeApiRequest("/usuaris/{$id}", 'GET');
}

/**
 * Registra un nuevo usuario (usado en Register).
 * @param array $data Datos del nuevo usuario.
 * @return array|false Datos del usuario creado o false.
 */
function registrarUsuario(array $data) {
    // Endpoint: /usuaris
    return makeApiRequest("/usuaris", 'POST', $data);
}

/**
 * Actualiza parcialmente los datos de un usuario (usado en Profile).
 * @param int $id ID del usuario.
 * @param array $dataToPatch Datos a actualizar.
 * @return array|false Datos del usuario actualizado o false.
 */
function actualizarUsuarioPatch(int $id, array $dataToPatch) {
    // Endpoint: /usuaris/{id}
    return makeApiRequest("/usuaris/{$id}", 'PATCH', $dataToPatch);
}