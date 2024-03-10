import {LitElement, html, css} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import {ContextConsumer, ContextProvider} from 'https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm';
import {cursorPosition} from './contexts.js';

export class ParagraphChar extends LitElement {
  cursorConsumer = new ContextConsumer(this, {context: cursorPosition, subscribe: true});

  static properties = {
    value: ''
  }

  static styles = css`
      .char {
          display: inline-block;
          position: relative;
      }
      
      .left-side {
          position: absolute;
          top: 0;
          left: 0;
          width: 50%;
          height: 100%;
          background: transparent;
      }
      
      .right-side {
          position: absolute;
          top: 0;
          right: 0;
          width: 50%;
          height: 100%;
          background: transparent;
      }
  `;

  constructor() {
    super();
  }

  setPositionLeft() {
    this.cursorConsumer.value.setValue("0");
    this.cursorConsumer.value.setValue(this.id);
  }

  setPositionRight() {
    this.cursorConsumer.value.setValue("0");

    // todo: compute actual ID, and if none found, set to 0 so that it would be in the end.
    // 0 is always in the end.
    this.cursorConsumer.value.setValue(this.id, true);
  }

  // Render the UI as a function of component state
  render() {
    return html`<span class="char">
        ${this.value}
        <span @click="${this.setPositionLeft}" class="left-side"></span>
        <span @click="${this.setPositionRight}" class="right-side"></span>
    </span>`;
  }
}


customElements.define('paragraph-char', ParagraphChar);