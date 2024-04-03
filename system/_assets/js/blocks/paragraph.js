window.addEventListener("htmx:load", () => {
  document.querySelectorAll('.paragraph-block').forEach((block) => {
    // Listen for content changes
    block.querySelector('paragraph-block').addEventListener('contentChanged', (e) => {
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

    // Listen for block creation
    block.querySelector('paragraph-block').addEventListener('createBlock', (e) => {
      const postId = block.getAttribute('data-post-id');
      const blockId = block.getAttribute('data-id');

      console.log('hello')

      htmx.ajax('POST', `/admin/api/post/${postId}/blocks/add/paragraph/${blockId}`, {
        values: {
          content: JSON.stringify(e.detail)
        },
        swap: 'outerHTML',
        target: '.blocks'
      });
    });

    // Listen for block deletion
    block.querySelector('paragraph-block').addEventListener('deleteBlock', (e) => {
      const postId = block.getAttribute('data-post-id');
      const blockId = block.getAttribute('data-id');

      htmx.ajax('DELETE', `/admin/api/post/${postId}/blocks/${blockId}`, {
        swap: 'outerHTML',
        target: '.blocks'
      });
    });
  });
});