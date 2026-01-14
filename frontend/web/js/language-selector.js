// Alternar desplegable del selector de idioma
document.addEventListener("DOMContentLoaded", function () {
  const languageSelector = document.querySelector(".language-selector");
  // Icono (imagen) para cambiar el src
  const languageIcon = document.getElementById("languageIcon");
  // Trigger (botón) para el evento click
  const languageTrigger =
    document.getElementById("languageTrigger") || languageIcon;

  const languageDropdown = document.querySelector(".language-dropdown");
  const languageOptions = document.querySelectorAll(
    ".language-dropdown a[data-lang]"
  );

  if (languageTrigger && languageSelector) {
    // Alternar desplegable al hacer clic
    languageTrigger.addEventListener("click", function (e) {
      e.stopPropagation();
      languageSelector.classList.toggle("active");
    });

    // Cerrar desplegable al hacer clic fuera
    document.addEventListener("click", function (e) {
      if (!languageSelector.contains(e.target)) {
        languageSelector.classList.remove("active");
      }
    });

    // Manejar selección de idioma
    languageOptions.forEach((option) => {
      option.addEventListener("click", function (e) {
        e.preventDefault();
        const selectedLang = this.getAttribute("data-lang");
        const selectedFlag = this.querySelector("img").src;

        // Actualizar el icono de la bandera principal
        languageIcon.src = selectedFlag;

        // Cerrar desplegable
        languageSelector.classList.remove("active");

        // Aquí puedes agregar lógica para cambiar realmente el idioma
        console.log("Language selected:", selectedLang);
        // Ejemplo: window.location.href = `?lang=${selectedLang}`;
      });
    });
  }
});
