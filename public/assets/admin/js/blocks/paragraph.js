import {css, html, LitElement} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextProvider} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import {v4 as uuidv4} from 'https://cdn.jsdelivr.net/npm/uuid@9.0.1/+esm'
import './paragraph/paragraph-group.js';
import {cursorPosition} from './paragraph/contexts.js';
import {charNodeFlattenFn} from './paragraph/utils.js';

export class ParagraphBlock extends LitElement {
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

  static styles = css`
    paragraph-group.active {
        display: inline-block;
        width: 100%;
        background: #eee;
    }
  `;

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

  listenKeyPress = (e) => {
    console.log(e);
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
      this.content = this.removeNodeFromContent(this.content, nodeIdBeforeCursor);
      return;
    }

    if (e.key === "Shift" || e.key === "Escape") {
      return;
    }

    // Any other key, just add it to the content
    this.content = this.addCharToContent(this.content, e.key);
  }

  setActive() {
    this.active = true;

    window.removeEventListener('keydown', this.listenKeyPress);
    window.addEventListener('keydown', this.listenKeyPress);
  }

  firstUpdated() {
    this.node = this.renderRoot;
  }

  addCharToContent(content, char) {
    return this.addNodeLeftOfId(content, this.cursorPosition, {id: uuidv4(), type: 'char', value: char});
  }

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

  setCursorAtEnd(e) {
    console.log('click', e)
    const id = this.computeLastContentTreeNodeId(this.content);
    const rightValue = this.computeTreeNodeIdRightOf(id);
    console.log('rightValue', rightValue)
    this.cursorPosition = rightValue;
  }

  // Render the UI as a function of component state
  render() {
    const content = this.content;
    this.content = [];
    this.content = this.traverseContentTreeAndAddCursorNode(this.traverseContentTreeAndRemoveCursorNode(content));

    return html`
      <paragraph-group @click="${this.setActive}" 
                       class="${this.active ? 'active' : 'not-active'}" 
                       type="normal"
                       .content=${this.content}>
      </paragraph-group>
    `;
  }
}

customElements.define('paragraph-block', ParagraphBlock);
