import {LitElement, html, css} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';

export class ParagraphCursor extends LitElement {
  static properties = {
    hidden: {type: Boolean, attribute: true, state: false}
  }

  static styles = css`
      @keyframes blink {
          0% {
              opacity: 1;
          }
          50% {
              opacity: 0;
          }
          100% {
              opacity: 1;
          }
      }
      
      span {
          display: inline-block;
          height: calc(1lh - 4px);
          width: 1px;
          background: #111;
          z-index: 2;
          position: absolute;
          animation: blink 1s infinite;
          margin-top: 2px;
      }
      
      span.hidden {
          display: none;
      }
  `;

  constructor() {
    super();
  }

  render() {
    return html`<span class="${this.hidden ? 'hidden' : ''}"></span>`;
  }
}

customElements.define('paragraph-cursor', ParagraphCursor);