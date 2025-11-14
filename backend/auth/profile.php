<?php
// backend/auth/profile.php

// Incluir las funciones de conexión para interactuar con la API
require_once '../includes/json_connect.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php"); 
    exit;
}

// 2. Obtener datos iniciales con la función de includes/
$userData = obtenerUsuarioPorId($user_id);

if (!$userData) {
    session_destroy();
    header("Location: login.php?error=user_not_found");
    exit;
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    
    $newEmail = trim($_POST['email'] ?? $userData['email']);
    $newNom = trim($_POST['nom'] ?? $userData['nom']);
    $newCognoms = trim($_POST['cognoms'] ?? $userData['cognoms']);
    
    $dataToPatch = [
        'email' => $newEmail,
        'nom' => $newNom,
        'cognoms' => $newCognoms
    ];

    // 3. Llamar a la función de actualización de includes/ (PATCH /usuaris/{id})
    $updatedData = actualizarUsuarioPatch($user_id, $dataToPatch);

    if ($updatedData) {
        $message = "Dades actualitzades correctament!";
        $userData = $updatedData; // Actualizar los datos mostrados
    } else {
        $message = "Error: No s'han pogut actualitzar les dades.";
    }
}

?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Perfil d'Usuari</title>
</head>
<body>
    <h1>Benvingut/da, <?= htmlspecialchars($userData['nom_usuari'] ?? 'Usuari') ?></h1>

    <?php if ($message): ?>
        <p style="color: green; font-weight: bold;"><?= $message ?></p>
    <?php endif; ?>

    <h2>Les teves dades actuals:</h2>
    <ul>
        <li>**Nom d'usuari:** <?= htmlspecialchars($userData['nom_usuari']) ?></li>
        <li>**Email actual:** <?= htmlspecialchars($userData['email']) ?></li>
        <li>**Nom:** <?= htmlspecialchars($userData['nom']) ?></li>
        <li>**Cognoms:** <?= htmlspecialchars($userData['cognoms']) ?></li>
    </ul>
    
    <hr>
    
    <h2>Actualitzar Dades</h2>
    <form method="POST" action="profile.php">
        <input type="hidden" name="action" value="update_profile">
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required><br><br>
        
        <label for="nom">Nom:</label><br>
        <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($userData['nom']) ?>" required><br><br>
        
        <label for="cognoms">Cognoms:</label><br>
        <input type="text" id="cognoms" name="cognoms" value="<?= htmlspecialchars($userData['cognoms']) ?>" required><br><br>
        
        <button type="submit">Guardar Canvis</button>
    </form>
    
    <hr>
    <p><a href="logout.php">Tancar Sessió</a></p>
</body>
</html>