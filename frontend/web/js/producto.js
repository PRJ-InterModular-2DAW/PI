document.addEventListener("DOMContentLoaded", () => {
  const params = new URLSearchParams(window.location.search);
  const productoId = params.get("id");

  if (!productoId) {
    document.getElementById("producto-container").innerHTML =
      '<div class="alert alert-danger mx-auto">Producto no especificado</div>';
    return;
  }

  cargarProducto(productoId);
  cargarComentarios(productoId);
  configurarFormulario(productoId);
});

async function cargarProducto(id) {
  try {
    const response = await fetch(`../../backend/api/productos.php?id=${id}`);
    if (!response.ok) throw new Error("Error al cargar producto");
    const producto = await response.json();

    // Renderizar producto
    let imagen = producto.imatge ? `img/productos/${producto.imatge}` : "img/header/logo.png";

    const html = `
            <div class="col-md-6 mb-4">
                <img src="${imagen}" class="img-fluid rounded shadow-sm" alt="${producto.nom
      }" style="max-height: 400px; object-fit: contain;">
            </div>
            <div class="col-md-6 text-start">
                <h1 class="fw-bold">${producto.nom}</h1>
                <p class="text-muted">SKU: ${producto.sku}</p>
                <h2 class="text-primary mb-4">${producto.preu} €</h2>
                <p class="lead">${producto.descripcio}</p>
                <p class="mt-2"><strong>Stock disponible:</strong> ${producto.estoc
      }</p>
                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-dark btn-lg">Añadir al carrito</button>
                    ${producto.estoc < 5
        ? '<div class="alert alert-warning mt-2">¡Quedan pocas unidades!</div>'
        : ""
      }
                </div>
            </div>
        `;
    document.getElementById("producto-container").innerHTML = html;
  } catch (error) {
    console.error(error);
    document.getElementById("producto-container").innerHTML =
      '<div class="alert alert-danger">No se pudo cargar el producto.</div>';
  }
}

async function cargarComentarios(id) {
  const lista = document.getElementById("lista-comentarios");
  lista.innerHTML =
    '<div class="spinner-border spinner-border-sm"></div> Cargando...';

  try {
    const response = await fetch(
      `../../backend/api/comentarios.php?producto_id=${id}`
    );
    const comentarios = await response.json();

    lista.innerHTML = "";
    if (comentarios.length === 0) {
      lista.innerHTML =
        '<p class="text-muted fst-italic">Aún no hay opiniones. ¡Sé el primero en comentar!</p>';
      return;
    }

    comentarios.forEach((c) => {
      const estrellas = "⭐".repeat(c.puntuacio);
      const fecha = new Date(c.fecha).toLocaleDateString();

      const card = document.createElement("div");
      card.className = "card mb-3 border-0 shadow-sm";
      card.innerHTML = `
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="card-subtitle fw-bold text-dark mb-0">${c.nom_usuari}</h6>
                        <small class="text-muted">${fecha}</small>
                    </div>
                    <div class="mb-2 text-warning">${estrellas}</div>
                    <p class="card-text">${c.comentario}</p>
                </div>
            `;
      lista.appendChild(card);
    });
  } catch (error) {
    console.error(error);
    lista.innerHTML = '<p class="text-danger">Error cargando comentarios.</p>';
  }
}

function configurarFormulario(id) {
  const form = document.getElementById("form-comentario");
  const msgContainer = document.getElementById("mensaje-form");

  form.addEventListener("submit", async (e) => {
    e.preventDefault();

    const comentario = document.getElementById("comentario-texto").value;
    const puntuacion = document.getElementById("puntuacion").value;

    // Limpiar mensajes anteriores
    msgContainer.innerHTML = "";
    msgContainer.className = "mt-2";

    try {
      const response = await fetch("../../backend/api/comentarios.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          producto_id: id,
          comentario: comentario,
          puntuacio: puntuacion,
        }),
      });

      const result = await response.json();

      if (response.ok) {
        // Éxito
        msgContainer.textContent = "¡Gracias por tu opinión!";
        msgContainer.className = "mt-2 alert alert-success";
        form.reset();
        cargarComentarios(id); // Recargar lista
      } else {
        // Error (probablemente 401 no logueado)
        if (response.status === 401) {
          // Muestro alerta pero tambien podria abrir el modal
          msgContainer.innerHTML =
            'Debes iniciar sesión para comentar. <a href="#" id="abrirModalComentario">Click aquí</a>';
          msgContainer.className = "mt-2 alert alert-warning";

          document
            .getElementById("abrirModalComentario")
            .addEventListener("click", (e) => {
              e.preventDefault();
              document.getElementById("authModal").classList.add("active");
            });
        } else {
          throw new Error(result.error || "Error desconocido");
        }
      }
    } catch (error) {
      msgContainer.textContent = "Error al enviar: " + error.message;
      msgContainer.className = "mt-2 alert alert-danger";
    }
  });
}
