<?php
// backend/test_db.php
require_once 'includes/json_connect.php';

echo "<h1>Test de Conexión a Base de Datos (Local)</h1>";

// 1. Verificar ruta definida
echo "<p>Ruta esperada del JSON: <code>" . JSON_DB_PATH . "</code></p>";

// 2. Verificar si existe
if (file_exists(JSON_DB_PATH)) {
    echo "<p style='color:green'>✅ El archivo EXISTE.</p>";
    echo "<p>Permisos: " . substr(sprintf('%o', fileperms(JSON_DB_PATH)), -4) . "</p>";
} else {
    echo "<p style='color:red'>❌ El archivo NO EXISTE en esa ruta.</p>";
    // Intentar averiguar ruta real
    echo "<p>Directorio actual (__DIR__): " . __DIR__ . "</p>";
}

// 3. Intentar leer usuarios
$u = leerDbLocal();
echo "<h3>Contenido leído (resumen):</h3>";
echo "<pre>";
if (isset($u['usuaris'])) {
    echo "Usuarios encontrados: " . count($u['usuaris']) . "\n";
    foreach ($u['usuaris'] as $user) {
        echo "- ID: {$user['id']}, User: {$user['nom_usuari']}, Pass (hash): " . substr($user['contrasenya'], 0, 10) . "...\n";
    }
} else {
    echo "No se encontró array 'usuaris'.\n";
    print_r($u);
}
echo "</pre>";

// 4. Testear contraseña '1234' contra usuario 'test'
echo "<h3>Prueba de validación de contraseña:</h3>";
foreach ($u['usuaris'] as $user) {
    if ($user['nom_usuari'] === 'test') {
        $hash = $user['contrasenya'];
        $check = password_verify('1234', $hash);
        echo "Usuario 'test' encontrado.<br>";
        echo "Hash: $hash<br>";
        echo "Verificando contra '1234': " . ($check ? "<b style='color:green'>CORRECTO</b>" : "<b style='color:red'>INCORRECTO</b>");
    }
}
?>