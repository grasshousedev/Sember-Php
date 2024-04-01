import {charNodesAfterId} from "./utils.js";

export default class ParagraphAction {
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

    const { cursorPosition, content } = context;

    if (cursorPosition === null || cursorPosition === "0") {
      context.dispatchEvent(new CustomEvent("createParagraphBlock", {
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
    context.dispatchEvent(new CustomEvent("content-changed", {
      detail: {
        content: context.markAllNodesAsDeselected(context.traverseContentTreeAndRemoveCursorNode(newContent)),
        after: () => {
          context.dispatchEvent(new CustomEvent("createParagraphBlock", {
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