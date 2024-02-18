htmx.onLoad(() => {
  // Block list
  const toggles = document.querySelectorAll(".toggle-block-list");

  toggles.forEach((el) => {
    el.addEventListener("click", () => {
      // Close all other options
      toggles.forEach((toggle) => {
        toggle.parentElement.classList.remove("active");
      });

      // Open the clicked options
      el.parentElement.classList.toggle("active");

      // Close the block options when leaving the block list
      el.parentElement
        .querySelector(".block-list")
        .addEventListener("mouseleave", () => {
          el.parentElement.classList.remove("active");
        });
    });
  });

  // Block options
  const blockOptions = document.querySelectorAll(".toggle-block-options");

  blockOptions.forEach((el) => {
    el.addEventListener("click", () => {
      // Close all other options
      blockOptions.forEach((toggle) => {
        toggle.parentElement
          .querySelector(".block-options-menu")
          .classList.remove("active");
      });

      // Open the clicked options
      el.parentElement
        .querySelector(".block-options-menu")
        .classList.toggle("active");

      el.parentElement
        .querySelector(".block-options-menu")
        .addEventListener("mouseleave", () => {
          el.parentElement
            .querySelector(".block-options-menu")
            .classList.remove("active");
        });
    });
  });

  // Post settings
  const postSettingsToggle = document.querySelector(".toggle-post-settings");
  const postSettings = document.querySelector(".post-settings");

  postSettingsToggle.addEventListener("click", () => {
    postSettings.classList.add("active");

    postSettings.addEventListener("mouseleave", () => {
      postSettings.classList.remove("active");
    });
  });
});

// Called after adding a new block, which scrolls the
// new block into view and focuses in the beginning of it.
htmx.on("focusBlockBeginning", (event) => {
  const blockId = event.detail.value;
  const block = document.querySelector(
    '#editor .block[data-id="' + blockId + '"]',
  );

  if (block) {
    if (block.querySelector("textarea")) {
      block.querySelector("textarea").focus();
    }
  }
});

// Called after removing a block, which scrolls the
// previous block into view and focuses in the end of it.
htmx.on("focusBlockEnd", (event) => {
  const blockId = event.detail.value;
  const block = document.querySelector(
    '#editor .block[data-id="' + blockId + '"]',
  );

  if (block) {
    if (block.querySelector("textarea")) {
      const temp_value = block.querySelector("textarea").value;
      block.querySelector("textarea").value = "";
      block.querySelector("textarea").value = temp_value;
      block.querySelector("textarea").focus();
    }
  }
});
