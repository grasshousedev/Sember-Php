function autogrow(els) {
  els.forEach((el) => {
    const textarea = el.querySelector("textarea");

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener("input", (_) => {
      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  });
}

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
