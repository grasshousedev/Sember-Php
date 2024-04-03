function selectImage(e) {
  const blockId = e.target.parentElement.getAttribute("data-id");
  const postId = e.target.parentElement.getAttribute("data-post-id");

  e.target.parentElement.insertAdjacentHTML(
    "beforeend",
    `<input 
            type="file" 
            accept="image/*" 
            name="file" 
            class="hidden"
            hx-encoding="multipart/form-data"
            hx-post="/admin/api/post/${postId}/blocks/${blockId}/opt/upload" 
            hx-trigger="input changed"
            hx-swap="outerHTML"
            hx-target=".blocks" />`,
  );

  const input = e.target.parentElement.querySelector("input[type=file]");

  htmx.process(input);
  input.click();
}

htmx.on("htmx:afterSettle", function (evt) {
  const imageBlocks = document.querySelectorAll(".image-block");

  imageBlocks.forEach((el) => {
    if (!el.querySelector(".upload-image")) return;

    el.querySelector(".upload-image").removeEventListener("click", selectImage);
    el.querySelector(".upload-image").addEventListener("click", selectImage);
  });
});
