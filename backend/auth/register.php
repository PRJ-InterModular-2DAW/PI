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
    <title>Registre d'Usuari</title>
</head>

<body>
    <h1>1. Registre d'usuari</h1>

    <?php if ($missatge): ?>
        <p style="color: green; font-weight: bold;"><?= htmlspecialchars($missatge) ?></p>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
        <h3 style="color: red;">S'han trobat els següents errors:</h3>
        <ul>
            <?php foreach ($errores as $error): ?>
                <li style="color: red;"><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label for="nom_usuari">Nom d'usuari:</label><br>
        <input type="text" id="nom_usuari" name="nom_usuari" value="<?= htmlspecialchars($nomUsuari ?? '') ?>"
            required><br><br>

        <label for="contrasenya">Contrasenya:</label><br>
        <input type="password" id="contrasenya" name="contrasenya" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required><br><br>

        <label for="nom">Nom (Opcional):</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>"><br><br>

        <label for="cognoms">Cognoms (Opcional):</label><br>
        <input type="text" id="cognoms" name="cognoms" value="<?= htmlspecialchars($cognoms ?? '') ?>"><br><br>

        <button type="submit">Registrar-se</button>
    </form>

    <hr>
    <p>Ja tens un compte? <a href="login.php">Inicia sessió aquí</a>.</p>
</body>

</html>