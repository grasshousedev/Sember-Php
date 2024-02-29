function onMarkdownBlockKeyDown(e) {
  console.log(e.key);

  if (e.key === "Enter") {
    e.preventDefault();

    const addBlockContainer = e.target.parentElement.nextElementSibling;
    addBlockContainer.querySelector('button[data-key="markdown"]').click();

    return false;
  }

  if (e.key === "Backspace" && e.target.value === "") {
    const blockOptions = e.target.nextElementSibling;
    const deleteButton = blockOptions.querySelector(".js-delete-block");

    deleteButton.click();
  }
}

window.addEventListener("htmx:afterOnLoad", () => {
  autogrow(document.querySelectorAll(".markdown-block"));

  // Listen to enter key press and add new block
  document.querySelectorAll(".markdown-block").forEach((block) => {
    block
      .querySelector("textarea")
      .removeEventListener("keydown", onMarkdownBlockKeyDown);
    block
      .querySelector("textarea")
      .addEventListener("keydown", onMarkdownBlockKeyDown);
  });
});
