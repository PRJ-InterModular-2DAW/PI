<?php
// backend/auth/login.php

require_once '../includes/json_connect.php'; 

$errores = [];

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nomUsuari = trim($_POST["nom_usuari"] ?? "");
    $contrasenya_raw = $_POST["contrasenya"] ?? ""; 
    
    $usuari = buscarUsuarioPorNombre($nomUsuari);

    if (!$usuari) {
        $errores[] = "Nom d'usuari o contrasenya incorrectes.";
    } else {
        if (password_verify($contrasenya_raw, $usuari['contrasenya'])) {
            session_regenerate_id(true); 
            $_SESSION['user_id'] = $usuari['id'];
            $_SESSION['nom_usuari'] = $usuari['nom_usuari'];
            setcookie('user_id', $usuari['id'], time() + 3600, "/"); 
            
            header("Location: profile.php"); 
            exit;
        } else {
            $errores[] = "Nom d'usuari o contrasenya incorrectes.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - L&SSHOP</title>
    <!-- Fuente Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Enlace al CSS que acabamos de crear -->
    <link rel="stylesheet" href="#">
</head>
<body>

    <div class="login-container">
        <h1>Login</h1>

        <?php if (!empty($errores)): ?>
            <div class="error-msg">
                <?php foreach ($errores as $error): ?>
                    <p style="margin:0;"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="input-group">
                <label for="nom_usuari">Usuario</label>
                <div class="input-wrapper">
                    <i class="fas fa-user"></i>
                    <input type="text" id="nom_usuari" name="nom_usuari" placeholder="Escribe tu usuario" value="<?= htmlspecialchars($_POST['nom_usuari'] ?? '') ?>" required>
                </div>
            </div>

            <div class="input-group">
                <label for="contrasenya">Contraseña</label>
                <div class="input-wrapper">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="contrasenya" name="contrasenya" placeholder="Escribe tu contraseña" required>
                </div>
            </div>

            <a href="#" class="forgot-pass">¿Olvidaste la contraseña?</a>

            <button type="submit" class="btn-submit">LOGIN</button>
        </form>

        <p class="social-text">O inicia sesión usando</p>
        <div class="social-icons">
            <a href="#" class="social-circle facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" class="social-circle twitter"><i class="fab fa-twitter"></i></a>
            <a href="#" class="social-circle google"><i class="fab fa-google"></i></a>
        </div>

        <div class="signup-link">
            ¿No tienes cuenta? <a href="register.php">Regístrate</a>
        </div>
        <div class="signup-link" style="margin-top: 15px;">
            <a href="/web/index.html"><i class="fas fa-arrow-left"></i> Volver a la tienda</a>
        </div>
    </div>

</body>
</html>