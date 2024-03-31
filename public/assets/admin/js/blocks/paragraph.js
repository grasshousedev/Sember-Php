window.addEventListener("htmx:load", () => {
  document.querySelectorAll('.paragraph-block').forEach((block) => {
    block.querySelector('paragraph-block').addEventListener('content-changed', (e) => {
      const postId = block.getAttribute('data-post-id');
      const blockId = block.getAttribute('data-id');


      console.log(e);
      htmx.ajax('POST', `/admin/api/post/${postId}/blocks/${blockId}/opt/update`, {
        values: {
          content: JSON.stringify(e.detail)
        },
        swap: 'none'
      });
    });
  });
});