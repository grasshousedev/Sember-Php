function filterByStatus(el) {
  const searchParams = new URLSearchParams(window.location.search);
  searchParams.set("status", el.value);
  window.location.search = searchParams.toString();
}

function sortBy(el) {
  const searchParams = new URLSearchParams(window.location.search);
  searchParams.set("sort_by", el.value);
  window.location.search = searchParams.toString();
}

function orderBy(el) {
  const searchParams = new URLSearchParams(window.location.search);
  searchParams.set("sort_order", el.value);
  window.location.search = searchParams.toString();
}