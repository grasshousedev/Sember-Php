htmx.on("htmx:afterSettle", () => {
  autogrow(document.querySelectorAll(".markdown-block"));
});
