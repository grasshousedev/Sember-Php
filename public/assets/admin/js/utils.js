function debounce(f, ms) {
  let timer;

  return (...args) => {
    clearTimeout(timer);

    timer = setTimeout(() => {
      f(...args);
    }, ms);
  };
}

function updateTextareaSize(e) {
  e.target.style.height = `0px`;
  e.target.style.height = `${e.target.scrollHeight}px`;
}

function autogrow(els) {
  els.forEach((el) => {
    console.log(el);
    const textarea = el.querySelector("textarea");

    textarea.style.boxSizing = "border-box";
    textarea.style.height = '0px';
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.removeEventListener("input", updateTextareaSize);
    textarea.addEventListener("input", updateTextareaSize);
  });
}
