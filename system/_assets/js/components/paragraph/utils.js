export function charNodeFlattenFn(item) {
  if (item.type === 'char') {
    return item;
  }

  if (item.type === 'group') {
    return item.content.flatMap(charNodeFlattenFn);
  }

  return [];
}

export function nodeFlattenFn(item) {
  if (item.type === 'char') {
    return item;
  }

  if (item.type === 'group') {
    return [
      item,
      ...item.content.flatMap(nodeFlattenFn)
    ]
  }

  return [];
}

export function charNodesAfterId(nodes, id) {
  const allCharNodes = nodes.flatMap(charNodeFlattenFn);
  const index = allCharNodes.findIndex((node) => node.id === id);

  return allCharNodes.slice(index);
}