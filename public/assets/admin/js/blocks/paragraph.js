window.addEventListener("htmx:load", () => {
  document.querySelectorAll('.paragraph-block').forEach((block) => {
    block.querySelector('paragraph-block').addEventListener('content-changed', (e) => {
      const postId = block.getAttribute('data-post-id');
      const blockId = block.getAttribute('data-id');

      htmx.ajax('POST', `/admin/api/post/${postId}/blocks/${blockId}/opt/update`, {
        values: {
          content: JSON.stringify(e.detail.content)
        },
        swap: 'none'
      }).then(() => {
        if (typeof e.detail.after === 'function') {
          e.detail.after();
        }
      });
    });

    block.querySelector('paragraph-block').addEventListener('createParagraphBlock', (e) => {
      const postId = block.getAttribute('data-post-id');
      const blockId = block.getAttribute('data-id');

      htmx.ajax('POST', `/admin/api/post/${postId}/blocks/add/paragraph/${blockId}`, {
        values: {
          content: JSON.stringify(e.detail)
        },
        swap: 'outerHTML',
        target: '.blocks'
      });
    });
  });
});