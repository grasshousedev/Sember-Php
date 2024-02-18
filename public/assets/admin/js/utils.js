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

function updateTextareaSize(e) {
  e.target.style.height = `0px`;
  e.target.value = e.target.value.trim();
  e.target.style.height = `${e.target.scrollHeight}px`;
}

function autogrow(els) {
  els.forEach((el) => {
    const textarea = el.querySelector("textarea");

    textarea.style.boxSizing = "border-box";
    textarea.style.height = parseInt(getComputedStyle(textarea).lineHeight, 10);
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.removeEventListener("input", updateTextareaSize);
    textarea.addEventListener("input", updateTextareaSize);
  });
}
