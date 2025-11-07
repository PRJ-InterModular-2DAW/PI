<?php

// Aquest fitxer ha d'estar a /backend/index.php
if($_SERVER["REQUEST_METHOD"] === "POST"){
    
    // 1. RECOLLIDA I VALIDACIÓ DE DADES
    // -------------------------------------
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $modulo = trim($_POST["module"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $acepto = trim($_POST["acepto"] ?? "");
    $errores = [];

    if (empty($nombre)) {
        $errores[] = "Por favor, escribe tu nombre";
    } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/", $nombre)) {
        $errores[] = "El nombre solo debe contener letras y espacios";
    }

    if (empty($email)) {
        $errores[] = "Por favor, escribe tu correo electronico";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errores[] = "El correo electronico no es valido";
    }

    if (empty($modulo)) {
        $errores[] = "No puedes dejar el modulo vacio";
    }

    if (!empty($phone) && !preg_match("/^[0-9]+$/", $phone)) {
        $errores[] = "El numero de telefono solo se permiten numeros";
    }
    
    if (empty($acepto)) {
        $errores[] = "Debes aceptar los términos y condiciones";
    }

    if (empty($errores)) {
        
        $nombreArchivoSubido = "";

        if (isset($_FILES['fichero']) && $_FILES['fichero']['error'] === UPLOAD_ERR_OK) {
            
            $nombreOriginal = basename($_FILES['fichero']['name']);
            $nombreSeguro = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $nombreOriginal);
            
            $rutaDestino = "../uploads/{$nombreSeguro}"; 

            if (move_uploaded_file($_FILES['fichero']['tmp_name'], $rutaDestino)) {
                $nombreArchivoSubido = $nombreOriginal;
            } else {
                $errores[] = "Error al mover el archivo subido. Revisa los permisos de la carpeta 'uploads'.";
            }
        }

        if (empty($errores)) {
            echo "<h2>Formulario enviado correctamente</h2>";
            echo "<p><b>Nombre:</b> " . htmlspecialchars($nombre) . "</p>";
            echo "<p><b>Email:</b> " . htmlspecialchars($email) . "</p>";
            echo "<p><b>Modulo:</b> " . htmlspecialchars($modulo) . "</p>";
            echo "<p><b>Telefono:</b> " . htmlspecialchars($phone) . "</p>";
            if ($nombreArchivoSubido) {
                echo "<p><b>Archivo subido:</b> " . htmlspecialchars($nombreArchivoSubido) . "</p>";
            }
        }
    }
    
    if (!empty($errores)) {
        echo "<h3>Se han encontrado los siguientes errores:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }

} else {
    header("Location: /index.html");
    exit;
}
?>