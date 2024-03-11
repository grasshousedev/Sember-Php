import {css, html, LitElement} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextProvider} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import {v4 as uuidv4} from 'https://cdn.jsdelivr.net/npm/uuid@9.0.1/+esm'
import './paragraph/paragraph-group.js';
import {cursorPosition} from './paragraph/contexts.js';
import {charNodeFlattenFn} from './paragraph/utils.js';

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
          const rightValue = this.computeTreeNodeIdRightOf(value);
          this.cursorProvider.setValue({rightValue, setValue: this.cursorProviderUpdateHandle});
          this.cursorPosition = rightValue;
        } else {
          this.cursorProvider.setValue({value, setValue: this.cursorProviderUpdateHandle});
          this.cursorPosition = value;
        }
      }
    });
  }

  cursorProvider = new ContextProvider(this, {context: cursorPosition});

  static properties = {
    active: false,
    cursorPosition: 0,
    content: [],
    node: false,
  }

  constructor() {
    super();

    this.cursorProvider.setValue({
      value: 0,
      setValue: this.cursorProviderUpdateHandle,
    });

    // Declare reactive properties
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
          {id: uuidv4(), type: 'char', value: 'd'},
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

    if (e.key === "ArrowLeft") {
      e.preventDefault();
      this.cursorPosition = this.computeTreeNodeIdLeftOf(this.cursorPosition);
      return;
    }

    if (e.key === "ArrowRight") {
      e.preventDefault();
      this.cursorPosition = this.computeTreeNodeIdRightOf(this.cursorPosition);
      return;
    }

    if (e.key === "Backspace") {
      e.preventDefault();
      const nodeIdBeforeCursor = this.computeTreeNodeIdLeftOf(this.cursorPosition);

      if (nodeIdBeforeCursor !== this.cursorPosition) {
        this.content = this.removeNodeFromContent(this.content, nodeIdBeforeCursor);
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

    if ((e.metaKey || e.ctrlKey) && e.key === "a") {
      // TODO: Implement select all
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
      'Dead', 'IntlBackslash', 'IntlRo', 'IntlYen', 'IntlPipe',
    ];

    if (!notAllowedChars.includes(e.key) && !e.metaKey) {
      e.preventDefault();
      this.content = this.addCharToContent(this.content, e.key);
    }
  }

  activateEditor = (e) => {
    if (e.target === this.node.host) {
      this.active = true;
    } else {
      this.active = false;
      this.cursorPosition = null;
    }
  }

  firstUpdated() {
    this.node = this.shadowRoot;

    window.removeEventListener('keydown', this.listenKeyPress);
    window.addEventListener('keydown', this.listenKeyPress);
    window.removeEventListener("click", this.activateEditor);
    window.addEventListener("click", this.activateEditor);
  }

  /**
   * Adds a character to the content
   *
   * @param content
   * @param char
   * @returns {*[]}
   */
  addCharToContent(content, char) {
    return this.addNodeLeftOfId(content, this.cursorPosition, {id: uuidv4(), type: 'char', value: char});
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
          content: this.addNodeLeftOfId(item.content, id, node)
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
   * @returns {*[]}
   */
  traverseContentTreeAndAddCursorNode(content) {
    if (this.cursorPosition === "0") {
      return [...content, {id: uuidv4(), type: 'cursor'}];
    }

    let newContent = [];

    for (let i = 0; i < content.length; i++) {
      const item = content[i];

      // chars
      if (item.type === 'char') {
        if (item.id === this.cursorPosition) {
          newContent.push({id: uuidv4(), type: 'cursor'});
        }

        newContent.push(item);
      }

      // groups
      if (item.type === 'group') {
        newContent.push({
          ...item,
          content: this.traverseContentTreeAndAddCursorNode(item.content)
        })
      }
    }

    return newContent;
  }

  /**
   * Computes the id of the last content tree node
   *
   * @param content
   * @returns {number}
   */
  computeLastContentTreeNodeId(content) {
    let lastId = 0;

    const lastNode = content[content.length - 1].type === 'cursor' ?
      content[content.length - 2] :
      content[content.length - 1];

    if (lastNode.type === 'char') {
      lastId = lastNode.id;
    }

    if (lastNode.type === 'group') {
      lastId = this.computeLastContentTreeNodeId(lastNode.content);
    }

    return lastId;
  }

  /**
   * Computes the id of the node left of a given id
   *
   * @param id
   * @returns {*|number}
   */
  computeTreeNodeIdLeftOf(id) {
    if (id === "0") {
      return this.computeLastContentTreeNodeId(this.content);
    }

    const charNodes = this.content.flatMap(charNodeFlattenFn).filter((item) => item?.type === 'char');
    const foundIndex = charNodes.findIndex((item) => item?.id === id);

    if (foundIndex === -1 || foundIndex === 0) {
      return id;
    }

    return charNodes[foundIndex - 1].id;
  }

  /**
   * Computes the id of the node right of a given id
   *
   * @param id
   * @returns {*|string}
   */
  computeTreeNodeIdRightOf(id) {
    const charNodes = this.content.flatMap(charNodeFlattenFn).filter((item) => item?.type === 'char');
    const foundIndex = charNodes.findIndex((item) => item?.id === id);

    if (foundIndex === -1) {
      return "0";
    }

    if (foundIndex === charNodes.length - 1) {
      return "0";
    }

    return charNodes[foundIndex + 1].id;
  }

  render() {
    const content = this.content;
    this.content = [];
    this.content = this.traverseContentTreeAndAddCursorNode(this.traverseContentTreeAndRemoveCursorNode(content));

    return html`
      <paragraph-group type="normal"
                       .content=${this.content}>
      </paragraph-group>
    `;
  }
}

customElements.define('paragraph-block', ParagraphBlock);
