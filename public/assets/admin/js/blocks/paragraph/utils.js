export function charNodeFlattenFn(item) {
  if (item.type === 'char') {
    return {
      id: item.id,
      type: item.type,
      value: item.value,
    };
  }

  if (item.type === 'group') {
    return item.content.flatMap(charNodeFlattenFn);
  }
}