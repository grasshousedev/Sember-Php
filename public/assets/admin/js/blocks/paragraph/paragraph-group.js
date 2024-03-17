import {LitElement, html} from 'https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js';
import './paragraph-cursor.js';
import './paragraph-char.js';

export class ParagraphGroup extends LitElement {
  static properties = {
    type: 'normal',
    content: []
  }

  constructor() {
    super();
  }

  render() {
    const out = html`${this.content.map((item) => {
      switch(item.type) {
        case 'cursor':
          return html`<paragraph-cursor .hidden=${item.hidden}></paragraph-cursor>`;
        case 'char':
          return html`<paragraph-char id=${item.id} .selected=${item.selected} .value=${item.value}></paragraph-char>`;
        case 'group':
          return html`<paragraph-group id=${item.id} type=${item.groupType} .content=${item.content}></paragraph-group>`;
      }
    })}`

    switch(this.type) {
      case 'bold':
        return html`<strong>${out}</strong>`;
      case 'italic':
        return html`<em>${out}</em>`;
      default:
        return out;
    }
  }
}

customElements.define('paragraph-group', ParagraphGroup);