(()=>{"use strict";var e={d:(t,n)=>{for(var r in n)e.o(n,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:n[r]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{hasBrowserEnv:()=>le,hasStandardBrowserEnv:()=>ue,hasStandardBrowserWebWorkerEnv:()=>fe});const n=window.wp.i18n,r=window.wc.wcBlocksRegistry,o=window.wp.htmlEntities,s=window.wc.wcSettings,i=window.wp.data,a=window.wc.wcBlocksData,c=window.React,l=(e,t,n,r)=>{const o=jQuery("#paymentSourceWalletsToken"),s=jQuery("#paymentCompleted"),i=jQuery(".wc-block-components-checkout-place-order-button");let a={country:n.county};"#powerBoardWalletApplePayButton"===t&&(a.wallets=["apple"],a.amount_label="Total"),"#powerBoardWalletPayPalButton"===t&&(a.pay_later=n.pay_later,a.style={height:55});let c=new window.cba.WalletButtons(t,n.resource.data.token,a);r?c.setEnv("preproduction_cba"):c.setEnv("production_cba"),c.onPaymentSuccessful((n=>{n.payment=e.replace("-","_"),o.val(JSON.stringify(n)),s.show(),jQuery(t).hide(),i.show(),i.click()})),c.onPaymentError((e=>{i.click()})),c.onPaymentInReview((n=>{n.payment=e.replace("-","_"),o.val(JSON.stringify(n)),s.show(),jQuery(t).hide(),i.show(),i.click()})),c.load()};function u(e,t){return function(){return e.apply(t,arguments)}}const{toString:d}=Object.prototype,{getPrototypeOf:f}=Object,p=(h=Object.create(null),e=>{const t=d.call(e);return h[t]||(h[t]=t.slice(8,-1).toLowerCase())});var h;const m=e=>(e=e.toLowerCase(),t=>p(t)===e),y=e=>t=>typeof t===e,{isArray:g}=Array,w=y("undefined"),b=m("ArrayBuffer"),E=y("string"),O=y("function"),S=y("number"),R=e=>null!==e&&"object"==typeof e,T=e=>{if("object"!==p(e))return!1;const t=f(e);return!(null!==t&&t!==Object.prototype&&null!==Object.getPrototypeOf(t)||Symbol.toStringTag in e||Symbol.iterator in e)},_=m("Date"),v=m("File"),A=m("Blob"),P=m("FileList"),j=m("URLSearchParams");function C(e,t,{allOwnKeys:n=!1}={}){if(null==e)return;let r,o;if("object"!=typeof e&&(e=[e]),g(e))for(r=0,o=e.length;r<o;r++)t.call(null,e[r],r,e);else{const o=n?Object.getOwnPropertyNames(e):Object.keys(e),s=o.length;let i;for(r=0;r<s;r++)i=o[r],t.call(null,e[i],i,e)}}function N(e,t){t=t.toLowerCase();const n=Object.keys(e);let r,o=n.length;for(;o-- >0;)if(r=n[o],t===r.toLowerCase())return r;return null}const x="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof self?self:"undefined"!=typeof window?window:global,k=e=>!w(e)&&e!==x,B=(D="undefined"!=typeof Uint8Array&&f(Uint8Array),e=>D&&e instanceof D);var D;const U=m("HTMLFormElement"),F=(({hasOwnProperty:e})=>(t,n)=>e.call(t,n))(Object.prototype),L=m("RegExp"),I=(e,t)=>{const n=Object.getOwnPropertyDescriptors(e),r={};C(n,((n,o)=>{let s;!1!==(s=t(n,o,e))&&(r[o]=s||n)})),Object.defineProperties(e,r)},q="abcdefghijklmnopqrstuvwxyz",M="0123456789",z={DIGIT:M,ALPHA:q,ALPHA_DIGIT:q+q.toUpperCase()+M},W=m("AsyncFunction"),H={isArray:g,isArrayBuffer:b,isBuffer:function(e){return null!==e&&!w(e)&&null!==e.constructor&&!w(e.constructor)&&O(e.constructor.isBuffer)&&e.constructor.isBuffer(e)},isFormData:e=>{let t;return e&&("function"==typeof FormData&&e instanceof FormData||O(e.append)&&("formdata"===(t=p(e))||"object"===t&&O(e.toString)&&"[object FormData]"===e.toString()))},isArrayBufferView:function(e){let t;return t="undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(e):e&&e.buffer&&b(e.buffer),t},isString:E,isNumber:S,isBoolean:e=>!0===e||!1===e,isObject:R,isPlainObject:T,isUndefined:w,isDate:_,isFile:v,isBlob:A,isRegExp:L,isFunction:O,isStream:e=>R(e)&&O(e.pipe),isURLSearchParams:j,isTypedArray:B,isFileList:P,forEach:C,merge:function e(){const{caseless:t}=k(this)&&this||{},n={},r=(r,o)=>{const s=t&&N(n,o)||o;T(n[s])&&T(r)?n[s]=e(n[s],r):T(r)?n[s]=e({},r):g(r)?n[s]=r.slice():n[s]=r};for(let e=0,t=arguments.length;e<t;e++)arguments[e]&&C(arguments[e],r);return n},extend:(e,t,n,{allOwnKeys:r}={})=>(C(t,((t,r)=>{n&&O(t)?e[r]=u(t,n):e[r]=t}),{allOwnKeys:r}),e),trim:e=>e.trim?e.trim():e.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,""),stripBOM:e=>(65279===e.charCodeAt(0)&&(e=e.slice(1)),e),inherits:(e,t,n,r)=>{e.prototype=Object.create(t.prototype,r),e.prototype.constructor=e,Object.defineProperty(e,"super",{value:t.prototype}),n&&Object.assign(e.prototype,n)},toFlatObject:(e,t,n,r)=>{let o,s,i;const a={};if(t=t||{},null==e)return t;do{for(o=Object.getOwnPropertyNames(e),s=o.length;s-- >0;)i=o[s],r&&!r(i,e,t)||a[i]||(t[i]=e[i],a[i]=!0);e=!1!==n&&f(e)}while(e&&(!n||n(e,t))&&e!==Object.prototype);return t},kindOf:p,kindOfTest:m,endsWith:(e,t,n)=>{e=String(e),(void 0===n||n>e.length)&&(n=e.length),n-=t.length;const r=e.indexOf(t,n);return-1!==r&&r===n},toArray:e=>{if(!e)return null;if(g(e))return e;let t=e.length;if(!S(t))return null;const n=new Array(t);for(;t-- >0;)n[t]=e[t];return n},forEachEntry:(e,t)=>{const n=(e&&e[Symbol.iterator]).call(e);let r;for(;(r=n.next())&&!r.done;){const n=r.value;t.call(e,n[0],n[1])}},matchAll:(e,t)=>{let n;const r=[];for(;null!==(n=e.exec(t));)r.push(n);return r},isHTMLForm:U,hasOwnProperty:F,hasOwnProp:F,reduceDescriptors:I,freezeMethods:e=>{I(e,((t,n)=>{if(O(e)&&-1!==["arguments","caller","callee"].indexOf(n))return!1;const r=e[n];O(r)&&(t.enumerable=!1,"writable"in t?t.writable=!1:t.set||(t.set=()=>{throw Error("Can not rewrite read-only method '"+n+"'")}))}))},toObjectSet:(e,t)=>{const n={},r=e=>{e.forEach((e=>{n[e]=!0}))};return g(e)?r(e):r(String(e).split(t)),n},toCamelCase:e=>e.toLowerCase().replace(/[-_\s]([a-z\d])(\w*)/g,(function(e,t,n){return t.toUpperCase()+n})),noop:()=>{},toFiniteNumber:(e,t)=>(e=+e,Number.isFinite(e)?e:t),findKey:N,global:x,isContextDefined:k,ALPHABET:z,generateString:(e=16,t=z.ALPHA_DIGIT)=>{let n="";const{length:r}=t;for(;e--;)n+=t[Math.random()*r|0];return n},isSpecCompliantForm:function(e){return!!(e&&O(e.append)&&"FormData"===e[Symbol.toStringTag]&&e[Symbol.iterator])},toJSONObject:e=>{const t=new Array(10),n=(e,r)=>{if(R(e)){if(t.indexOf(e)>=0)return;if(!("toJSON"in e)){t[r]=e;const o=g(e)?[]:{};return C(e,((e,t)=>{const s=n(e,r+1);!w(s)&&(o[t]=s)})),t[r]=void 0,o}}return e};return n(e,0)},isAsyncFn:W,isThenable:e=>e&&(R(e)||O(e))&&O(e.then)&&O(e.catch)};function J(e,t,n,r,o){Error.call(this),Error.captureStackTrace?Error.captureStackTrace(this,this.constructor):this.stack=(new Error).stack,this.message=e,this.name="AxiosError",t&&(this.code=t),n&&(this.config=n),r&&(this.request=r),o&&(this.response=o)}H.inherits(J,Error,{toJSON:function(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:H.toJSONObject(this.config),code:this.code,status:this.response&&this.response.status?this.response.status:null}}});const K=J.prototype,V={};["ERR_BAD_OPTION_VALUE","ERR_BAD_OPTION","ECONNABORTED","ETIMEDOUT","ERR_NETWORK","ERR_FR_TOO_MANY_REDIRECTS","ERR_DEPRECATED","ERR_BAD_RESPONSE","ERR_BAD_REQUEST","ERR_CANCELED","ERR_NOT_SUPPORT","ERR_INVALID_URL"].forEach((e=>{V[e]={value:e}})),Object.defineProperties(J,V),Object.defineProperty(K,"isAxiosError",{value:!0}),J.from=(e,t,n,r,o,s)=>{const i=Object.create(K);return H.toFlatObject(e,i,(function(e){return e!==Error.prototype}),(e=>"isAxiosError"!==e)),J.call(i,e.message,t,n,r,o),i.cause=e,i.name=e.name,s&&Object.assign(i,s),i};const Q=J;function $(e){return H.isPlainObject(e)||H.isArray(e)}function G(e){return H.endsWith(e,"[]")?e.slice(0,-2):e}function X(e,t,n){return e?e.concat(t).map((function(e,t){return e=G(e),!n&&t?"["+e+"]":e})).join(n?".":""):t}const Y=H.toFlatObject(H,{},null,(function(e){return/^is[A-Z]/.test(e)})),Z=function(e,t,n){if(!H.isObject(e))throw new TypeError("target must be an object");t=t||new FormData;const r=(n=H.toFlatObject(n,{metaTokens:!0,dots:!1,indexes:!1},!1,(function(e,t){return!H.isUndefined(t[e])}))).metaTokens,o=n.visitor||l,s=n.dots,i=n.indexes,a=(n.Blob||"undefined"!=typeof Blob&&Blob)&&H.isSpecCompliantForm(t);if(!H.isFunction(o))throw new TypeError("visitor must be a function");function c(e){if(null===e)return"";if(H.isDate(e))return e.toISOString();if(!a&&H.isBlob(e))throw new Q("Blob is not supported. Use a Buffer instead.");return H.isArrayBuffer(e)||H.isTypedArray(e)?a&&"function"==typeof Blob?new Blob([e]):Buffer.from(e):e}function l(e,n,o){let a=e;if(e&&!o&&"object"==typeof e)if(H.endsWith(n,"{}"))n=r?n:n.slice(0,-2),e=JSON.stringify(e);else if(H.isArray(e)&&function(e){return H.isArray(e)&&!e.some($)}(e)||(H.isFileList(e)||H.endsWith(n,"[]"))&&(a=H.toArray(e)))return n=G(n),a.forEach((function(e,r){!H.isUndefined(e)&&null!==e&&t.append(!0===i?X([n],r,s):null===i?n:n+"[]",c(e))})),!1;return!!$(e)||(t.append(X(o,n,s),c(e)),!1)}const u=[],d=Object.assign(Y,{defaultVisitor:l,convertValue:c,isVisitable:$});if(!H.isObject(e))throw new TypeError("data must be an object");return function e(n,r){if(!H.isUndefined(n)){if(-1!==u.indexOf(n))throw Error("Circular reference detected in "+r.join("."));u.push(n),H.forEach(n,(function(n,s){!0===(!(H.isUndefined(n)||null===n)&&o.call(t,n,H.isString(s)?s.trim():s,r,d))&&e(n,r?r.concat(s):[s])})),u.pop()}}(e),t};function ee(e){const t={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+","%00":"\0"};return encodeURIComponent(e).replace(/[!'()~]|%20|%00/g,(function(e){return t[e]}))}function te(e,t){this._pairs=[],e&&Z(e,this,t)}const ne=te.prototype;ne.append=function(e,t){this._pairs.push([e,t])},ne.toString=function(e){const t=e?function(t){return e.call(this,t,ee)}:ee;return this._pairs.map((function(e){return t(e[0])+"="+t(e[1])}),"").join("&")};const re=te;function oe(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}function se(e,t,n){if(!t)return e;const r=n&&n.encode||oe,o=n&&n.serialize;let s;if(s=o?o(t,n):H.isURLSearchParams(t)?t.toString():new re(t,n).toString(r),s){const t=e.indexOf("#");-1!==t&&(e=e.slice(0,t)),e+=(-1===e.indexOf("?")?"?":"&")+s}return e}const ie=class{constructor(){this.handlers=[]}use(e,t,n){return this.handlers.push({fulfilled:e,rejected:t,synchronous:!!n&&n.synchronous,runWhen:n?n.runWhen:null}),this.handlers.length-1}eject(e){this.handlers[e]&&(this.handlers[e]=null)}clear(){this.handlers&&(this.handlers=[])}forEach(e){H.forEach(this.handlers,(function(t){null!==t&&e(t)}))}},ae={silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},ce={isBrowser:!0,classes:{URLSearchParams:"undefined"!=typeof URLSearchParams?URLSearchParams:re,FormData:"undefined"!=typeof FormData?FormData:null,Blob:"undefined"!=typeof Blob?Blob:null},protocols:["http","https","file","blob","url","data"]},le="undefined"!=typeof window&&"undefined"!=typeof document,ue=(de="undefined"!=typeof navigator&&navigator.product,le&&["ReactNative","NativeScript","NS"].indexOf(de)<0);var de;const fe="undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope&&"function"==typeof self.importScripts,pe={...t,...ce},he=function(e){function t(e,n,r,o){let s=e[o++];if("__proto__"===s)return!0;const i=Number.isFinite(+s),a=o>=e.length;return s=!s&&H.isArray(r)?r.length:s,a?(H.hasOwnProp(r,s)?r[s]=[r[s],n]:r[s]=n,!i):(r[s]&&H.isObject(r[s])||(r[s]=[]),t(e,n,r[s],o)&&H.isArray(r[s])&&(r[s]=function(e){const t={},n=Object.keys(e);let r;const o=n.length;let s;for(r=0;r<o;r++)s=n[r],t[s]=e[s];return t}(r[s])),!i)}if(H.isFormData(e)&&H.isFunction(e.entries)){const n={};return H.forEachEntry(e,((e,r)=>{t(function(e){return H.matchAll(/\w+|\[(\w*)]/g,e).map((e=>"[]"===e[0]?"":e[1]||e[0]))}(e),r,n,0)})),n}return null},me={transitional:ae,adapter:["xhr","http"],transformRequest:[function(e,t){const n=t.getContentType()||"",r=n.indexOf("application/json")>-1,o=H.isObject(e);if(o&&H.isHTMLForm(e)&&(e=new FormData(e)),H.isFormData(e))return r?JSON.stringify(he(e)):e;if(H.isArrayBuffer(e)||H.isBuffer(e)||H.isStream(e)||H.isFile(e)||H.isBlob(e))return e;if(H.isArrayBufferView(e))return e.buffer;if(H.isURLSearchParams(e))return t.setContentType("application/x-www-form-urlencoded;charset=utf-8",!1),e.toString();let s;if(o){if(n.indexOf("application/x-www-form-urlencoded")>-1)return function(e,t){return Z(e,new pe.classes.URLSearchParams,Object.assign({visitor:function(e,t,n,r){return pe.isNode&&H.isBuffer(e)?(this.append(t,e.toString("base64")),!1):r.defaultVisitor.apply(this,arguments)}},t))}(e,this.formSerializer).toString();if((s=H.isFileList(e))||n.indexOf("multipart/form-data")>-1){const t=this.env&&this.env.FormData;return Z(s?{"files[]":e}:e,t&&new t,this.formSerializer)}}return o||r?(t.setContentType("application/json",!1),function(e,t,n){if(H.isString(e))try{return(0,JSON.parse)(e),H.trim(e)}catch(e){if("SyntaxError"!==e.name)throw e}return(0,JSON.stringify)(e)}(e)):e}],transformResponse:[function(e){const t=this.transitional||me.transitional,n=t&&t.forcedJSONParsing,r="json"===this.responseType;if(e&&H.isString(e)&&(n&&!this.responseType||r)){const n=!(t&&t.silentJSONParsing)&&r;try{return JSON.parse(e)}catch(e){if(n){if("SyntaxError"===e.name)throw Q.from(e,Q.ERR_BAD_RESPONSE,this,null,this.response);throw e}}}return e}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,env:{FormData:pe.classes.FormData,Blob:pe.classes.Blob},validateStatus:function(e){return e>=200&&e<300},headers:{common:{Accept:"application/json, text/plain, */*","Content-Type":void 0}}};H.forEach(["delete","get","head","post","put","patch"],(e=>{me.headers[e]={}}));const ye=me,ge=H.toObjectSet(["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"]),we=Symbol("internals");function be(e){return e&&String(e).trim().toLowerCase()}function Ee(e){return!1===e||null==e?e:H.isArray(e)?e.map(Ee):String(e)}function Oe(e,t,n,r,o){return H.isFunction(r)?r.call(this,t,n):(o&&(t=n),H.isString(t)?H.isString(r)?-1!==t.indexOf(r):H.isRegExp(r)?r.test(t):void 0:void 0)}class Se{constructor(e){e&&this.set(e)}set(e,t,n){const r=this;function o(e,t,n){const o=be(t);if(!o)throw new Error("header name must be a non-empty string");const s=H.findKey(r,o);(!s||void 0===r[s]||!0===n||void 0===n&&!1!==r[s])&&(r[s||t]=Ee(e))}const s=(e,t)=>H.forEach(e,((e,n)=>o(e,n,t)));return H.isPlainObject(e)||e instanceof this.constructor?s(e,t):H.isString(e)&&(e=e.trim())&&!/^[-_a-zA-Z0-9^`|~,!#$%&'*+.]+$/.test(e.trim())?s((e=>{const t={};let n,r,o;return e&&e.split("\n").forEach((function(e){o=e.indexOf(":"),n=e.substring(0,o).trim().toLowerCase(),r=e.substring(o+1).trim(),!n||t[n]&&ge[n]||("set-cookie"===n?t[n]?t[n].push(r):t[n]=[r]:t[n]=t[n]?t[n]+", "+r:r)})),t})(e),t):null!=e&&o(t,e,n),this}get(e,t){if(e=be(e)){const n=H.findKey(this,e);if(n){const e=this[n];if(!t)return e;if(!0===t)return function(e){const t=Object.create(null),n=/([^\s,;=]+)\s*(?:=\s*([^,;]+))?/g;let r;for(;r=n.exec(e);)t[r[1]]=r[2];return t}(e);if(H.isFunction(t))return t.call(this,e,n);if(H.isRegExp(t))return t.exec(e);throw new TypeError("parser must be boolean|regexp|function")}}}has(e,t){if(e=be(e)){const n=H.findKey(this,e);return!(!n||void 0===this[n]||t&&!Oe(0,this[n],n,t))}return!1}delete(e,t){const n=this;let r=!1;function o(e){if(e=be(e)){const o=H.findKey(n,e);!o||t&&!Oe(0,n[o],o,t)||(delete n[o],r=!0)}}return H.isArray(e)?e.forEach(o):o(e),r}clear(e){const t=Object.keys(this);let n=t.length,r=!1;for(;n--;){const o=t[n];e&&!Oe(0,this[o],o,e,!0)||(delete this[o],r=!0)}return r}normalize(e){const t=this,n={};return H.forEach(this,((r,o)=>{const s=H.findKey(n,o);if(s)return t[s]=Ee(r),void delete t[o];const i=e?function(e){return e.trim().toLowerCase().replace(/([a-z\d])(\w*)/g,((e,t,n)=>t.toUpperCase()+n))}(o):String(o).trim();i!==o&&delete t[o],t[i]=Ee(r),n[i]=!0})),this}concat(...e){return this.constructor.concat(this,...e)}toJSON(e){const t=Object.create(null);return H.forEach(this,((n,r)=>{null!=n&&!1!==n&&(t[r]=e&&H.isArray(n)?n.join(", "):n)})),t}[Symbol.iterator](){return Object.entries(this.toJSON())[Symbol.iterator]()}toString(){return Object.entries(this.toJSON()).map((([e,t])=>e+": "+t)).join("\n")}get[Symbol.toStringTag](){return"AxiosHeaders"}static from(e){return e instanceof this?e:new this(e)}static concat(e,...t){const n=new this(e);return t.forEach((e=>n.set(e))),n}static accessor(e){const t=(this[we]=this[we]={accessors:{}}).accessors,n=this.prototype;function r(e){const r=be(e);t[r]||(function(e,t){const n=H.toCamelCase(" "+t);["get","set","has"].forEach((r=>{Object.defineProperty(e,r+n,{value:function(e,n,o){return this[r].call(this,t,e,n,o)},configurable:!0})}))}(n,e),t[r]=!0)}return H.isArray(e)?e.forEach(r):r(e),this}}Se.accessor(["Content-Type","Content-Length","Accept","Accept-Encoding","User-Agent","Authorization"]),H.reduceDescriptors(Se.prototype,(({value:e},t)=>{let n=t[0].toUpperCase()+t.slice(1);return{get:()=>e,set(e){this[n]=e}}})),H.freezeMethods(Se);const Re=Se;function Te(e,t){const n=this||ye,r=t||n,o=Re.from(r.headers);let s=r.data;return H.forEach(e,(function(e){s=e.call(n,s,o.normalize(),t?t.status:void 0)})),o.normalize(),s}function _e(e){return!(!e||!e.__CANCEL__)}function ve(e,t,n){Q.call(this,null==e?"canceled":e,Q.ERR_CANCELED,t,n),this.name="CanceledError"}H.inherits(ve,Q,{__CANCEL__:!0});const Ae=ve,Pe=pe.hasStandardBrowserEnv?{write(e,t,n,r,o,s){const i=[e+"="+encodeURIComponent(t)];H.isNumber(n)&&i.push("expires="+new Date(n).toGMTString()),H.isString(r)&&i.push("path="+r),H.isString(o)&&i.push("domain="+o),!0===s&&i.push("secure"),document.cookie=i.join("; ")},read(e){const t=document.cookie.match(new RegExp("(^|;\\s*)("+e+")=([^;]*)"));return t?decodeURIComponent(t[3]):null},remove(e){this.write(e,"",Date.now()-864e5)}}:{write(){},read:()=>null,remove(){}};function je(e,t){return e&&!/^([a-z][a-z\d+\-.]*:)?\/\//i.test(t)?function(e,t){return t?e.replace(/\/?\/$/,"")+"/"+t.replace(/^\/+/,""):e}(e,t):t}const Ce=pe.hasStandardBrowserEnv?function(){const e=/(msie|trident)/i.test(navigator.userAgent),t=document.createElement("a");let n;function r(n){let r=n;return e&&(t.setAttribute("href",r),r=t.href),t.setAttribute("href",r),{href:t.href,protocol:t.protocol?t.protocol.replace(/:$/,""):"",host:t.host,search:t.search?t.search.replace(/^\?/,""):"",hash:t.hash?t.hash.replace(/^#/,""):"",hostname:t.hostname,port:t.port,pathname:"/"===t.pathname.charAt(0)?t.pathname:"/"+t.pathname}}return n=r(window.location.href),function(e){const t=H.isString(e)?r(e):e;return t.protocol===n.protocol&&t.host===n.host}}():function(){return!0};function Ne(e,t){let n=0;const r=function(e,t){e=e||10;const n=new Array(e),r=new Array(e);let o,s=0,i=0;return t=void 0!==t?t:1e3,function(a){const c=Date.now(),l=r[i];o||(o=c),n[s]=a,r[s]=c;let u=i,d=0;for(;u!==s;)d+=n[u++],u%=e;if(s=(s+1)%e,s===i&&(i=(i+1)%e),c-o<t)return;const f=l&&c-l;return f?Math.round(1e3*d/f):void 0}}(50,250);return o=>{const s=o.loaded,i=o.lengthComputable?o.total:void 0,a=s-n,c=r(a);n=s;const l={loaded:s,total:i,progress:i?s/i:void 0,bytes:a,rate:c||void 0,estimated:c&&i&&s<=i?(i-s)/c:void 0,event:o};l[t?"download":"upload"]=!0,e(l)}}const xe={http:null,xhr:"undefined"!=typeof XMLHttpRequest&&function(e){return new Promise((function(t,n){let r=e.data;const o=Re.from(e.headers).normalize();let s,i,{responseType:a,withXSRFToken:c}=e;function l(){e.cancelToken&&e.cancelToken.unsubscribe(s),e.signal&&e.signal.removeEventListener("abort",s)}if(H.isFormData(r))if(pe.hasStandardBrowserEnv||pe.hasStandardBrowserWebWorkerEnv)o.setContentType(!1);else if(!1!==(i=o.getContentType())){const[e,...t]=i?i.split(";").map((e=>e.trim())).filter(Boolean):[];o.setContentType([e||"multipart/form-data",...t].join("; "))}let u=new XMLHttpRequest;if(e.auth){const t=e.auth.username||"",n=e.auth.password?unescape(encodeURIComponent(e.auth.password)):"";o.set("Authorization","Basic "+btoa(t+":"+n))}const d=je(e.baseURL,e.url);function f(){if(!u)return;const r=Re.from("getAllResponseHeaders"in u&&u.getAllResponseHeaders());!function(e,t,n){const r=n.config.validateStatus;n.status&&r&&!r(n.status)?t(new Q("Request failed with status code "+n.status,[Q.ERR_BAD_REQUEST,Q.ERR_BAD_RESPONSE][Math.floor(n.status/100)-4],n.config,n.request,n)):e(n)}((function(e){t(e),l()}),(function(e){n(e),l()}),{data:a&&"text"!==a&&"json"!==a?u.response:u.responseText,status:u.status,statusText:u.statusText,headers:r,config:e,request:u}),u=null}if(u.open(e.method.toUpperCase(),se(d,e.params,e.paramsSerializer),!0),u.timeout=e.timeout,"onloadend"in u?u.onloadend=f:u.onreadystatechange=function(){u&&4===u.readyState&&(0!==u.status||u.responseURL&&0===u.responseURL.indexOf("file:"))&&setTimeout(f)},u.onabort=function(){u&&(n(new Q("Request aborted",Q.ECONNABORTED,e,u)),u=null)},u.onerror=function(){n(new Q("Network Error",Q.ERR_NETWORK,e,u)),u=null},u.ontimeout=function(){let t=e.timeout?"timeout of "+e.timeout+"ms exceeded":"timeout exceeded";const r=e.transitional||ae;e.timeoutErrorMessage&&(t=e.timeoutErrorMessage),n(new Q(t,r.clarifyTimeoutError?Q.ETIMEDOUT:Q.ECONNABORTED,e,u)),u=null},pe.hasStandardBrowserEnv&&(c&&H.isFunction(c)&&(c=c(e)),c||!1!==c&&Ce(d))){const t=e.xsrfHeaderName&&e.xsrfCookieName&&Pe.read(e.xsrfCookieName);t&&o.set(e.xsrfHeaderName,t)}void 0===r&&o.setContentType(null),"setRequestHeader"in u&&H.forEach(o.toJSON(),(function(e,t){u.setRequestHeader(t,e)})),H.isUndefined(e.withCredentials)||(u.withCredentials=!!e.withCredentials),a&&"json"!==a&&(u.responseType=e.responseType),"function"==typeof e.onDownloadProgress&&u.addEventListener("progress",Ne(e.onDownloadProgress,!0)),"function"==typeof e.onUploadProgress&&u.upload&&u.upload.addEventListener("progress",Ne(e.onUploadProgress)),(e.cancelToken||e.signal)&&(s=t=>{u&&(n(!t||t.type?new Ae(null,e,u):t),u.abort(),u=null)},e.cancelToken&&e.cancelToken.subscribe(s),e.signal&&(e.signal.aborted?s():e.signal.addEventListener("abort",s)));const p=function(e){const t=/^([-+\w]{1,25})(:?\/\/|:)/.exec(e);return t&&t[1]||""}(d);p&&-1===pe.protocols.indexOf(p)?n(new Q("Unsupported protocol "+p+":",Q.ERR_BAD_REQUEST,e)):u.send(r||null)}))}};H.forEach(xe,((e,t)=>{if(e){try{Object.defineProperty(e,"name",{value:t})}catch(e){}Object.defineProperty(e,"adapterName",{value:t})}}));const ke=e=>`- ${e}`,Be=e=>H.isFunction(e)||null===e||!1===e,De=e=>{e=H.isArray(e)?e:[e];const{length:t}=e;let n,r;const o={};for(let s=0;s<t;s++){let t;if(n=e[s],r=n,!Be(n)&&(r=xe[(t=String(n)).toLowerCase()],void 0===r))throw new Q(`Unknown adapter '${t}'`);if(r)break;o[t||"#"+s]=r}if(!r){const e=Object.entries(o).map((([e,t])=>`adapter ${e} `+(!1===t?"is not supported by the environment":"is not available in the build")));let n=t?e.length>1?"since :\n"+e.map(ke).join("\n"):" "+ke(e[0]):"as no adapter specified";throw new Q("There is no suitable adapter to dispatch the request "+n,"ERR_NOT_SUPPORT")}return r};function Ue(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new Ae(null,e)}function Fe(e){return Ue(e),e.headers=Re.from(e.headers),e.data=Te.call(e,e.transformRequest),-1!==["post","put","patch"].indexOf(e.method)&&e.headers.setContentType("application/x-www-form-urlencoded",!1),De(e.adapter||ye.adapter)(e).then((function(t){return Ue(e),t.data=Te.call(e,e.transformResponse,t),t.headers=Re.from(t.headers),t}),(function(t){return _e(t)||(Ue(e),t&&t.response&&(t.response.data=Te.call(e,e.transformResponse,t.response),t.response.headers=Re.from(t.response.headers))),Promise.reject(t)}))}const Le=e=>e instanceof Re?e.toJSON():e;function Ie(e,t){t=t||{};const n={};function r(e,t,n){return H.isPlainObject(e)&&H.isPlainObject(t)?H.merge.call({caseless:n},e,t):H.isPlainObject(t)?H.merge({},t):H.isArray(t)?t.slice():t}function o(e,t,n){return H.isUndefined(t)?H.isUndefined(e)?void 0:r(void 0,e,n):r(e,t,n)}function s(e,t){if(!H.isUndefined(t))return r(void 0,t)}function i(e,t){return H.isUndefined(t)?H.isUndefined(e)?void 0:r(void 0,e):r(void 0,t)}function a(n,o,s){return s in t?r(n,o):s in e?r(void 0,n):void 0}const c={url:s,method:s,data:s,baseURL:i,transformRequest:i,transformResponse:i,paramsSerializer:i,timeout:i,timeoutMessage:i,withCredentials:i,withXSRFToken:i,adapter:i,responseType:i,xsrfCookieName:i,xsrfHeaderName:i,onUploadProgress:i,onDownloadProgress:i,decompress:i,maxContentLength:i,maxBodyLength:i,beforeRedirect:i,transport:i,httpAgent:i,httpsAgent:i,cancelToken:i,socketPath:i,responseEncoding:i,validateStatus:a,headers:(e,t)=>o(Le(e),Le(t),!0)};return H.forEach(Object.keys(Object.assign({},e,t)),(function(r){const s=c[r]||o,i=s(e[r],t[r],r);H.isUndefined(i)&&s!==a||(n[r]=i)})),n}const qe={};["object","boolean","number","function","string","symbol"].forEach(((e,t)=>{qe[e]=function(n){return typeof n===e||"a"+(t<1?"n ":" ")+e}}));const Me={};qe.transitional=function(e,t,n){function r(e,t){return"[Axios v1.6.7] Transitional option '"+e+"'"+t+(n?". "+n:"")}return(n,o,s)=>{if(!1===e)throw new Q(r(o," has been removed"+(t?" in "+t:"")),Q.ERR_DEPRECATED);return t&&!Me[o]&&(Me[o]=!0,console.warn(r(o," has been deprecated since v"+t+" and will be removed in the near future"))),!e||e(n,o,s)}};const ze={assertOptions:function(e,t,n){if("object"!=typeof e)throw new Q("options must be an object",Q.ERR_BAD_OPTION_VALUE);const r=Object.keys(e);let o=r.length;for(;o-- >0;){const s=r[o],i=t[s];if(i){const t=e[s],n=void 0===t||i(t,s,e);if(!0!==n)throw new Q("option "+s+" must be "+n,Q.ERR_BAD_OPTION_VALUE)}else if(!0!==n)throw new Q("Unknown option "+s,Q.ERR_BAD_OPTION)}},validators:qe},We=ze.validators;class He{constructor(e){this.defaults=e,this.interceptors={request:new ie,response:new ie}}async request(e,t){try{return await this._request(e,t)}catch(e){if(e instanceof Error){let t;Error.captureStackTrace?Error.captureStackTrace(t={}):t=new Error;const n=t.stack?t.stack.replace(/^.+\n/,""):"";e.stack?n&&!String(e.stack).endsWith(n.replace(/^.+\n.+\n/,""))&&(e.stack+="\n"+n):e.stack=n}throw e}}_request(e,t){"string"==typeof e?(t=t||{}).url=e:t=e||{},t=Ie(this.defaults,t);const{transitional:n,paramsSerializer:r,headers:o}=t;void 0!==n&&ze.assertOptions(n,{silentJSONParsing:We.transitional(We.boolean),forcedJSONParsing:We.transitional(We.boolean),clarifyTimeoutError:We.transitional(We.boolean)},!1),null!=r&&(H.isFunction(r)?t.paramsSerializer={serialize:r}:ze.assertOptions(r,{encode:We.function,serialize:We.function},!0)),t.method=(t.method||this.defaults.method||"get").toLowerCase();let s=o&&H.merge(o.common,o[t.method]);o&&H.forEach(["delete","get","head","post","put","patch","common"],(e=>{delete o[e]})),t.headers=Re.concat(s,o);const i=[];let a=!0;this.interceptors.request.forEach((function(e){"function"==typeof e.runWhen&&!1===e.runWhen(t)||(a=a&&e.synchronous,i.unshift(e.fulfilled,e.rejected))}));const c=[];let l;this.interceptors.response.forEach((function(e){c.push(e.fulfilled,e.rejected)}));let u,d=0;if(!a){const e=[Fe.bind(this),void 0];for(e.unshift.apply(e,i),e.push.apply(e,c),u=e.length,l=Promise.resolve(t);d<u;)l=l.then(e[d++],e[d++]);return l}u=i.length;let f=t;for(d=0;d<u;){const e=i[d++],t=i[d++];try{f=e(f)}catch(e){t.call(this,e);break}}try{l=Fe.call(this,f)}catch(e){return Promise.reject(e)}for(d=0,u=c.length;d<u;)l=l.then(c[d++],c[d++]);return l}getUri(e){return se(je((e=Ie(this.defaults,e)).baseURL,e.url),e.params,e.paramsSerializer)}}H.forEach(["delete","get","head","options"],(function(e){He.prototype[e]=function(t,n){return this.request(Ie(n||{},{method:e,url:t,data:(n||{}).data}))}})),H.forEach(["post","put","patch"],(function(e){function t(t){return function(n,r,o){return this.request(Ie(o||{},{method:e,headers:t?{"Content-Type":"multipart/form-data"}:{},url:n,data:r}))}}He.prototype[e]=t(),He.prototype[e+"Form"]=t(!0)}));const Je=He;class Ke{constructor(e){if("function"!=typeof e)throw new TypeError("executor must be a function.");let t;this.promise=new Promise((function(e){t=e}));const n=this;this.promise.then((e=>{if(!n._listeners)return;let t=n._listeners.length;for(;t-- >0;)n._listeners[t](e);n._listeners=null})),this.promise.then=e=>{let t;const r=new Promise((e=>{n.subscribe(e),t=e})).then(e);return r.cancel=function(){n.unsubscribe(t)},r},e((function(e,r,o){n.reason||(n.reason=new Ae(e,r,o),t(n.reason))}))}throwIfRequested(){if(this.reason)throw this.reason}subscribe(e){this.reason?e(this.reason):this._listeners?this._listeners.push(e):this._listeners=[e]}unsubscribe(e){if(!this._listeners)return;const t=this._listeners.indexOf(e);-1!==t&&this._listeners.splice(t,1)}static source(){let e;return{token:new Ke((function(t){e=t})),cancel:e}}}const Ve=Ke,Qe={Continue:100,SwitchingProtocols:101,Processing:102,EarlyHints:103,Ok:200,Created:201,Accepted:202,NonAuthoritativeInformation:203,NoContent:204,ResetContent:205,PartialContent:206,MultiStatus:207,AlreadyReported:208,ImUsed:226,MultipleChoices:300,MovedPermanently:301,Found:302,SeeOther:303,NotModified:304,UseProxy:305,Unused:306,TemporaryRedirect:307,PermanentRedirect:308,BadRequest:400,Unauthorized:401,PaymentRequired:402,Forbidden:403,NotFound:404,MethodNotAllowed:405,NotAcceptable:406,ProxyAuthenticationRequired:407,RequestTimeout:408,Conflict:409,Gone:410,LengthRequired:411,PreconditionFailed:412,PayloadTooLarge:413,UriTooLong:414,UnsupportedMediaType:415,RangeNotSatisfiable:416,ExpectationFailed:417,ImATeapot:418,MisdirectedRequest:421,UnprocessableEntity:422,Locked:423,FailedDependency:424,TooEarly:425,UpgradeRequired:426,PreconditionRequired:428,TooManyRequests:429,RequestHeaderFieldsTooLarge:431,UnavailableForLegalReasons:451,InternalServerError:500,NotImplemented:501,BadGateway:502,ServiceUnavailable:503,GatewayTimeout:504,HttpVersionNotSupported:505,VariantAlsoNegotiates:506,InsufficientStorage:507,LoopDetected:508,NotExtended:510,NetworkAuthenticationRequired:511};Object.entries(Qe).forEach((([e,t])=>{Qe[t]=e}));const $e=Qe,Ge=function e(t){const n=new Je(t),r=u(Je.prototype.request,n);return H.extend(r,Je.prototype,n,{allOwnKeys:!0}),H.extend(r,n,null,{allOwnKeys:!0}),r.create=function(n){return e(Ie(t,n))},r}(ye);Ge.Axios=Je,Ge.CanceledError=Ae,Ge.CancelToken=Ve,Ge.isCancel=_e,Ge.VERSION="1.6.7",Ge.toFormData=Z,Ge.AxiosError=Q,Ge.Cancel=Ge.CanceledError,Ge.all=function(e){return Promise.all(e)},Ge.spread=function(e){return function(t){return e.apply(null,t)}},Ge.isAxiosError=function(e){return H.isObject(e)&&!0===e.isAxiosError},Ge.mergeConfig=Ie,Ge.AxiosHeaders=Re,Ge.formToJSON=e=>he(H.isHTMLForm(e)?new FormData(e):e),Ge.getAdapter=De,Ge.HttpStatusCode=$e,Ge.default=Ge;const Xe=Ge,Ye="power_board",Ze={validationError:(0,n.__)("Please fill in the required fields of the form to display payment methods",Ye),fillDataError:(0,n.__)("The payment service does not accept payment. Please try again later or choose another payment method.",Ye),notAvailable:(0,n.__)("Payment method is not avalaible for your country!!!",Ye)};let et={initData:null,wasInit:!1};((e,t,u,d)=>{const f=`power_board_${e}_wallet_block_data`,p=`power_board_${e}_wallets_gateway`,h=(0,s.getSetting)(f,{}),m=(0,o.decodeEntities)(h.title)||(0,n.__)("Power Board PayPal",Ye),y=t=>{const n=(0,i.select)(a.CHECKOUT_STORE_KEY),r=(0,i.select)(a.CART_STORE_KEY),{eventRegistration:s,emitResponse:f}=t,{onPaymentSetup:p,onCheckoutValidation:m}=s,y=r.getCustomerData().billingAddress,g=jQuery(".power-board-country-available"),w=jQuery("#paymentCompleted");let b=((e,t)=>{for(let n=0;n<t.length;n++)if(!e.hasOwnProperty(t[n])||!e[t[n]])return!1;return!0})(y,d);jQuery(".wc-block-components-checkout-place-order-button").hide();let E=jQuery("#"+u).length;if("true"===new URLSearchParams(window.location.search).get("afterpay_success"))w.show();else if(!b||et.initData||et.wasInit)b&&et.initData&&!E&&(g.hide(),setTimeout((()=>{l(e,"#"+u,et.initData,h.isSandbox)}),100));else{g.hide(),et.wasInit=!0;let t={type:e,order_id:n.getOrderId(),total:r.getCartTotals(),address:r.getCustomerData().billingAddress,shipping_address:r.getCustomerData().shippingAddress,shipping_rates:r.getShippingRates(),items:r.getCartData().items};Xe.post("/wp-json/power_board/v1/wallets/charge",t).then((t=>{et.initData=t.data,setTimeout((()=>{l(e,"#"+u,et.initData,h.isSandbox)}),100)})).catch((e=>{et.wasInit=!1}))}(0,c.useEffect)((()=>{const e=m((async e=>b&&!(!document.getElementById("paymentSourceWalletsToken").value||"false"===new URLSearchParams(window.location.search).get("afterpay_success"))||{type:f.responseTypes.ERROR,errorMessage:Ze.fillDataError})),t=p((async e=>document.getElementById("paymentSourceWalletsToken").value&&"false"!==new URLSearchParams(window.location.search).get("afterpay_success")?{type:f.responseTypes.SUCCESS,meta:{paymentMethodData:{payment_response:document.getElementById("paymentSourceWalletsToken").value,wallets:JSON.stringify(h.wallets)}}}:{type:f.responseTypes.ERROR,errorMessage:Ze.fillDataError}));return()=>{t()&&e()&&onEmitter}}),[f.responseTypes.ERROR,f.responseTypes.SUCCESS,p]);const O=(0,c.createElement)("div",null,(0,o.decodeEntities)(h.description||"")),S=(0,c.createElement)("input",{type:"hidden",id:"paymentSourceWalletsToken"}),R=(0,c.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),T=[(0,c.createElement)("div",{id:u,class:"paudock-wallets-buttons"})];return(0,c.createElement)("div",null,O,(0,c.createElement)("div",{class:"logo-comm-bank"},(0,c.createElement)("img",{src:"/wp-content/plugins/power_board/assets/images/logo.png"})),R,(0,c.createElement)("div",{id:"powerBoardWidgetWallets",class:"power-board-widget-content"},...T),(0,c.createElement)("div",{class:"power-board-validation-error",style:{display:b?"none":""}},Ze.validationError),(0,c.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},Ze.notAvailable),S)},g={name:p,label:(0,c.createElement)((e=>{const{PaymentMethodLabel:t}=e.components;return(0,c.createElement)(t,{text:m})}),null),content:(0,c.createElement)(y,null),edit:(0,c.createElement)(y,null),canMakePayment:()=>!0,ariaLabel:m,supports:{features:h.supports}};(0,r.registerPaymentMethod)(g)})("pay-pal",0,"powerBoardWalletPayPalButton",["first_name","last_name","email","address_1","city","state","country","postcode"])})();