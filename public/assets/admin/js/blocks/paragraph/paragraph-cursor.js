import {LitElement, html, css} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';

export class ParagraphCursor extends LitElement {
  static properties = {}
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
          height: calc(100% - 4px);
          width: 1px;
          background: #111;
          position: absolute;
          margin-left: -1px;
          animation: blink 1s infinite;
          margin-top: 2px;
      }
  `;

  constructor() {
    super();
  }

  // Render the UI as a function of component state
  render() {
    return html`<span></span>`;
  }
}

customElements.define('paragraph-cursor', ParagraphCursor);