function debounce(f, ms) {
  let timer;

  return (...args) => {
    clearTimeout(timer);

    timer = setTimeout(() => {
      f(...args);
    }, ms);
  };
}

function lines(el) {
  const lineHeight = parseInt(getComputedStyle(el).lineHeight, 10);

  return Math.floor(el.scrollHeight / lineHeight);
}

function autogrow(els) {
  let scrollPos = window.scrollY;

  els.forEach((el) => {
    const textarea = el.querySelector("textarea");

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener("change", (_) => {
      scrollPos = window.scrollY;

      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });

    const ro = new ResizeObserver((_) => {
      if (scrollPos > 0) {
        window.scrollTo(0, scrollPos);
      }
    });

    ro.observe(textarea);
  });
}
