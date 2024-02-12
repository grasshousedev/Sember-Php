function codeBlockDebounce(f, ms) {
  let timer;

  return (...args) => {
    clearTimeout(timer);

    timer = setTimeout(() => {
      f(...args);
    }, ms);
  };
}

htmx.on("htmx:afterSwap", (evt) => {
  if (!evt.target.id || evt.target.id !== "editor") return;

  document.querySelectorAll(".code-block").forEach((el) => {
    const blockId = el.getAttribute("data-id");
    const cm = CodeMirror.fromTextArea(el.querySelector(".code-textarea"), {
      lineNumbers: true,
      theme: "idea",
    });

    function codeBlockSave(value) {
      const el = document.querySelector(
        `[data-id="${blockId}"] .code-textarea`,
      );
      el.value = value;
      el.dispatchEvent(new Event("input"));
    }

    const debouncedSave = codeBlockDebounce(codeBlockSave, 500);

    cm.on("change", (e) => {
      debouncedSave(e.getValue());
    });
  });
});

// htmx.on("htmx:afterSwap", () => {
//   instances = [];
//
//   document.querySelectorAll(".code-block").forEach((el) => {
//     el.querySelector(".CodeMirror").remove();
//   });
// });
