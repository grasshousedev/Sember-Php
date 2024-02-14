function autogrow(els) {
  els.forEach((el) => {
    const textarea = el.querySelector("textarea");

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener("input", (_) => {
      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  });
}
