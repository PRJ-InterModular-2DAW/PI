<?php
// backend/auth/login.php

// Incluir las funciones de conexión para interactuar con la API
require_once '../includes/json_connect.php'; 

$errores = [];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    // Obtener datos del formulario
    $nomUsuari = trim($_POST["nom_usuari"] ?? "");
    $contrasenya_raw = $_POST["contrasenya"] ?? ""; 
    
    // 1. Comprobar si l'usuari existeix (GET /usuaris?nom_usuari=...)
    $usuari = buscarUsuarioPorNombre($nomUsuari);

    if (!$usuari) {
        $errores[] = "Nom d'usuari o contrasenya incorrectes.";
    } else {
        // 2. Validar la contrasenya amb password_verify()
        if (password_verify($contrasenya_raw, $usuari['contrasenya'])) {
            
            // 3. Si és correcte: Crear sessió i cookie
            
            // Buena Práctica: Regenerar ID para evitar Session Hijacking
            session_regenerate_id(true); 
            
            $_SESSION['user_id'] = $usuari['id'];
            $_SESSION['nom_usuari'] = $usuari['nom_usuari'];
            
            // Crear una cookie d'identificació (Guardar una cookie d'identificació (setcookie('user_id', $usuari['id'], time()+3600, "/")))
            setcookie('user_id', $usuari['id'], time() + 3600, "/"); 
            
            // Redirigir al perfil (profile.php está en el mismo directorio)
            header("Location: profile.php"); 
            exit;

        } else {
            $errores[] = "Nom d'usuari o contrasenya incorrectes.";
        }
    }
    
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Inici de Sessió</title>
</head>
<body>
    <h1>2. Inici de Sessió</h1>
    
    <?php if (!empty($errores)): ?>
        <h3 style="color: red;">S'han trobat els següents errors:</h3>
        <ul>
        <?php foreach ($errores as $error): ?>
            <li style="color: red;"><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label for="nom_usuari">Nom d'usuari:</label><br>
        <input type="text" id="nom_usuari" name="nom_usuari" value="<?= htmlspecialchars($_POST['nom_usuari'] ?? '') ?>" required><br><br>
        
        <label for="contrasenya">Contrasenya:</label><br>
        <input type="password" id="contrasenya" name="contrasenya" required><br><br>
        
        <button type="submit">Iniciar Sessió</button>
    </form>
    
    <hr>
    <p>No tens un compte? <a href="register.php">Registra't aquí</a>.</p>
</body>
</html>