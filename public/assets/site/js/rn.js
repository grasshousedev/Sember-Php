import { annotate } from "/assets/site/js/rough-notation.esm.js";

let annotatedLinks = [];

function annotateLinks(els) {
  annotatedLinks.forEach((a) => a.remove());

  els.forEach((e) => {
    const annotation = annotate(e, { type: "underline" });
    annotation.show();
    annotatedLinks.push(annotation);
  });
}

let annotatedStrikes = [];

function annotateStrikes(els) {
  annotatedStrikes.forEach((a) => a.remove());

  els.forEach((e) => {
    const annotation = annotate(e, { type: "strike-through" });
    annotation.show();
    annotatedStrikes.push(annotation);
  });
}

let annotatedCode = [];

function annotateCode(els) {
  annotatedCode.forEach((a) => a.remove());

  els.forEach((e) => {
    const annotation = annotate(e, {
      type: "highlight",
      color: "#FDDB29",
      multiline: true,
    });
    annotation.show();
    annotatedCode.push(annotation);
  });
}

function annotateEls() {
  annotateLinks([
    ...document.querySelectorAll(".body a"),
    ...document.querySelectorAll(".about-section a"),
  ]);

  annotateStrikes(document.querySelectorAll(".body del"));
  annotateCode(document.querySelectorAll(".body p code"));
}

window.addEventListener("load", () => {
  annotateEls();

  const ro = new ResizeObserver((entries) => {
    for (let entry of entries) {
      annotateEls();
    }
  });

  ro.observe(document.querySelector(".posts"));
  ro.observe(document.querySelector("body"));
  ro.observe(document.querySelector(".about-section"));
});
