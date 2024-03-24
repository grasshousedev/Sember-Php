function onHeadingBlockKeyDown(e) {
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

window.addEventListener("htmx:load", () => {
  autogrow(document.querySelectorAll(".heading-block"));

  // Listen to enter key press and add new block
  document.querySelectorAll(".heading-block").forEach((block) => {
    block
      .querySelector("textarea")
      .removeEventListener("keydown", onHeadingBlockKeyDown);
    block
      .querySelector("textarea")
      .addEventListener("keydown", onHeadingBlockKeyDown);
  });
});
