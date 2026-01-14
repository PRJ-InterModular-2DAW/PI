document.addEventListener("DOMContentLoaded", function () {
  const hamburgerBtn = document.getElementById("hamburgerBtn");

  // Seleccionar todos los elementos de navegación que están ocultos por defecto en móvil
  // Más los nuevos elementos extra para móvil
  const navItems = [
    document.getElementById("nav-mujer"),
    document.getElementById("nav-hombre"),
    ...document.querySelectorAll(".mobile-extra"),
  ];

  if (hamburgerBtn) {
    hamburgerBtn.addEventListener("click", function () {
      navItems.forEach((item) => {
        if (item) {
          // Lógica de alternancia (Toggle)
          // Si el elemento tiene style.display en block, lo ocultamos.
          // De lo contrario, lo mostramos.
          // Nota: usamos style.display aquí asumiendo que el CSS !important fue eliminado
          // O lo forzamos. Dado que el mediaQuery tenía !important eliminado, esto debería funcionar.
          // Si no, recurrimos a setProperty.

          if (item.style.display === "block") {
            item.style.display = "none";
          } else {
            item.style.display = "block";
          }
        }
      });
    });
  }

  // Listener de redimensionado de ventana para arreglar problemas de vista de escritorio
  // Si el usuario abre el menú en móvil (display: block en línea) -> luego redimensiona a escritorio,
  // queremos ELIMINAR ese estilo en línea para que el CSS de escritorio tome el control (display: block o none según corresponda).
  window.addEventListener("resize", function () {
    if (window.innerWidth > 768) {
      navItems.forEach((item) => {
        if (item) {
          item.style.display = ""; // Limpia el estilo en línea
        }
      });
    }
  });
});
