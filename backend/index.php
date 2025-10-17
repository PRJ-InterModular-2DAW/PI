<?php
if($_SERVER["REQUEST_METHOD"] === "POST"){
    $nombre = trim($_POST["nombre"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $modulo = trim($_POST["module"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
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

    if (!empty($phone) && !preg_match("/^[0-9-+\s-()]+$/", $phone)) {
        $errores[] = "El numero de telefono solo se permiten numeros";
    }

    if (empty($errores)) {
        echo "<h2>Formulario enviado correctamente</h2>";
        echo "<p><b>Nombre:</b> " . htmlspecialchars($nombre) . "</p>";
        echo "<p><b>Email:</b> " . htmlspecialchars($email) . "</p>";
        echo "<p><b>Modulo:</b> " . htmlspecialchars($modulo) . "</p>";
       echo "<p><b>Telefono:</b> " . htmlspecialchars($phone) . "</p>";
    } else {
        echo "<h3>Se han encontrado los siguientes errores:</h3><ul>";
        foreach ($errores as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
    }
}
?>