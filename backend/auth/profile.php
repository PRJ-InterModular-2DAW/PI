<?php
// backend/auth/profile.php
require_once '../includes/json_connect.php'; 
session_set_cookie_params(0, '/'); // Asegurar acceso a sesión global
if (session_status() === PHP_SESSION_NONE) {
     session_start();
}

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    // Si no hay sessión, fuera
    header("Location: ../../web/index.html"); 
    exit;
}

$userData = obtenerUsuarioPorId($user_id);

if (!$userData) {
    session_destroy();
    header("Location: ../../web/index.html?error=user_not_found");
    exit;
}

$message = '';
$msgType = ''; // success o error

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    
    $newEmail = trim($_POST['email'] ?? $userData['email']);
    $newNom = trim($_POST['nom'] ?? $userData['nom']);
    $newCognoms = trim($_POST['cognoms'] ?? $userData['cognoms']);
    
    // Validar un poco
    if (empty($newEmail)) {
        $message = "El correo no puede estar vacío.";
        $msgType = "error";
    } else {
        $dataToPatch = [
            'email' => $newEmail,
            'nom' => $newNom,
            'cognoms' => $newCognoms
        ];
    
        $updatedData = actualizarUsuarioPatch($user_id, $dataToPatch);
    
        if ($updatedData) {
            $message = "Datos actualizados correctamente.";
            $msgType = "success";
            $userData = $updatedData; // Refrescar datos
            // Actualizar sesión si el nombre cambió
            if (isset($updatedData['nom_usuari'])) {
                $_SESSION['nom_usuari'] = $updatedData['nom_usuari'];
            }
        } else {
            $message = "Error al actualizar. Inténtalo más tarde.";
             $msgType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - L&SSHOP</title>
    <!-- Fuentes -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos PRINCIPALES -->
    <link rel="stylesheet" href="../../web/css/estilos.css">
</head>
<body class="profile-page">

    <!-- Header Simplificado para perfil -->
    <header style="padding: 15px 30px; display:flex; justify-content: space-between; align-items: center; background: #EAE3D2;">
        <div class="logo">
             <!-- Ruta corregida a /web/ -->
             <a href="../../web/index.html" style="font-size: 24px; font-weight: bold; color: black; text-decoration: none;">
                L&<span style="font-weight: 900;">SS</span>HOP
             </a>
        </div>
        <div class="actions">
            <!-- Ruta corregida a /web/ -->
            <a href="../../web/index.html" style="text-decoration: none; color: black; font-weight: 600;">
                <i class="fas fa-store"></i> Volver a la Tienda
            </a>
        </div>
    </header>

    <div class="profile-container">
        <h1>Mi Perfil</h1>

        <?php if ($message): ?>
            <div style="padding: 15px; margin-bottom: 20px; border-radius: 4px; 
                 background-color: <?= $msgType == 'success' ? '#d4edda' : '#f8d7da' ?>;
                 color: <?= $msgType == 'success' ? '#155724' : '#721c24' ?>;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="welcome-msg" style="text-align: center; margin-bottom: 20px;">
            <p>Hola, <strong><?= htmlspecialchars($userData['nom_usuari']) ?></strong>. Aquí puedes gestionar tus datos personales.</p>
        </div>

        <form method="POST" action="profile.php" class="profile-form">
            <input type="hidden" name="action" value="update_profile">
            
            <div style="grid-column: 1 / -1;">
                <h2>Información Personal</h2>
            </div>

            <div class="form-group">
                <label for="nom_usuari">Usuario</label>
                <input type="text" value="<?= htmlspecialchars($userData['nom_usuari']) ?>" disabled style="background-color: #f9f9f9; color: #888;">
                <small style="color: #999;">El nombre de usuario no se puede cambiar.</small>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nom">Nombre</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($userData['nom'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="cognoms">Apellidos</label>
                <input type="text" id="cognoms" name="cognoms" value="<?= htmlspecialchars($userData['cognoms'] ?? '') ?>">
            </div>
            
            <button type="submit">Actualizar Datos</button>
        </form>

        <div class="profile-actions">
            <!-- Ruta corregida a /web/ -->
            <a href="../../web/index.html" class="btn-home">
                <i class="fas fa-arrow-left"></i> Volver al Inicio
            </a>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
            </a>
        </div>
    </div>

    <!-- Footer rápido -->
    <footer style="text-align: center; padding: 20px; color: #666; font-size: 0.9rem;">
        &copy; 2025 L&SSHOP. Todos los derechos reservados.
    </footer>

</body>
</html>