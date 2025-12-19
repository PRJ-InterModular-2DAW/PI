// auth_ui.js
document.addEventListener("DOMContentLoaded", async () => {
  try {
    const response = await fetch("../../backend/api/session_status.php");
    const session = await response.json();

    if (session.logged_in) {
      actualizarIconoPerfil(session.user);
    }
  } catch (error) {
    console.error("Error verificando sesión:", error);
  }
});

function actualizarIconoPerfil(user) {
  // Buscar el contenedor de header-right
  const headerRight = document.querySelector(".header-right");
  if (!headerRight) return;

  // Buscar el enlace de login actual
  let loginLink = headerRight.querySelector('a[href*="login.php"]');

  // Si no está el <a> pero sí imagen (caso raro), buscamos imagen
  if (!loginLink) {
    const img = headerRight.querySelector('img[src*="iconoPerfil.png"]');
    if (img) loginLink = img.parentElement;
  }

  if (loginLink && loginLink.tagName === "A") {
    // En lugar de crear un menú complejo, simplemente:
    // 1. Cambiamos el href para apuntar a profile.php
    loginLink.href = "../../backend/auth/profile.php";

    // 2. Modificamos visualmente si queremos (tooltip)
    loginLink.title = `Ir al perfil de ${user.nom_usuari}`;

    // 3. IMPORTANTE: Clonar el nodo para eliminar listeners anteriores (del modal)
    // Si el modal se activaba por clase o listener en este elemento, necesitamos limpiarlo.
    // Pero en tu código inicial el listener estaba en: 'a[href="../../backend/auth/login.php"]'
    // Al cambiar el href, el script del modal (si busca por href exacto) dejará de funcionar automáticamente.
    // Vamos a verificar si el modal script busca por href exacto.
    // Sí: const openBtn = document.querySelector('a[href="../../backend/auth/login.php"]');

    // Así que con cambiar el href basta para que el modal deje de interceptarlo.
  }
}
