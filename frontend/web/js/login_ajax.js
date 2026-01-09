// js/login_ajax.js
document.addEventListener("DOMContentLoaded", () => {
  configurarLoginAJAX();
  configurarRegisterAJAX();
});

function configurarLoginAJAX() {
  const loginForm = document.querySelector("#formLogin form");
  if (!loginForm) return;

  // Crear contenedor de mensajes si no existe
  let msgDiv = loginForm.querySelector(".login-message");
  if (!msgDiv) {
    msgDiv = document.createElement("div");
    msgDiv.className = "login-message text-center mt-3";
    loginForm.appendChild(msgDiv);
  }

  // Remover listeners anteriores para evitar duplicados si se llama varias veces (clonar nodo)
  const newForm = loginForm.cloneNode(true);
  loginForm.parentNode.replaceChild(newForm, loginForm);

  // Volver a seleccionar el nuevo formulario
  const activeForm = document.querySelector("#formLogin form");

  activeForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const btn = activeForm.querySelector('button[type="submit"]');
    const originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = "Enviando...";

    // Buscar el msgDiv de nuevo dentro del nuevo form
    let currentMsgDiv = activeForm.querySelector(".login-message");
    if (!currentMsgDiv) {
      // Recrrear si se perdió al clonar (aunque cloneNode(true) copia todo)
      currentMsgDiv = document.createElement("div");
      currentMsgDiv.className = "login-message text-center mt-3";
      activeForm.appendChild(currentMsgDiv);
    }

    currentMsgDiv.textContent = "";
    currentMsgDiv.className = "login-message text-center mt-3";

    const formData = new FormData(activeForm);
    const data = Object.fromEntries(formData.entries());

    try {
      const response = await fetch("../../backend/api/login.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
      });

      const result = await response.json();

      if (result.success) {
        currentMsgDiv.textContent = "¡Bienvenido! Redirigiendo a tu perfil...";
        currentMsgDiv.classList.add("text-success");
        setTimeout(() => {
          window.location.href = "../../backend/auth/profile.php";
        }, 1000);
      } else {
        currentMsgDiv.textContent = result.error || "Error al iniciar sesión";
        currentMsgDiv.classList.add("text-danger");
        btn.disabled = false;
        btn.textContent = originalText;
      }
    } catch (error) {
      console.error(error);
      currentMsgDiv.textContent = "Error de conexión";
      currentMsgDiv.classList.add("text-danger");
      btn.disabled = false;
      btn.textContent = originalText;
    }
  });
}

function configurarRegisterAJAX() {
    // Buscar formulario dentro del div #formRegister
    const registerForm = document.querySelector('#formRegister form');
    if (!registerForm) return;

    // Contenedor mensajes
    let msgDiv = registerForm.querySelector('.register-message');
    if (!msgDiv) {
        msgDiv = document.createElement('div');
        msgDiv.className = 'register-message text-center mt-3';
        registerForm.appendChild(msgDiv);
    }

    // Limpiar listeners viejos clonando
    const newForm = registerForm.cloneNode(true);
    registerForm.parentNode.replaceChild(newForm, registerForm);
    
    // Seleccionar formulario activo
    const activeForm = document.querySelector('#formRegister form');

    activeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btn = activeForm.querySelector('button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'Registrando...';

        // Recuperar msgDiv del nuevo form
        let currentMsgDiv = activeForm.querySelector('.register-message');
        if (!currentMsgDiv) {
             currentMsgDiv = document.createElement('div');
             currentMsgDiv.className = 'register-message text-center mt-3';
             activeForm.appendChild(currentMsgDiv);
        }
        currentMsgDiv.textContent = '';
        currentMsgDiv.className = 'register-message text-center mt-3';

        const formData = new FormData(activeForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('../../backend/api/register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                currentMsgDiv.textContent = '¡Registro completado! Ve a iniciar sesión.';
                currentMsgDiv.classList.add('text-success');
                
                // Opcional: Limpiar formulario
                activeForm.reset();

                // Cambiar a pestaña login automáticamente tras 1.5s
                setTimeout(() => {
                    const tabLogin = document.getElementById('tabLogin');
                    if (tabLogin) tabLogin.click();
                }, 1500);

            } else {
                currentMsgDiv.textContent = result.error || 'Error al registrar';
                currentMsgDiv.classList.add('text-danger');
            }
        } catch (error) {
            console.error(error);
            currentMsgDiv.textContent = 'Error de conexión';
            currentMsgDiv.classList.add('text-danger');
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    });
}
