import {charNodesAfterId} from "./utils.js";

export default class ParagraphAction {
  /**
   * Capture the "Backspace" key press. This will delete the character to the
   * left of the cursor position. If there's a selection, it will delete the
   * selected text. If the cursor is at the beginning, it will dispatch a
   * "deleteBlock" event.
   *
   * @param event
   * @param context
   */
  static backspace(event, context) {
    let content = context.content;

    // There's a selection, which means we want to delete the selected text
    if (context.selectionExists()) {
      const selectedNodes = context.selectedNodes();

      let nodeIdBeforeSelection = context.computeTreeNodeIdRightOf(content, selectedNodes[0].id);

      if (selectedNodes.length > 1) {
        nodeIdBeforeSelection = context.computeTreeNodeIdRightOf(
          content,
          selectedNodes[selectedNodes.length - 1].id,
        );
      }

      const fromBeginning = selectedNodes[0].id === context.computeFirstContentTreeNodeId(content);
      const fromEnd = selectedNodes[selectedNodes.length - 1].id === context.computeLastContentTreeNodeId(content);

      for (let i = 0; i < selectedNodes.length; i++) {
        content = context.removeNodeFromContent(content, selectedNodes[i].id);
      }

      content = context.removeOrphanGroups(this.markAllNodesAsDeselected(content));

      // Is the selection at the beginning?
      if (fromBeginning) {
        context.cursorPosition = context.computeFirstContentTreeNodeId(content);
      } else if (fromEnd) {
        context.cursorPosition = "0";
      } else {
        context.cursorPosition = nodeIdBeforeSelection;
      }

      context.content = context.removeOrphanGroups(content);
    }

    // There's no selection, which means we want to delete the character to the left
    else {
      let nodeIdBeforeCursor = context.computeTreeNodeIdLeftOf(content, context.cursorPosition);

      if (nodeIdBeforeCursor !== context.cursorPosition) {
        while (context.getNodeById(content, nodeIdBeforeCursor)?.type === "group") {
          nodeIdBeforeCursor = context.computeTreeNodeIdLeftOf(content, nodeIdBeforeCursor);
        }
      }

      // Do not delete if the cursor is at the beginning, but do dispatch a deleteBlock event
      if (context.content.length > 0 && context.content[0].type === "cursor") {
        context.dispatchEvent(new CustomEvent("deleteBlock", {
          bubbles: true,
          composed: true,
        }));

        return;
      }

      context.cursorPosition = context.computeTreeNodeIdRightOf(content, nodeIdBeforeCursor);
      content = context.removeNodeFromContent(content, nodeIdBeforeCursor);
      context.content = context.removeOrphanGroups(content);
    }
  }

  /**
   * Capture the "Enter" key press. This will create a new paragraph
   * block below. If there's any content after the `cursorPosition`, it
   * will be moved to the new block.
   *
   * @param event
   * @param context
   */
  static enter(event, context) {
    event.preventDefault();
    console.log('enter');

    const { cursorPosition, content } = context;

    if (cursorPosition === null || cursorPosition === "0") {
      context.dispatchEvent(new CustomEvent("createBlock", {
        detail: [],
        bubbles: true,
        composed: true,
      }));

      return;
    }

    // Get the char nodes after the cursor position
    const charNodes = charNodesAfterId(content, cursorPosition);

    // Remove those nodes
    let newContent = content;

    for (const node of charNodes) {
      newContent = context.removeNodeFromContent(newContent, node.id);
    }

    // Make sure there's no orphans
    newContent = context.removeOrphanGroups(newContent);

    // Dispatch events
    context.dispatchEvent(new CustomEvent("contentChanged", {
      detail: {
        content: context.markAllNodesAsDeselected(context.traverseContentTreeAndRemoveCursorNode(newContent)),
        after: () => {
          context.dispatchEvent(new CustomEvent("createBlock", {
            detail: charNodes,
            bubbles: true,
            composed: true,
          }));
        }
      },
      bubbles: true,
      composed: true,
    }));


    // TODO: Construct new content (preserving groups) for those char nodes
  }
}