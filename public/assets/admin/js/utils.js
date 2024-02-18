function autogrow(els) {
  els.forEach((el) => {
    const textarea = el.querySelector("textarea");

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener("input", (_) => {
      const scrollPos = window.scrollY;

      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;

      window.scrollTo(0, scrollPos + 10);
    });
  });
}
