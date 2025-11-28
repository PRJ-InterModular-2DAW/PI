<?php
// backend/auth/register.php

// Incluir las funciones de conexión para interactuar con la API
// Asumimos que json_connect.php está en la carpeta de al lado (../includes/)
require_once '../includes/json_connect.php';

$errores = [];
$missatge = '';

// Se asume que el formulario de registro usa el método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. RECOLLIDA DE DADES
    $nomUsuari = trim($_POST["nom_usuari"] ?? "");
    $contrasenya = $_POST["contrasenya"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $nom = trim($_POST["nom"] ?? "");
    $cognoms = trim($_POST["cognoms"] ?? "");

    // 2. VALIDACIÓ DE CAMPS BUITS
    if (empty($nomUsuari)) {
        $errores[] = "El nom d'usuari és obligatori.";
    }
    if (empty($contrasenya)) {
        $errores[] = "La contrasenya és obligatòria.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correu electrònic no és vàlid o està buit.";
    }
    // 'Nom' y 'cognoms' pueden ser opcionales, pero los incluimos si son necesarios

    // Si no hay errores de formato/campos vacíos, procedemos a la validación de duplicados
    if (empty($errores)) {

        // Validar que no hi haja usuaris duplicats (GET /usuaris?nom_usuari=...)
        if (buscarUsuarioPorNombre($nomUsuari)) {
            $errores[] = "Aquest nom d'usuari ja existeix. Tria'n un altre.";
        }
    }

    // Si no hay errores, se procede al registro
    if (empty($errores)) {

        // --- NUEVO: Calcular el ID siguiente ---
        $nuevoId = obtenerSiguienteIdUsuario();

        // 3. CIFRAR LA CONTRASENYA I CONSTRUIR EL PAYLOAD
        $data = [
            "id" => (string) $nuevoId, // Lo enviamos como string "2", "3" para mantener coherencia
            "nom_usuari" => $nomUsuari,
            "contrasenya" => password_hash($contrasenya, PASSWORD_DEFAULT),
            "email" => $email,
            "nom" => $nom,
            "cognoms" => $cognoms,
            "data_registre" => date('c')
        ];

        // 4. Enviar una petició POST /usuaris al JSON Server
        $usuario_registrado = registrarUsuario($data);

        if ($usuario_registrado) {
            $missatge = "Registre completat amb èxit! Ja pots iniciar sessió.";
            // Si el registro es exitoso, borramos las variables de formulario para dejar los campos vacíos
            $nomUsuari = $email = $nom = $cognoms = '';
        } else {
            $errores[] = "Error al connectar amb el servidor o registrar l'usuari.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registre - L&SSHOP</title>
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
            <a href="login.php" class="tab">Iniciar sesión</a>
            <a href="#" class="tab active">Registrarte</a>
        </div>

        <?php if ($missatge): ?>
            <div class="success-msg">
                <p><i class="fas fa-check-circle"></i> <?= htmlspecialchars($missatge) ?></p>
            </div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="error-msg">
                <?php foreach ($errores as $error): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="register.php" class="login-form">
            <div class="form-group">
                <label for="nom_usuari">Nom d'usuari</label>
                <input type="text" id="nom_usuari" name="nom_usuari" class="form-input" value="<?= htmlspecialchars($nomUsuari ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="form-input" value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="contrasenya">Contrasenya</label>
                <input type="password" id="contrasenya" name="contrasenya" class="form-input" required>
            </div>

            <div class="form-group">
                <label for="nom">Nom (Opcional)</label>
                <input type="text" id="nom" name="nom" class="form-input" value="<?= htmlspecialchars($nom ?? '') ?>">
            </div>

            <div class="form-group">
                <label for="cognoms">Cognoms (Opcional)</label>
                <input type="text" id="cognoms" name="cognoms" class="form-input" value="<?= htmlspecialchars($cognoms ?? '') ?>">
            </div>

            <button type="submit" class="btn-submit">Registrar-se</button>
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