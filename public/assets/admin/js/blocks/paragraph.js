import {LitElement, html, css} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextProvider} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import { v4 as uuidv4 } from 'https://cdn.jsdelivr.net/npm/uuid@9.0.1/+esm'
import './paragraph/paragraph-group.js';
import {cursorPosition} from './paragraph/contexts.js';

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
    content: []
  }

  static styles = css`
    paragraph-group.active {
        display: block;
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

  setActive() {
    this.active = true;
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

    const lastNode = content[content.length - 1];

    if (lastNode.type === 'char') {
      lastId = lastNode.id;
    }

    if (lastNode.type === 'group') {
      lastId = this.computeLastContentTreeNodeId(lastNode.content);
    }

    return lastId;
  }

  computeTreeNodeIdRightOf(id) {
    const charNodeFlattenFn = (item) => {
      if (item.type === 'char') {
        return item.id;
      }

      if (item.type === 'group') {
        return item.content.flatMap(charNodeFlattenFn);
      }
    };

    const charNodes = this.content.flatMap(charNodeFlattenFn);
    const foundIndex = charNodes.findIndex((item) => item === id);

    if (foundIndex === -1) {
      return "0";
    }

    if (foundIndex === charNodes.length - 1) {
      return "0";
    }

    return charNodes[foundIndex + 1];
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
