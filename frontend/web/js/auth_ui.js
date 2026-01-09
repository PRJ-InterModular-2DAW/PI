// auth_ui.js
document.addEventListener("DOMContentLoaded", async () => {
  // 1. Chequeo optimista (localStorage)
  const storedSession = localStorage.getItem("user_session");
  if (storedSession) {
    try {
      const user = JSON.parse(storedSession);
      actualizarIconoPerfil(user);
    } catch (e) {
      console.error("Error parseando sesión local", e);
      localStorage.removeItem("user_session");
    }
  }

  // 2. Verificación real con backend
  try {
    const response = await fetch("../../backend/api/session_status.php");
    const session = await response.json();

    if (session.logged_in) {
      // Confirmamos y actualizamos localStorage por si acaso cambió algo
      localStorage.setItem("user_session", JSON.stringify(session.user));
      actualizarIconoPerfil(session.user);
    } else {
      // Si el servidor dice que no hay sesión, borramos local
      if (storedSession) {
        localStorage.removeItem("user_session");
        // Opcional: recargar página o revertir UI si es crítico
        // pero mejor dejarlo así para que el usuario se de cuenta al intentar acción
        revertirIconoPerfil();
      }
    }
  } catch (error) {
    console.error("Error verificando sesión:", error);
  }
});

function revertirIconoPerfil() {
  // Restaurar el enlace de login si se había cambiado
  const headerRight = document.querySelector(".header-right");
  if (!headerRight) return;

  // Buscar el enlace que apunta a profile.php
  const profileLink = headerRight.querySelector('a[href*="profile.php"]');
  if (profileLink) {
    profileLink.href = "../../backend/auth/login.php";
    profileLink.removeAttribute("title");
    // El modal script debería volver a funcionar si busca por href
    // Re-adjuntar evento para abrir modal
    const modal = document.getElementById("authModal");
    if (modal) {
      // Eliminar listeners previos clonando de nuevo (por si acaso)
      const freshLink = profileLink.cloneNode(true);
      freshLink.addEventListener("click", (e) => {
        e.preventDefault();
        modal.classList.add("active");
      });
      profileLink.parentNode.replaceChild(freshLink, profileLink);
    }
  }
}

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
    // Clonamos el nodo para eliminar los Event Listeners del modal (que se adjuntaron al inicio)
    const newLink = loginLink.cloneNode(true);

    newLink.href = "../../backend/auth/profile.php";
    newLink.title = `Ir al perfil de ${user.nom_usuari}`;

    // Reemplazamos en el DOM
    loginLink.parentNode.replaceChild(newLink, loginLink);
  }
}
