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
}