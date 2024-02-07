function autogrow(els) {
  els.forEach(el => {
    const textarea = el.querySelector('textarea');

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener('input', (_) => {
      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  });
}

htmx.onLoad(() => {
  const toggles = document.querySelectorAll('.toggle-block-list');

  toggles.forEach(el => {
    el.addEventListener('click', () => {
      // Close all other options
      toggles.forEach(toggle => {
        toggle.parentElement.querySelector('.block-list').classList.remove('active');
      });

      // Open the clicked options
      el.parentElement.querySelector('.block-list').classList.toggle('active');

      el.parentElement.querySelector('.block-list').addEventListener('mouseleave', () => {
        el.parentElement.querySelector('.block-list').classList.remove('active');
      })
    });
  })

  // Block options
  const blockOptions = document.querySelectorAll('.toggle-block-options');

  blockOptions.forEach(el => {
    el.addEventListener('click', () => {
      // Close all other options
      blockOptions.forEach(toggle => {
        toggle.parentElement.querySelector('.block-options-menu').classList.remove('active');
      });

      // Open the clicked options
      el.parentElement.querySelector('.block-options-menu').classList.toggle('active');

      el.parentElement.querySelector('.block-options-menu').addEventListener('mouseleave', () => {
        el.parentElement.querySelector('.block-options-menu').classList.remove('active');
      })
    });
  });

  // Markdown blocks
  autogrow(document.querySelectorAll('.markdown-block'));

  // Big Heading blocks
  autogrow(document.querySelectorAll('.big_heading-block'));

  // Medium Heading blocks
  autogrow(document.querySelectorAll('.medium_heading-block'));

  // Small Heading blocks
  autogrow(document.querySelectorAll('.small_heading-block'));
});