import {
  LitElement,
  html,
  css,
} from "https://cdn.jsdelivr.net/gh/lit/dist@3/core/lit-core.min.js";
import { ContextConsumer } from "https://cdn.jsdelivr.net/npm/@lit/context@1.1.0/+esm";
import { cursorPosition, meta } from "./contexts.js";

export class ParagraphChar extends LitElement {
  cursorConsumer = new ContextConsumer(this, {
    context: cursorPosition,
    subscribe: true,
  });
  metaConsumer = new ContextConsumer(this, { context: meta, subscribe: true });

  static properties = {
    value: { type: String, attribute: true, state: false },
    selected: { type: Boolean, attribute: true, state: false },
    mouseDown: { type: Boolean, attribute: false, state: true },
  };

  static styles = css`
    .char {
      display: inline-block;
      position: relative;
      user-select: none;
    }

    .char.selected {
      background: lightskyblue;
    }

    .sides {
      display: grid;
      width: 100%;
      height: 100%;
      grid-template-columns: 1fr 1fr;
      user-select: all;
      cursor: text;
      z-index: 2;
      position: absolute;
      top: 0;
      left: 0;
    }

    .left-side {
      width: 100%;
      height: 100%;
    }

    .right-side {
      width: 100%;
      height: 100%;
    }
  `;

  constructor() {
    super();
    this.mouseDown = false;
  }

  firstUpdated() {
    const node = this.shadowRoot;

    node.removeEventListener("dblclick", this.doubleClick);
    node.addEventListener("dblclick", this.doubleClick);
    // node.addEventListener("mouseover", this.pointerOver, { once: true });
    // node.addEventListener("mouseout", this.pointerOut, { once: true });
    //
    // window.addEventListener("mousedown", () => {
    //   this.mouseDown = true;
    //   this.metaConsumer.value.markAllNodesAsDeselected();
    // });
    //
    // window.addEventListener("mouseup", () => {
    //   this.mouseDown = false;
    // });
  }

  doubleClick = (e) => {
    e.preventDefault();

    this.metaConsumer.value.selectWordOfNode(this.id);

    return false;
  };

  pointerOver = (e) => {
    e.preventDefault();

    if (!this.mouseDown) return;

    this.metaConsumer.value.toggleNodeAsSelected(this.id);

    return false;
  };

  pointerOut = (e) => {
    e.preventDefault();

    if (!this.mouseDown) return;

    setTimeout(() => {
      this.shadowRoot.addEventListener("mouseover", this.pointerOver, {
        once: true,
      });

      this.shadowRoot.addEventListener("mouseout", this.pointerOut, {
        once: true,
      });
    }, 100);

    return false;
  };

  setPositionLeft() {
    this.cursorConsumer.value.setValue("0");
    this.cursorConsumer.value.setValue(this.id);
  }

  setPositionRight() {
    this.cursorConsumer.value.setValue("0");
    this.cursorConsumer.value.setValue(this.id, true);
  }

  render() {
    return html`<span class="char ${this.selected ? "selected" : ""}">
      ${this.value === " " ? html`&nbsp;` : this.value}
      <div class="sides">
        <span @click="${this.setPositionLeft}" class="left-side"></span>
        <span @click="${this.setPositionRight}" class="right-side"></span>
      </div>
    </span>`;
  }
}

customElements.define("paragraph-char", ParagraphChar);
