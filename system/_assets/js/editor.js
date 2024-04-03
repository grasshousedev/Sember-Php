htmx.onLoad(() => {
  // Title
  autogrow([document.querySelector('.post-title')]);

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

      // How far is the block list from the top of the window
      const distanceToTop = el.parentElement.querySelector('.block-list').getBoundingClientRect().top;

      // If the block list is too close to the top, move it down
      if (distanceToTop < 10) {
        el.parentElement.querySelector('.block-list').style.bottom = `-${Math.abs(distanceToTop) + 30}px`;
      }

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
});

// Called after adding a new block, which scrolls the
// new block into view and focuses in the beginning of it.
htmx.on("focusBlockBeginning", (event) => {
  const blockId = event.detail.value;
  const block = document.querySelector(
    '#editor .block[data-id="' + blockId + '"]',
  );

  if (block) {
    block.scrollIntoView({ behavior: "instant", block: "end" });

    if (block.querySelector("textarea")) {
      block.querySelector("textarea").focus();
    }

    if (block.querySelector('paragraph-block')) {
      block.querySelector('paragraph-block').setCursorToBeginning();
      block.querySelector('paragraph-block').click();
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
    block.scrollIntoView({ behavior: "instant", block: "end" });

    if (block.querySelector("textarea")) {
      const temp_value = block.querySelector("textarea").value;
      block.querySelector("textarea").value = "";
      block.querySelector("textarea").value = temp_value;
      block.querySelector("textarea").focus();
    }

    if (block.querySelector('paragraph-block')) {
      block.querySelector('paragraph-block').setCursorToEnd();
      block.querySelector('paragraph-block').click();
    }
  }
});
