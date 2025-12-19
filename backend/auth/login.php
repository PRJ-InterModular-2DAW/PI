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
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Enlace al CSS -->
    <link rel="stylesheet" href="../../web/css/login-style.css">
</head>
<body>

    <div class="overlay"></div>

    <div class="login-card">
        <h2 class="login-title">Entra en <span class="brand-thin">L&</span><span class="brand-bold">SS</span>HOP</h2>

        <div class="auth-tabs">
            <a href="#" class="tab active">Iniciar sesión</a>
            <a href="register.php" class="tab">Registrarte</a>
        </div>

        <?php if (!empty($errores)): ?>
            <div class="error-msg">
                <?php foreach ($errores as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="login-form">
            <div class="form-group">
                <label for="nom_usuari">Correo electrónico</label>
                <input type="text" id="nom_usuari" name="nom_usuari" class="form-input" value="<?= htmlspecialchars($_POST['nom_usuari'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="contrasenya">Contraseña</label>
                <input type="password" id="contrasenya" name="contrasenya" class="form-input" required>
            </div>

            <a href="#" class="forgot-pass">¿Has olvidado tu contraseña?</a>

            <button type="submit" class="btn-submit">Iniciar sesión</button>
        </form>

        <div class="separator">
            <span>o</span>
        </div>

        <div class="social-login">
            <button class="btn-social google">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Google_%22G%22_Logo.svg" alt="Google">
                Google
            </button>
            <button class="btn-social apple">
                <i class="fab fa-apple"></i>
                Apple
            </button>
        </div>
    </div>

</body>
</html>