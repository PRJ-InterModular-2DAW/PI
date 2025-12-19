<?php
// backend/includes/json_connect.php

// REVERSIÓN: Usar la configuración original de Docker/JsonServer que funcionaba al usuario
define('API_URL', 'http://jsonserver:3000');

// Función genérica para cURL
function makeApiRequest(string $endpoint, string $method = 'GET', array $data = [])
{
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

    if (in_array($http_code, [200, 201]) && !empty($response)) {
        return json_decode($response, true);
    }

    return false;
}

// Funciones de autenticación usando la API

function buscarUsuarioPorNombre(string $nomUsuari)
{
    // Buscamos tal cual (la API original filtraba por ?nom_usuari=...)
    $nomUsuari_encoded = urlencode($nomUsuari);
    $endpoint = "/usuaris?nom_usuari=" . $nomUsuari_encoded;
    $usuaris = makeApiRequest($endpoint, 'GET');

    if ($usuaris && count($usuaris) > 0) {
        return $usuaris[0];
    }
    return false;
}

function obtenerUsuarioPorId(string $id)
{
    return makeApiRequest("/usuaris/{$id}", 'GET');
}

function registrarUsuario(array $data)
{
    return makeApiRequest("/usuaris", 'POST', $data);
}

function actualizarUsuarioPatch(string $id, array $dataToPatch)
{
    return makeApiRequest("/usuaris/{$id}", 'PATCH', $dataToPatch);
}

function obtenerSiguienteIdUsuario()
{
    $todosLosUsuaris = makeApiRequest("/usuaris", 'GET');
    if (!$todosLosUsuaris)
        return 1;

    $maxId = 0;
    foreach ($todosLosUsuaris as $usuari) {
        $idActual = intval($usuari['id'] ?? 0);
        if ($idActual > $maxId)
            $maxId = $idActual;
    }
    return $maxId + 1;
}
?>