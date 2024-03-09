window.addEventListener("htmx:afterOnLoad", () => {
  autogrow(document.querySelectorAll(".code-block"));
});