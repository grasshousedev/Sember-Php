window.addEventListener("load", () => {
  const toggles = document.querySelectorAll(".toggle-about-section");

  toggles.forEach((el) => {
    el.addEventListener("click", () => {
      document.querySelector("body").classList.toggle("js-about-section-open");
    });
  });
});
