/**
 * Bundled by jsDelivr using Rollup v2.79.1 and Terser v5.19.2.
 * Original file: /npm/@lit/context@1.1.0/index.js
 *
 * Do NOT use SRI with dynamically generated files! More information: https://www.jsdelivr.com/using-sri-with-dynamic-files
 */
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */
class t extends Event{constructor(t,e,s){super("context-request",{bubbles:!0,composed:!0}),this.context=t,this.callback=e,this.subscribe=s??!1}}
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */function e(t){return t}
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */class s{constructor(t,e,s,i){if(this.subscribe=!1,this.provided=!1,this.value=void 0,this.t=(t,e)=>{this.unsubscribe&&(this.unsubscribe!==e&&(this.provided=!1,this.unsubscribe()),this.subscribe||this.unsubscribe()),this.value=t,this.host.requestUpdate(),this.provided&&!this.subscribe||(this.provided=!0,this.callback&&this.callback(t,e)),this.unsubscribe=e},this.host=t,void 0!==e.context){const t=e;this.context=t.context,this.callback=t.callback,this.subscribe=t.subscribe??!1}else this.context=e,this.callback=s,this.subscribe=i??!1;this.host.addController(this)}hostConnected(){this.dispatchRequest()}hostDisconnected(){this.unsubscribe&&(this.unsubscribe(),this.unsubscribe=void 0)}dispatchRequest(){this.host.dispatchEvent(new t(this.context,this.t,this.subscribe))}}
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */class i{get value(){return this.o}set value(t){this.setValue(t)}setValue(t,e=!1){const s=e||!Object.is(t,this.o);this.o=t,s&&this.updateObservers()}constructor(t){this.subscriptions=new Map,this.updateObservers=()=>{for(const[t,{disposer:e}]of this.subscriptions)t(this.o,e)},void 0!==t&&(this.value=t)}addCallback(t,e,s){if(!s)return void t(this.value);this.subscriptions.has(t)||this.subscriptions.set(t,{disposer:()=>{this.subscriptions.delete(t)},consumerHost:e});const{disposer:i}=this.subscriptions.get(t);t(this.value,i)}clearCallbacks(){this.subscriptions.clear()}}
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */class n extends Event{constructor(t){super("context-provider",{bubbles:!0,composed:!0}),this.context=t}}class o extends i{constructor(e,s,i){super(void 0!==s.context?s.initialValue:i),this.onContextRequest=t=>{const e=t.composedPath()[0];t.context===this.context&&e!==this.host&&(t.stopPropagation(),this.addCallback(t.callback,e,t.subscribe))},this.onProviderRequest=e=>{const s=e.composedPath()[0];if(e.context!==this.context||s===this.host)return;const i=new Set;for(const[e,{consumerHost:s}]of this.subscriptions)i.has(e)||(i.add(e),s.dispatchEvent(new t(this.context,e,!0)));e.stopPropagation()},this.host=e,void 0!==s.context?this.context=s.context:this.context=s,this.attachListeners(),this.host.addController?.(this)}attachListeners(){this.host.addEventListener("context-request",this.onContextRequest),this.host.addEventListener("context-provider",this.onProviderRequest)}hostConnected(){this.host.dispatchEvent(new n(this.context))}}
/**
 * @license
 * Copyright 2021 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */class c{constructor(){this.pendingContextRequests=new Map,this.onContextProvider=e=>{const s=this.pendingContextRequests.get(e.context);if(void 0===s)return;this.pendingContextRequests.delete(e.context);const{requests:i}=s;for(const{elementRef:s,callbackRef:n}of i){const i=s.deref(),o=n.deref();void 0===i||void 0===o||i.dispatchEvent(new t(e.context,o,!0))}},this.onContextRequest=t=>{if(!0!==t.subscribe)return;const e=t.composedPath()[0],s=t.callback;let i=this.pendingContextRequests.get(t.context);void 0===i&&this.pendingContextRequests.set(t.context,i={callbacks:new WeakMap,requests:[]});let n=i.callbacks.get(e);void 0===n&&i.callbacks.set(e,n=new WeakSet),n.has(s)||(n.add(s),i.requests.push({elementRef:new WeakRef(e),callbackRef:new WeakRef(s)}))}}attach(t){t.addEventListener("context-request",this.onContextRequest),t.addEventListener("context-provider",this.onContextProvider)}detach(t){t.removeEventListener("context-request",this.onContextRequest),t.removeEventListener("context-provider",this.onContextProvider)}}
/**
 * @license
 * Copyright 2017 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */function r({context:t}){return(e,s)=>{const i=new WeakMap;if("object"==typeof s)return s.addInitializer((function(){i.set(this,new o(this,{context:t}))})),{get(){return e.get.call(this)},set(t){return i.get(this)?.setValue(t),e.set.call(this,t)},init(t){return i.get(this)?.setValue(t),t}};{e.constructor.addInitializer((e=>{i.set(e,new o(e,{context:t}))}));const n=Object.getOwnPropertyDescriptor(e,s);let c;if(void 0===n){const t=new WeakMap;c={get:function(){return t.get(this)},set:function(e){i.get(this).setValue(e),t.set(this,e)},configurable:!0,enumerable:!0}}else{const t=n.set;c={...n,set:function(e){i.get(this).setValue(e),t?.call(this,e)}}}return void Object.defineProperty(e,s,c)}}}
/**
 * @license
 * Copyright 2022 Google LLC
 * SPDX-License-Identifier: BSD-3-Clause
 */function h({context:t,subscribe:e}){return(i,n)=>{"object"==typeof n?n.addInitializer((function(){new s(this,{context:t,callback:t=>{this[n.name]=t},subscribe:e})})):i.constructor.addInitializer((i=>{new s(i,{context:t,callback:t=>{i[n]=t},subscribe:e})}))}}export{s as ContextConsumer,t as ContextEvent,o as ContextProvider,c as ContextRoot,h as consume,e as createContext,r as provide};export default null;
//# sourceMappingURL=/sm/af0d13a3086b93879911b8b4d4acdad4c61007581cd66974997bf2d14b6d801f.map