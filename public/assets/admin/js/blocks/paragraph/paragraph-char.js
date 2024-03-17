import {LitElement, html, css} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextConsumer} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import {cursorPosition, meta} from './contexts.js';

export class ParagraphChar extends LitElement {
  cursorConsumer = new ContextConsumer(this, {context: cursorPosition, subscribe: true});
  metaConsumer = new ContextConsumer(this, {context: meta, subscribe: true});

  static properties = {
    value: {type: String, attribute: true, state: false},
    selected: {type: Boolean, attribute: true, state: false},
  }

  static styles = css`
      .char {
          display: inline-block;
          position: relative;
          user-select: none;
      }
      
      .char.selected {
          background: lightskyblue;
      }
      
      .left-side {
          position: absolute;
          top: 0;
          left: 0;
          width: 50%;
          height: 100%;
          z-index: 1;
          cursor: text;
          background: transparent;
      }
      
      .right-side {
          position: absolute;
          top: 0;
          right: 0;
          width: 50%;
          height: 100%;
          z-index: 1;
          cursor: text;
          background: transparent;
      }
  `;

  constructor() {
    super();
  }

  firstUpdated() {
    const node = this.shadowRoot;

    node.removeEventListener('dblclick', this.doubleClick);
    node.addEventListener('dblclick', this.doubleClick);
  }

  doubleClick = (e) => {
    e.preventDefault();
    this.metaConsumer.value.selectWordOfNode(this.id);
    return false;
  }

  setPositionLeft() {
    this.cursorConsumer.value.setValue("0");
    this.cursorConsumer.value.setValue(this.id);
  }

  setPositionRight() {
    this.cursorConsumer.value.setValue("0");
    this.cursorConsumer.value.setValue(this.id, true);
  }

  render() {
    return html`<span class="char ${this.selected ? 'selected' : ''}">
        ${this.value === ' ' ? html`&nbsp;` : this.value}
        <span @click="${this.setPositionLeft}" class="left-side"></span>
        <span @click="${this.setPositionRight}" class="right-side"></span>
    </span>`;
  }
}

customElements.define('paragraph-char', ParagraphChar);