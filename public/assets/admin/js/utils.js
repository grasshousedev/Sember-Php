function debounce(f, ms) {
  let timer;

  return (...args) => {
    clearTimeout(timer);

    timer = setTimeout(() => {
      f(...args);
    }, ms);
  };
}

function autogrow(els) {
  els.forEach((el) => {
    const textarea = el.querySelector("textarea");
    let scrollPos = window.scrollY;

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener("input", (_) => {
      scrollPos = window.scrollY;

      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });

    const ro = new ResizeObserver((entries) => {
      if (scrollPos > 0) {
        window.scrollTo(0, scrollPos);
      }
    });

    ro.observe(textarea);
  });
}
