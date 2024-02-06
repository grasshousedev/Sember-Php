htmx.onLoad(() => {
  const toggles = document.querySelectorAll('.toggle-block-options');

  toggles.forEach(el => {
    el.addEventListener('click', () => {
      // Close all other options
      toggles.forEach(toggle => {
        toggle.parentElement.querySelector('.block-options').classList.remove('active');
      });

      // Open the clicked options
      el.parentElement.querySelector('.block-options').classList.toggle('active');

      el.parentElement.querySelector('.block-options').addEventListener('mouseleave', () => {
        el.parentElement.querySelector('.block-options').classList.remove('active');
      })
    });
  })

  // Markdown blocks
  const markdownBlocks = document.querySelectorAll('.markdown-block');

  markdownBlocks.forEach(el => {
    const textarea = el.querySelector('textarea');

    textarea.style.height = `0px`;
    textarea.style.height = `${textarea.scrollHeight}px`;

    textarea.addEventListener('input', (e) => {
      textarea.style.height = `0px`;
      textarea.style.height = `${textarea.scrollHeight}px`;
    });
  });
});