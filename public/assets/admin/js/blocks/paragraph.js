import {css, html, LitElement} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextProvider} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import {v4 as uuidv4} from 'https://cdn.jsdelivr.net/npm/uuid@9.0.1/+esm'
import './paragraph/paragraph-group.js';
import {cursorPosition, meta} from './paragraph/contexts.js';
import {charNodeFlattenFn, nodeFlattenFn} from './paragraph/utils.js';

export class ParagraphBlock extends LitElement {
  /**
   * Updates the cursor position
   *
   * @param value
   */
  cursorProviderUpdateHandle = (value) => {
    this.cursorProvider.setValue({
      value,
      setValue: (value, right = false) => {
        if (right) {
          const rightValue = this.computeTreeNodeIdRightOf(this.content, value);
          this.cursorProvider.setValue({rightValue, setValue: this.cursorProviderUpdateHandle});
          this.cursorPosition = rightValue;
        } else {
          this.cursorProvider.setValue({value, setValue: this.cursorProviderUpdateHandle});
          this.cursorPosition = value;
        }

        this.content = this.markAllNodesAsDeselected(this.content);
      }
    });
  }

  cursorProvider = new ContextProvider(this, {context: cursorPosition});
  metaProvider = new ContextProvider(this, {context: meta});

  static properties = {
    active: {type: Boolean, state: true, attribute: false},
    cursorPosition: {type: String, state: true, attribute: false},
    content: {type: Array, state: true, attribute: false},
    node: {type: Object, state: true, attribute: false},
  }

  constructor() {
    super();

    this.cursorProvider.setValue({
      value: 0,
      setValue: this.cursorProviderUpdateHandle,
    });

    this.metaProvider.setValue({
      selectWordOfNode: this.selectWordOfNodeId,
    })

    this.content = [
      {id: uuidv4(), type: 'char', value: 'H'},
      {id: uuidv4(), type: 'char', value: 'e'},
      {id: uuidv4(), type: 'char', value: 'l'},
      {id: uuidv4(), type: 'char', value: 'l'},
      {id: uuidv4(), type: 'char', value: 'o'},
      {id: uuidv4(), type: 'char', value: ','},
      {id: uuidv4(), type: 'char', value: ' '},
      {
        id: uuidv4(),
        type: 'group',
        groupType: 'bold',
        content: [
          {id: uuidv4(), type: 'char', value: 'W'},
          {id: uuidv4(), type: 'char', value: 'o'},
          {id: uuidv4(), type: 'char', value: 'r'},
          {id: uuidv4(), type: 'char', value: 'l'},
          {id: uuidv4(), type: 'char', value: 'd'}
        ]
      }];
  }

  /**
   * Listens for key presses
   *
   * @param e
   */
  listenKeyPress = (e) => {
    if (!this.active) return;

    // Move left (without shift)
    if (!e.shiftKey && e.key === "ArrowLeft") {
      e.preventDefault();
      this.cursorPosition = this.computeTreeNodeIdLeftOf(this.content, this.cursorPosition);
      this.content = this.markAllNodesAsDeselected(this.content);
      return;
    }

    // Move right (without shift)
    if (!e.shiftKey && e.key === "ArrowRight") {
      e.preventDefault();
      this.cursorPosition = this.computeTreeNodeIdRightOf(this.content, this.cursorPosition);
      this.content = this.markAllNodesAsDeselected(this.content);
      return;
    }

    // Delete text
    if (e.key === "Backspace") {
      e.preventDefault();
      let nodeIdBeforeCursor = this.computeTreeNodeIdLeftOf(this.content, this.cursorPosition);

      if (nodeIdBeforeCursor !== this.cursorPosition) {
        while(this.getNodeById(this.content, nodeIdBeforeCursor).type === 'group') {
          nodeIdBeforeCursor = this.computeTreeNodeIdLeftOf(this.content, nodeIdBeforeCursor);
        }

        // If the node before cursor is a group, we mean the node before that
        this.cursorPosition = this.computeTreeNodeIdRightOf(this.content, nodeIdBeforeCursor);
        const content = this.removeNodeFromContent(this.content, nodeIdBeforeCursor);
        this.content = this.removeOrphanGroups(content);
      }

      return;
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "b") {
      // TODO: Implement bold
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "i") {
      // TODO: Implement italic
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "u") {
      // TODO: Implement underline
    }

    // Select all
    if ((e.metaKey || e.ctrlKey) && e.key === "a") {
      e.preventDefault();
      this.content = this.markAllNodesAsSelected(this.content);
      this.cursorPosition = this.computeFirstContentTreeNodeId(this.content);
      return false;
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "z") {
      // TODO: Implement undo
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "y") {
      // TODO: Implement redo
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "c") {
      // TODO: Implement copy
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "x") {
      // TODO: Implement cut
    }

    if ((e.metaKey || e.ctrlKey) && e.key === "v") {
      // TODO: Implement paste
    }

    if (e.shiftKey && e.key === "ArrowLeft") {
      e.preventDefault();
      this.cursorPosition = this.computeTreeNodeIdLeftOf(this.content, this.cursorPosition);
      this.content = this.toggleNodeAsSelected(this.content, this.cursorPosition);
      return;
    }

    if (e.shiftKey && e.key === "ArrowRight") {
      e.preventDefault();
      this.content = this.toggleNodeAsSelected(this.content, this.cursorPosition);
      this.cursorPosition = this.computeTreeNodeIdRightOf(this.content, this.cursorPosition);
      return;
    }

    const notAllowedChars = [
      'ArrowLeft', 'ArrowRight', 'Backspace', 'Meta', 'Control', 'Shift',
      'Alt', 'CapsLock', 'Tab', 'Enter', 'Escape', 'PageUp', 'PageDown',
      'End', 'Home', 'Insert', 'Delete', 'F1', 'F2', 'F3', 'F4', 'F5',
      'F6', 'F7', 'F8', 'F9', 'F10', 'F11', 'F12', 'PrintScreen',
      'ScrollLock', 'Pause', 'ContextMenu', 'OS', 'MediaTrackPrevious',
      'MediaTrackNext', 'MediaPlayPause', 'MediaStop', 'MediaTrackNext',
      'MediaSelect', 'Mail', 'Calculator', 'BrowserSearch', 'BrowserHome',
      'BrowserBack', 'BrowserForward', 'BrowserStop', 'BrowserRefresh',
      'BrowserFavorites', 'VolumeMute', 'VolumeDown', 'VolumeUp', 'MediaPlay',
      'MediaPause', 'MediaRecord', 'MediaFastForward', 'MediaRewind',
      'MediaTrackNext', 'MediaTrackPrevious', 'MediaStop', 'MediaEject',
      'MediaPlayPause', 'LaunchMail', 'LaunchApp2', 'LaunchApp1', 'Select',
      'Open', 'Find', 'Help', 'Clear', 'Symbol', 'Unidentified',
      'Dead', 'IntlBackslash', 'IntlRo', 'IntlYen', 'IntlPipe', 'ArrowUp',
      'ArrowDown'
    ];

    if (!notAllowedChars.includes(e.key) && !e.metaKey) {
      e.preventDefault();
      this.addCharToContent(e.key);
    }
  }

  activateEditor = (e) => {
    if (e.target === this.node.host) {
      this.active = true;
    } else {
      this.active = false;
      this.cursorPosition = null;
      this.content = this.markAllNodesAsDeselected(this.content);
    }
  }

  firstUpdated() {
    this.node = this.shadowRoot;

    window.removeEventListener('keydown', this.listenKeyPress);
    window.addEventListener('keydown', this.listenKeyPress);
    window.removeEventListener("click", this.activateEditor);
    window.addEventListener("click", this.activateEditor);
  }

  willUpdate() {
    const content = this.traverseContentTreeAndRemoveCursorNode(this.content);
    this.content = this.traverseContentTreeAndAddCursorNode(content, {hidden: this.selectionExists()});
  }

  /**
   * Gets a node by id
   *
   * @param content
   * @param id
   * @returns {*|null}
   */
  getNodeById(content, id) {
    const nodes = this.allNodesFlatten(content);
    const foundIndex = nodes.findIndex((item) => item?.id === id);

    if (foundIndex === -1) {
      return null;
    }

    return nodes[foundIndex];
  }

  /**
   * Removes orphan groups
   *
   * @param content
   * @returns {*[]}
   */
  removeOrphanGroups(content) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        newContent.push(item);
      }

      // groups
      if (item.type === 'group' && item.content.length > 0) {
        newContent.push({
          ...item,
          content: this.removeOrphanGroups(item.content)
        });
      }
    }

    return newContent;
  }

  /**
   * Flatten all nodes.
   *
   * @param content
   * @returns {any[]}
   */
  allNodesFlatten(content) {
    return content.flatMap(nodeFlattenFn);
  }

  /**
   * Flatten all char nodes
   *
   * @param content
   * @returns {*[]}
   */
  charNodesFlatten(content) {
    return content.flatMap(charNodeFlattenFn);
  }

  /**
   * Toggles a node as selected
   *
   * @param content
   * @param nodeId
   * @returns {*[]}
   */
  toggleNodeAsSelected(content, nodeId) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id === nodeId) {
          newContent.push({...item, selected: typeof item.selected === 'boolean' ? !item.selected : true});
        } else {
          newContent.push(item);
        }
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.toggleNodeAsSelected(item.content, nodeId)
        })
      }
    }

    return newContent;
  }

  /**
   * Marks all nodes as selected
   *
   * @param content
   * @returns {*}
   */
  markAllNodesAsSelected(content) {
    return content.map((item) => {
      if (item.type === 'char') {
        return {...item, selected: true};
      }

      if (item.type === 'group') {
        return {
          ...item,
          content: this.markAllNodesAsSelected(item.content)
        }
      }

      return item;
    });
  }

  /**
   * Marks all nodes as deselected
   *
   * @param content
   * @returns {*}
   */
  markAllNodesAsDeselected(content) {
    return content.map((item) => {
      if (item.type === 'char') {
        return {...item, selected: false};
      }

      if (item.type === 'group') {
        return {
          ...item,
          content: this.markAllNodesAsDeselected(item.content)
        }
      }

      return item;
    });
  }

  /**
   * Selects a word of a given node id
   *
   * @param nodeId
   */
  selectWordOfNodeId = (nodeId) => {
    const allNodes = this.charNodesFlatten(this.content);
    const foundIndex = allNodes.findIndex((item) => item?.id === nodeId);

    // Is this a space node or a comma/punctuation node?
    // Because if it is, we just want to highlight that
    if (
      allNodes[foundIndex]?.value === ' ' ||
      allNodes[foundIndex]?.value === ',' ||
      allNodes[foundIndex]?.value === '.') {
      this.content = this.toggleNodeAsSelected(this.content, allNodes[foundIndex].id);
      return;
    }

    // Find first space node to the left
    let leftIndex = foundIndex;
    while (
      leftIndex > 0 &&
      allNodes[leftIndex]?.value !== ' ' &&
      allNodes[leftIndex]?.value !== ',' &&
      allNodes[leftIndex]?.value !== '.') {
      leftIndex--;
    }

    // Find first space node to the right
    let rightIndex = foundIndex;
    while (
      rightIndex < allNodes.length
      && allNodes[rightIndex]?.value !== ' '
      && allNodes[rightIndex]?.value !== ','
      && allNodes[rightIndex]?.value !== '.') {
      rightIndex++;
    }

    // Mark all nodes between left and right index as selected
    for (let i = leftIndex; i < rightIndex; i++) {
      if (
        allNodes[i]?.type === 'char' &&
        allNodes[i]?.value !== ' ' &&
        allNodes[i]?.value !== ',' &&
        allNodes[i]?.value !== '.') {
        this.content = this.toggleNodeAsSelected(this.content, allNodes[i].id);
      }
    }

    // Move cursor to the beginning
    if (allNodes[leftIndex]?.value === ' ') {
      this.cursorPosition = allNodes[leftIndex + 1]?.id;
    } else {
      this.cursorPosition = allNodes[leftIndex]?.id;
    }
  }

  /**
   * Adds a character to the content
   *
   * @param char
   */
  addCharToContent(char) {
    let content = this.content;
    const newCharId = uuidv4();
    const newChar = {id: newCharId, type: 'char', value: char};

    // If we're in the end of the content
    if (this.cursorPosition === "0") {
      // If there is no content
      if (this.isContentEmpty()) {
        this.content = [
          newChar,
          {
            id: uuidv4(),
            type: 'cursor'
          }
        ];

        return;
      }

      // If there is, add to the right of the last node
      this.content = this.addNodeRightOfId(
        content,
        this.computeLastContentTreeNodeId(content),
        newChar
      );

      return;
    }

    // If we're in the beginning or middle of the content
    // and there is text selected, remove the selected text
    if (this.selectionExists()) {
      const selectedNodes = this.selectedNodes();

      // If the selection is a single char, replace it
      if (selectedNodes.length === 1) {
        content = this.replaceNode(content, selectedNodes[0].id, {
          ...selectedNodes[0],
          value: char,
        });

        this.content = this.markAllNodesAsDeselected(content);
        this.cursorPosition = this.computeTreeNodeIdRightOf(this.content, selectedNodes[0].id);
      }

      // Otherwise, remove the selected nodes and add the new char
      else {
        content = this.addNodeLeftOfId(
          content,
          this.cursorPosition,
          newChar);

        for (let i = 0; i < selectedNodes.length; i++) {
          content = this.removeNodeFromContent(content, selectedNodes[i].id);
        }

        this.content = content;
        this.cursorPosition = this.computeTreeNodeIdRightOf(this.content, newCharId);
      }

      return;
    }

    // Otherwise proceed as normal
    this.content = this.addNodeLeftOfId(
      content,
      this.cursorPosition,
      newChar
    );
  }

  /**
   * Checks if the content is empty
   *
   * @returns {boolean}
   */
  isContentEmpty() {
    return this.content.length === 0 || this.content.every((item) => item.type === 'cursor');
  }

  /**
   * Checks if there is a selection
   *
   * @returns {boolean}
   */
  selectionExists() {
    return this.charNodesFlatten(this.content).some((item) => item?.selected === true);
  }

  /**
   * Returns the selected nodes
   *
   * @returns {*[]}
   */
  selectedNodes() {
    return this.charNodesFlatten(this.content).filter((item) => item?.selected === true);
  }

  /**
   * Removes a node from the content
   *
   * @param content
   * @param nodeId
   * @returns {*[]}
   */
  removeNodeFromContent(content, nodeId) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id !== nodeId) {
          newContent.push(item);
        }
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.removeNodeFromContent(item.content, nodeId)
        });
      }
    }

    return newContent;
  }

  /**
   * Replaces a node in the content
   *
   * @param content
   * @param nodeId
   * @param newNode
   * @returns {*[]}
   */
  replaceNode(content, nodeId, newNode) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id === nodeId) {
          newContent.push(newNode);
        } else {
          newContent.push(item);
        }
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.replaceNode(item.content, nodeId, newNode)
        });
      }
    }

    return newContent;
  }

  /**
   * Adds a node to the left of a given id
   *
   * @param content
   * @param id
   * @param node
   * @returns {*[]}
   */
  addNodeLeftOfId(content, id, node) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id === id) {
          newContent.push(node);
        }

        newContent.push(item);
      }

      // groups
      if (item.type === 'group') {
        if (item.id === id) {
          newContent.push(node);
        }

        newContent.push({
          ...item,
          content: this.addNodeLeftOfId(item.content, id, node)
        })
      }
    }

    return newContent;
  }

  /**
   * Adds a node to the right of a given id
   *
   * @param content
   * @param id
   * @param node
   * @returns {*[]}
   */
  addNodeRightOfId(content, id, node) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        newContent.push(item);

        if (item.id === id) {
          newContent.push(node);
        }
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.addNodeRightOfId(item.content, id, node)
        })
      }
    }

    return newContent;
  }

  /**
   * Traverses the content tree and removes the cursor node
   *
   * @param content
   * @returns {*[]}
   */
  traverseContentTreeAndRemoveCursorNode(content) {
    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        newContent.push(item);
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.traverseContentTreeAndRemoveCursorNode(item.content)
        })
      }
    }

    return newContent;
  }

  /**
   * Traverses the content tree and adds the cursor node
   *
   * @param content
   * @param opts
   * @returns {*[]}
   */
  traverseContentTreeAndAddCursorNode(content, opts = {}) {
    const newCursor = {id: uuidv4(), type: 'cursor', ...opts};

    if (this.cursorPosition === "0") {
      return [...content, newCursor];
    }

    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id === this.cursorPosition) {
          newContent.push(newCursor);
        }

        newContent.push(item);
      }

      // groups
      if (item.type === 'group') {
        if (item.id === this.cursorPosition) {
          newContent.push(newCursor);
        }

        newContent.push({
          ...item,
          content: this.traverseContentTreeAndAddCursorNode(item.content, opts)
        })
      }
    }

    return newContent;
  }

  /**
   * Computes the id of the first content tree node
   *
   * @param content
   * @returns {*|string}
   */
  computeFirstContentTreeNodeId(content) {
    const charNodes = this.charNodesFlatten(content);

    if (charNodes.length > 0) {
      return charNodes[0].id;
    }

    return "0";
  }

  /**
   * Computes the id of the last content tree node
   *
   * @param content
   * @returns {string}
   */
  computeLastContentTreeNodeId(content) {
    const charNodes = this.content.flatMap(charNodeFlattenFn).filter((item) => item?.type === 'char');

    if (charNodes.length > 0) {
      return charNodes[charNodes.length - 1].id;
    }

    return "0";
  }

  /**
   * Computes the id of the node left of a given id
   *
   * @param content
   * @param id
   * @returns {*|number}
   */
  computeTreeNodeIdLeftOf(content, id) {
    if (id === "0") {
      return this.computeLastContentTreeNodeId(content);
    }

    const nodes = this.allNodesFlatten(content);
    const foundIndex = nodes.findIndex((item) => item?.id === id);

    if (foundIndex === -1 || foundIndex === 0) {
      return id;
    }

    return nodes[foundIndex - 1].id;
  }

  /**
   * Computes the id of the node right of a given id
   *
   * @param content
   * @param id
   * @returns {*|string}
   */
  computeTreeNodeIdRightOf(content, id) {
    const nodes = this.allNodesFlatten(content);
    const foundIndex = nodes.findIndex((item) => item?.id === id);

    if (foundIndex === -1) {
      return "0";
    }

    if (foundIndex === nodes.length - 1) {
      return "0";
    }

    return nodes[foundIndex + 1].id;
  }

  render() {
    return html`
        <div class="editor">
            <paragraph-group type="normal" .content=${this.content}>
            </paragraph-group>
        </div>
    `;
  }
}

customElements.define('paragraph-block', ParagraphBlock);
