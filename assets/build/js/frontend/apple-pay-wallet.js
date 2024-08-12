(()=>{"use strict";var e={d:(t,n)=>{for(var r in n)e.o(n,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:n[r]})},o:(e,t)=>Object.prototype.hasOwnProperty.call(e,t),r:e=>{"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{hasBrowserEnv:()=>he,hasStandardBrowserEnv:()=>me,hasStandardBrowserWebWorkerEnv:()=>ge,origin:()=>we});const n=window.wp.i18n,r=window.wc.wcBlocksRegistry,o=window.wp.htmlEntities,s=window.wc.wcSettings,i=window.wp.data,a=window.wc.wcBlocksData,c=window.React;let l={};const u=(e,t,n,r)=>{const o=jQuery("#paymentSourceWalletsToken"),s=jQuery("#paymentCompleted"),i=jQuery(".wc-block-components-checkout-place-order-button");let a={country:n.county};"#powerBoardWalletApplePayButton"===t&&(a.wallets=["apple"],a.amount_label="Total"),"#powerBoardWalletPayPalButton"===t&&(a.pay_later=n.pay_later,a.style={height:55}),"#powerBoardWalletAfterpayButton"===t&&jQuery("#powerBoardWalletAfterpayButton").each(((t,r)=>r.addEventListener("click",(t=>{n.payment=e.replace("-","_"),o.val(JSON.stringify(n)),i.click()}),!0))),l.current&&delete l.current,l.current=new window.cba.WalletButtons(t,n.resource.data.token,a),l.current.setEnv(r?"preproduction_cba":"production_cba"),l.current.onPaymentSuccessful((n=>{n.payment=e.replace("-","_"),o.val(JSON.stringify(n)),s.show(),jQuery(t).hide(),i.show(),i.click()})),l.current.onPaymentError((e=>{i.click()})),l.current.onPaymentInReview((n=>{n.payment=e.replace("-","_"),o.val(JSON.stringify(n)),s.show(),jQuery(t).hide(),i.show(),i.click()})),l.current.load()};function d(e,t){return function(){return e.apply(t,arguments)}}const{toString:f}=Object.prototype,{getPrototypeOf:p}=Object,h=(m=Object.create(null),e=>{const t=f.call(e);return m[t]||(m[t]=t.slice(8,-1).toLowerCase())});var m;const y=e=>(e=e.toLowerCase(),t=>h(t)===e),g=e=>t=>typeof t===e,{isArray:w}=Array,b=g("undefined"),E=y("ArrayBuffer"),S=g("string"),R=g("function"),O=g("number"),T=e=>null!==e&&"object"==typeof e,v=e=>{if("object"!==h(e))return!1;const t=p(e);return!(null!==t&&t!==Object.prototype&&null!==Object.getPrototypeOf(t)||Symbol.toStringTag in e||Symbol.iterator in e)},A=y("Date"),_=y("File"),C=y("Blob"),P=y("FileList"),j=y("URLSearchParams"),[x,N,B,D]=["ReadableStream","Request","Response","Headers"].map(y);function k(e,t,{allOwnKeys:n=!1}={}){if(null==e)return;let r,o;if("object"!=typeof e&&(e=[e]),w(e))for(r=0,o=e.length;r<o;r++)t.call(null,e[r],r,e);else{const o=n?Object.getOwnPropertyNames(e):Object.keys(e),s=o.length;let i;for(r=0;r<s;r++)i=o[r],t.call(null,e[i],i,e)}}function L(e,t){t=t.toLowerCase();const n=Object.keys(e);let r,o=n.length;for(;o-- >0;)if(r=n[o],t===r.toLowerCase())return r;return null}const U="undefined"!=typeof globalThis?globalThis:"undefined"!=typeof self?self:"undefined"!=typeof window?window:global,F=e=>!b(e)&&e!==U,q=(I="undefined"!=typeof Uint8Array&&p(Uint8Array),e=>I&&e instanceof I);var I;const M=y("HTMLFormElement"),W=(({hasOwnProperty:e})=>(t,n)=>e.call(t,n))(Object.prototype),z=y("RegExp"),H=(e,t)=>{const n=Object.getOwnPropertyDescriptors(e),r={};k(n,((n,o)=>{let s;!1!==(s=t(n,o,e))&&(r[o]=s||n)})),Object.defineProperties(e,r)},J="abcdefghijklmnopqrstuvwxyz",K="0123456789",V={DIGIT:K,ALPHA:J,ALPHA_DIGIT:J+J.toUpperCase()+K},$=y("AsyncFunction"),Q={isArray:w,isArrayBuffer:E,isBuffer:function(e){return null!==e&&!b(e)&&null!==e.constructor&&!b(e.constructor)&&R(e.constructor.isBuffer)&&e.constructor.isBuffer(e)},isFormData:e=>{let t;return e&&("function"==typeof FormData&&e instanceof FormData||R(e.append)&&("formdata"===(t=h(e))||"object"===t&&R(e.toString)&&"[object FormData]"===e.toString()))},isArrayBufferView:function(e){let t;return t="undefined"!=typeof ArrayBuffer&&ArrayBuffer.isView?ArrayBuffer.isView(e):e&&e.buffer&&E(e.buffer),t},isString:S,isNumber:O,isBoolean:e=>!0===e||!1===e,isObject:T,isPlainObject:v,isReadableStream:x,isRequest:N,isResponse:B,isHeaders:D,isUndefined:b,isDate:A,isFile:_,isBlob:C,isRegExp:z,isFunction:R,isStream:e=>T(e)&&R(e.pipe),isURLSearchParams:j,isTypedArray:q,isFileList:P,forEach:k,merge:function e(){const{caseless:t}=F(this)&&this||{},n={},r=(r,o)=>{const s=t&&L(n,o)||o;v(n[s])&&v(r)?n[s]=e(n[s],r):v(r)?n[s]=e({},r):w(r)?n[s]=r.slice():n[s]=r};for(let e=0,t=arguments.length;e<t;e++)arguments[e]&&k(arguments[e],r);return n},extend:(e,t,n,{allOwnKeys:r}={})=>(k(t,((t,r)=>{n&&R(t)?e[r]=d(t,n):e[r]=t}),{allOwnKeys:r}),e),trim:e=>e.trim?e.trim():e.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,""),stripBOM:e=>(65279===e.charCodeAt(0)&&(e=e.slice(1)),e),inherits:(e,t,n,r)=>{e.prototype=Object.create(t.prototype,r),e.prototype.constructor=e,Object.defineProperty(e,"super",{value:t.prototype}),n&&Object.assign(e.prototype,n)},toFlatObject:(e,t,n,r)=>{let o,s,i;const a={};if(t=t||{},null==e)return t;do{for(o=Object.getOwnPropertyNames(e),s=o.length;s-- >0;)i=o[s],r&&!r(i,e,t)||a[i]||(t[i]=e[i],a[i]=!0);e=!1!==n&&p(e)}while(e&&(!n||n(e,t))&&e!==Object.prototype);return t},kindOf:h,kindOfTest:y,endsWith:(e,t,n)=>{e=String(e),(void 0===n||n>e.length)&&(n=e.length),n-=t.length;const r=e.indexOf(t,n);return-1!==r&&r===n},toArray:e=>{if(!e)return null;if(w(e))return e;let t=e.length;if(!O(t))return null;const n=new Array(t);for(;t-- >0;)n[t]=e[t];return n},forEachEntry:(e,t)=>{const n=(e&&e[Symbol.iterator]).call(e);let r;for(;(r=n.next())&&!r.done;){const n=r.value;t.call(e,n[0],n[1])}},matchAll:(e,t)=>{let n;const r=[];for(;null!==(n=e.exec(t));)r.push(n);return r},isHTMLForm:M,hasOwnProperty:W,hasOwnProp:W,reduceDescriptors:H,freezeMethods:e=>{H(e,((t,n)=>{if(R(e)&&-1!==["arguments","caller","callee"].indexOf(n))return!1;const r=e[n];R(r)&&(t.enumerable=!1,"writable"in t?t.writable=!1:t.set||(t.set=()=>{throw Error("Can not rewrite read-only method '"+n+"'")}))}))},toObjectSet:(e,t)=>{const n={},r=e=>{e.forEach((e=>{n[e]=!0}))};return w(e)?r(e):r(String(e).split(t)),n},toCamelCase:e=>e.toLowerCase().replace(/[-_\s]([a-z\d])(\w*)/g,(function(e,t,n){return t.toUpperCase()+n})),noop:()=>{},toFiniteNumber:(e,t)=>null!=e&&Number.isFinite(e=+e)?e:t,findKey:L,global:U,isContextDefined:F,ALPHABET:V,generateString:(e=16,t=V.ALPHA_DIGIT)=>{let n="";const{length:r}=t;for(;e--;)n+=t[Math.random()*r|0];return n},isSpecCompliantForm:function(e){return!!(e&&R(e.append)&&"FormData"===e[Symbol.toStringTag]&&e[Symbol.iterator])},toJSONObject:e=>{const t=new Array(10),n=(e,r)=>{if(T(e)){if(t.indexOf(e)>=0)return;if(!("toJSON"in e)){t[r]=e;const o=w(e)?[]:{};return k(e,((e,t)=>{const s=n(e,r+1);!b(s)&&(o[t]=s)})),t[r]=void 0,o}}return e};return n(e,0)},isAsyncFn:$,isThenable:e=>e&&(T(e)||R(e))&&R(e.then)&&R(e.catch)};function G(e,t,n,r,o){Error.call(this),Error.captureStackTrace?Error.captureStackTrace(this,this.constructor):this.stack=(new Error).stack,this.message=e,this.name="AxiosError",t&&(this.code=t),n&&(this.config=n),r&&(this.request=r),o&&(this.response=o)}Q.inherits(G,Error,{toJSON:function(){return{message:this.message,name:this.name,description:this.description,number:this.number,fileName:this.fileName,lineNumber:this.lineNumber,columnNumber:this.columnNumber,stack:this.stack,config:Q.toJSONObject(this.config),code:this.code,status:this.response&&this.response.status?this.response.status:null}}});const X=G.prototype,Y={};["ERR_BAD_OPTION_VALUE","ERR_BAD_OPTION","ECONNABORTED","ETIMEDOUT","ERR_NETWORK","ERR_FR_TOO_MANY_REDIRECTS","ERR_DEPRECATED","ERR_BAD_RESPONSE","ERR_BAD_REQUEST","ERR_CANCELED","ERR_NOT_SUPPORT","ERR_INVALID_URL"].forEach((e=>{Y[e]={value:e}})),Object.defineProperties(G,Y),Object.defineProperty(X,"isAxiosError",{value:!0}),G.from=(e,t,n,r,o,s)=>{const i=Object.create(X);return Q.toFlatObject(e,i,(function(e){return e!==Error.prototype}),(e=>"isAxiosError"!==e)),G.call(i,e.message,t,n,r,o),i.cause=e,i.name=e.name,s&&Object.assign(i,s),i};const Z=G;function ee(e){return Q.isPlainObject(e)||Q.isArray(e)}function te(e){return Q.endsWith(e,"[]")?e.slice(0,-2):e}function ne(e,t,n){return e?e.concat(t).map((function(e,t){return e=te(e),!n&&t?"["+e+"]":e})).join(n?".":""):t}const re=Q.toFlatObject(Q,{},null,(function(e){return/^is[A-Z]/.test(e)})),oe=function(e,t,n){if(!Q.isObject(e))throw new TypeError("target must be an object");t=t||new FormData;const r=(n=Q.toFlatObject(n,{metaTokens:!0,dots:!1,indexes:!1},!1,(function(e,t){return!Q.isUndefined(t[e])}))).metaTokens,o=n.visitor||l,s=n.dots,i=n.indexes,a=(n.Blob||"undefined"!=typeof Blob&&Blob)&&Q.isSpecCompliantForm(t);if(!Q.isFunction(o))throw new TypeError("visitor must be a function");function c(e){if(null===e)return"";if(Q.isDate(e))return e.toISOString();if(!a&&Q.isBlob(e))throw new Z("Blob is not supported. Use a Buffer instead.");return Q.isArrayBuffer(e)||Q.isTypedArray(e)?a&&"function"==typeof Blob?new Blob([e]):Buffer.from(e):e}function l(e,n,o){let a=e;if(e&&!o&&"object"==typeof e)if(Q.endsWith(n,"{}"))n=r?n:n.slice(0,-2),e=JSON.stringify(e);else if(Q.isArray(e)&&function(e){return Q.isArray(e)&&!e.some(ee)}(e)||(Q.isFileList(e)||Q.endsWith(n,"[]"))&&(a=Q.toArray(e)))return n=te(n),a.forEach((function(e,r){!Q.isUndefined(e)&&null!==e&&t.append(!0===i?ne([n],r,s):null===i?n:n+"[]",c(e))})),!1;return!!ee(e)||(t.append(ne(o,n,s),c(e)),!1)}const u=[],d=Object.assign(re,{defaultVisitor:l,convertValue:c,isVisitable:ee});if(!Q.isObject(e))throw new TypeError("data must be an object");return function e(n,r){if(!Q.isUndefined(n)){if(-1!==u.indexOf(n))throw Error("Circular reference detected in "+r.join("."));u.push(n),Q.forEach(n,(function(n,s){!0===(!(Q.isUndefined(n)||null===n)&&o.call(t,n,Q.isString(s)?s.trim():s,r,d))&&e(n,r?r.concat(s):[s])})),u.pop()}}(e),t};function se(e){const t={"!":"%21","'":"%27","(":"%28",")":"%29","~":"%7E","%20":"+","%00":"\0"};return encodeURIComponent(e).replace(/[!'()~]|%20|%00/g,(function(e){return t[e]}))}function ie(e,t){this._pairs=[],e&&oe(e,this,t)}const ae=ie.prototype;ae.append=function(e,t){this._pairs.push([e,t])},ae.toString=function(e){const t=e?function(t){return e.call(this,t,se)}:se;return this._pairs.map((function(e){return t(e[0])+"="+t(e[1])}),"").join("&")};const ce=ie;function le(e){return encodeURIComponent(e).replace(/%3A/gi,":").replace(/%24/g,"$").replace(/%2C/gi,",").replace(/%20/g,"+").replace(/%5B/gi,"[").replace(/%5D/gi,"]")}function ue(e,t,n){if(!t)return e;const r=n&&n.encode||le,o=n&&n.serialize;let s;if(s=o?o(t,n):Q.isURLSearchParams(t)?t.toString():new ce(t,n).toString(r),s){const t=e.indexOf("#");-1!==t&&(e=e.slice(0,t)),e+=(-1===e.indexOf("?")?"?":"&")+s}return e}const de=class{constructor(){this.handlers=[]}use(e,t,n){return this.handlers.push({fulfilled:e,rejected:t,synchronous:!!n&&n.synchronous,runWhen:n?n.runWhen:null}),this.handlers.length-1}eject(e){this.handlers[e]&&(this.handlers[e]=null)}clear(){this.handlers&&(this.handlers=[])}forEach(e){Q.forEach(this.handlers,(function(t){null!==t&&e(t)}))}},fe={silentJSONParsing:!0,forcedJSONParsing:!0,clarifyTimeoutError:!1},pe={isBrowser:!0,classes:{URLSearchParams:"undefined"!=typeof URLSearchParams?URLSearchParams:ce,FormData:"undefined"!=typeof FormData?FormData:null,Blob:"undefined"!=typeof Blob?Blob:null},protocols:["http","https","file","blob","url","data"]},he="undefined"!=typeof window&&"undefined"!=typeof document,me=(ye="undefined"!=typeof navigator&&navigator.product,he&&["ReactNative","NativeScript","NS"].indexOf(ye)<0);var ye;const ge="undefined"!=typeof WorkerGlobalScope&&self instanceof WorkerGlobalScope&&"function"==typeof self.importScripts,we=he&&window.location.href||"http://localhost",be={...t,...pe},Ee=function(e){function t(e,n,r,o){let s=e[o++];if("__proto__"===s)return!0;const i=Number.isFinite(+s),a=o>=e.length;return s=!s&&Q.isArray(r)?r.length:s,a?(Q.hasOwnProp(r,s)?r[s]=[r[s],n]:r[s]=n,!i):(r[s]&&Q.isObject(r[s])||(r[s]=[]),t(e,n,r[s],o)&&Q.isArray(r[s])&&(r[s]=function(e){const t={},n=Object.keys(e);let r;const o=n.length;let s;for(r=0;r<o;r++)s=n[r],t[s]=e[s];return t}(r[s])),!i)}if(Q.isFormData(e)&&Q.isFunction(e.entries)){const n={};return Q.forEachEntry(e,((e,r)=>{t(function(e){return Q.matchAll(/\w+|\[(\w*)]/g,e).map((e=>"[]"===e[0]?"":e[1]||e[0]))}(e),r,n,0)})),n}return null},Se={transitional:fe,adapter:["xhr","http","fetch"],transformRequest:[function(e,t){const n=t.getContentType()||"",r=n.indexOf("application/json")>-1,o=Q.isObject(e);if(o&&Q.isHTMLForm(e)&&(e=new FormData(e)),Q.isFormData(e))return r?JSON.stringify(Ee(e)):e;if(Q.isArrayBuffer(e)||Q.isBuffer(e)||Q.isStream(e)||Q.isFile(e)||Q.isBlob(e)||Q.isReadableStream(e))return e;if(Q.isArrayBufferView(e))return e.buffer;if(Q.isURLSearchParams(e))return t.setContentType("application/x-www-form-urlencoded;charset=utf-8",!1),e.toString();let s;if(o){if(n.indexOf("application/x-www-form-urlencoded")>-1)return function(e,t){return oe(e,new be.classes.URLSearchParams,Object.assign({visitor:function(e,t,n,r){return be.isNode&&Q.isBuffer(e)?(this.append(t,e.toString("base64")),!1):r.defaultVisitor.apply(this,arguments)}},t))}(e,this.formSerializer).toString();if((s=Q.isFileList(e))||n.indexOf("multipart/form-data")>-1){const t=this.env&&this.env.FormData;return oe(s?{"files[]":e}:e,t&&new t,this.formSerializer)}}return o||r?(t.setContentType("application/json",!1),function(e){if(Q.isString(e))try{return(0,JSON.parse)(e),Q.trim(e)}catch(e){if("SyntaxError"!==e.name)throw e}return(0,JSON.stringify)(e)}(e)):e}],transformResponse:[function(e){const t=this.transitional||Se.transitional,n=t&&t.forcedJSONParsing,r="json"===this.responseType;if(Q.isResponse(e)||Q.isReadableStream(e))return e;if(e&&Q.isString(e)&&(n&&!this.responseType||r)){const n=!(t&&t.silentJSONParsing)&&r;try{return JSON.parse(e)}catch(e){if(n){if("SyntaxError"===e.name)throw Z.from(e,Z.ERR_BAD_RESPONSE,this,null,this.response);throw e}}}return e}],timeout:0,xsrfCookieName:"XSRF-TOKEN",xsrfHeaderName:"X-XSRF-TOKEN",maxContentLength:-1,maxBodyLength:-1,env:{FormData:be.classes.FormData,Blob:be.classes.Blob},validateStatus:function(e){return e>=200&&e<300},headers:{common:{Accept:"application/json, text/plain, */*","Content-Type":void 0}}};Q.forEach(["delete","get","head","post","put","patch"],(e=>{Se.headers[e]={}}));const Re=Se,Oe=Q.toObjectSet(["age","authorization","content-length","content-type","etag","expires","from","host","if-modified-since","if-unmodified-since","last-modified","location","max-forwards","proxy-authorization","referer","retry-after","user-agent"]),Te=Symbol("internals");function ve(e){return e&&String(e).trim().toLowerCase()}function Ae(e){return!1===e||null==e?e:Q.isArray(e)?e.map(Ae):String(e)}function _e(e,t,n,r,o){return Q.isFunction(r)?r.call(this,t,n):(o&&(t=n),Q.isString(t)?Q.isString(r)?-1!==t.indexOf(r):Q.isRegExp(r)?r.test(t):void 0:void 0)}class Ce{constructor(e){e&&this.set(e)}set(e,t,n){const r=this;function o(e,t,n){const o=ve(t);if(!o)throw new Error("header name must be a non-empty string");const s=Q.findKey(r,o);(!s||void 0===r[s]||!0===n||void 0===n&&!1!==r[s])&&(r[s||t]=Ae(e))}const s=(e,t)=>Q.forEach(e,((e,n)=>o(e,n,t)));if(Q.isPlainObject(e)||e instanceof this.constructor)s(e,t);else if(Q.isString(e)&&(e=e.trim())&&!/^[-_a-zA-Z0-9^`|~,!#$%&'*+.]+$/.test(e.trim()))s((e=>{const t={};let n,r,o;return e&&e.split("\n").forEach((function(e){o=e.indexOf(":"),n=e.substring(0,o).trim().toLowerCase(),r=e.substring(o+1).trim(),!n||t[n]&&Oe[n]||("set-cookie"===n?t[n]?t[n].push(r):t[n]=[r]:t[n]=t[n]?t[n]+", "+r:r)})),t})(e),t);else if(Q.isHeaders(e))for(const[t,r]of e.entries())o(r,t,n);else null!=e&&o(t,e,n);return this}get(e,t){if(e=ve(e)){const n=Q.findKey(this,e);if(n){const e=this[n];if(!t)return e;if(!0===t)return function(e){const t=Object.create(null),n=/([^\s,;=]+)\s*(?:=\s*([^,;]+))?/g;let r;for(;r=n.exec(e);)t[r[1]]=r[2];return t}(e);if(Q.isFunction(t))return t.call(this,e,n);if(Q.isRegExp(t))return t.exec(e);throw new TypeError("parser must be boolean|regexp|function")}}}has(e,t){if(e=ve(e)){const n=Q.findKey(this,e);return!(!n||void 0===this[n]||t&&!_e(0,this[n],n,t))}return!1}delete(e,t){const n=this;let r=!1;function o(e){if(e=ve(e)){const o=Q.findKey(n,e);!o||t&&!_e(0,n[o],o,t)||(delete n[o],r=!0)}}return Q.isArray(e)?e.forEach(o):o(e),r}clear(e){const t=Object.keys(this);let n=t.length,r=!1;for(;n--;){const o=t[n];e&&!_e(0,this[o],o,e,!0)||(delete this[o],r=!0)}return r}normalize(e){const t=this,n={};return Q.forEach(this,((r,o)=>{const s=Q.findKey(n,o);if(s)return t[s]=Ae(r),void delete t[o];const i=e?function(e){return e.trim().toLowerCase().replace(/([a-z\d])(\w*)/g,((e,t,n)=>t.toUpperCase()+n))}(o):String(o).trim();i!==o&&delete t[o],t[i]=Ae(r),n[i]=!0})),this}concat(...e){return this.constructor.concat(this,...e)}toJSON(e){const t=Object.create(null);return Q.forEach(this,((n,r)=>{null!=n&&!1!==n&&(t[r]=e&&Q.isArray(n)?n.join(", "):n)})),t}[Symbol.iterator](){return Object.entries(this.toJSON())[Symbol.iterator]()}toString(){return Object.entries(this.toJSON()).map((([e,t])=>e+": "+t)).join("\n")}get[Symbol.toStringTag](){return"AxiosHeaders"}static from(e){return e instanceof this?e:new this(e)}static concat(e,...t){const n=new this(e);return t.forEach((e=>n.set(e))),n}static accessor(e){const t=(this[Te]=this[Te]={accessors:{}}).accessors,n=this.prototype;function r(e){const r=ve(e);t[r]||(function(e,t){const n=Q.toCamelCase(" "+t);["get","set","has"].forEach((r=>{Object.defineProperty(e,r+n,{value:function(e,n,o){return this[r].call(this,t,e,n,o)},configurable:!0})}))}(n,e),t[r]=!0)}return Q.isArray(e)?e.forEach(r):r(e),this}}Ce.accessor(["Content-Type","Content-Length","Accept","Accept-Encoding","User-Agent","Authorization"]),Q.reduceDescriptors(Ce.prototype,(({value:e},t)=>{let n=t[0].toUpperCase()+t.slice(1);return{get:()=>e,set(e){this[n]=e}}})),Q.freezeMethods(Ce);const Pe=Ce;function je(e,t){const n=this||Re,r=t||n,o=Pe.from(r.headers);let s=r.data;return Q.forEach(e,(function(e){s=e.call(n,s,o.normalize(),t?t.status:void 0)})),o.normalize(),s}function xe(e){return!(!e||!e.__CANCEL__)}function Ne(e,t,n){Z.call(this,null==e?"canceled":e,Z.ERR_CANCELED,t,n),this.name="CanceledError"}Q.inherits(Ne,Z,{__CANCEL__:!0});const Be=Ne;function De(e,t,n){const r=n.config.validateStatus;n.status&&r&&!r(n.status)?t(new Z("Request failed with status code "+n.status,[Z.ERR_BAD_REQUEST,Z.ERR_BAD_RESPONSE][Math.floor(n.status/100)-4],n.config,n.request,n)):e(n)}const ke=(e,t,n=3)=>{let r=0;const o=function(e,t){e=e||10;const n=new Array(e),r=new Array(e);let o,s=0,i=0;return t=void 0!==t?t:1e3,function(a){const c=Date.now(),l=r[i];o||(o=c),n[s]=a,r[s]=c;let u=i,d=0;for(;u!==s;)d+=n[u++],u%=e;if(s=(s+1)%e,s===i&&(i=(i+1)%e),c-o<t)return;const f=l&&c-l;return f?Math.round(1e3*d/f):void 0}}(50,250);return function(e,t){let n=0;const r=1e3/t;let o=null;return function(){const t=!0===this,s=Date.now();if(t||s-n>r)return o&&(clearTimeout(o),o=null),n=s,e.apply(null,arguments);o||(o=setTimeout((()=>(o=null,n=Date.now(),e.apply(null,arguments))),r-(s-n)))}}((n=>{const s=n.loaded,i=n.lengthComputable?n.total:void 0,a=s-r,c=o(a);r=s;const l={loaded:s,total:i,progress:i?s/i:void 0,bytes:a,rate:c||void 0,estimated:c&&i&&s<=i?(i-s)/c:void 0,event:n,lengthComputable:null!=i};l[t?"download":"upload"]=!0,e(l)}),n)},Le=be.hasStandardBrowserEnv?function(){const e=/(msie|trident)/i.test(navigator.userAgent),t=document.createElement("a");let n;function r(n){let r=n;return e&&(t.setAttribute("href",r),r=t.href),t.setAttribute("href",r),{href:t.href,protocol:t.protocol?t.protocol.replace(/:$/,""):"",host:t.host,search:t.search?t.search.replace(/^\?/,""):"",hash:t.hash?t.hash.replace(/^#/,""):"",hostname:t.hostname,port:t.port,pathname:"/"===t.pathname.charAt(0)?t.pathname:"/"+t.pathname}}return n=r(window.location.href),function(e){const t=Q.isString(e)?r(e):e;return t.protocol===n.protocol&&t.host===n.host}}():function(){return!0},Ue=be.hasStandardBrowserEnv?{write(e,t,n,r,o,s){const i=[e+"="+encodeURIComponent(t)];Q.isNumber(n)&&i.push("expires="+new Date(n).toGMTString()),Q.isString(r)&&i.push("path="+r),Q.isString(o)&&i.push("domain="+o),!0===s&&i.push("secure"),document.cookie=i.join("; ")},read(e){const t=document.cookie.match(new RegExp("(^|;\\s*)("+e+")=([^;]*)"));return t?decodeURIComponent(t[3]):null},remove(e){this.write(e,"",Date.now()-864e5)}}:{write(){},read:()=>null,remove(){}};function Fe(e,t){return e&&!/^([a-z][a-z\d+\-.]*:)?\/\//i.test(t)?function(e,t){return t?e.replace(/\/?\/$/,"")+"/"+t.replace(/^\/+/,""):e}(e,t):t}const qe=e=>e instanceof Pe?{...e}:e;function Ie(e,t){t=t||{};const n={};function r(e,t,n){return Q.isPlainObject(e)&&Q.isPlainObject(t)?Q.merge.call({caseless:n},e,t):Q.isPlainObject(t)?Q.merge({},t):Q.isArray(t)?t.slice():t}function o(e,t,n){return Q.isUndefined(t)?Q.isUndefined(e)?void 0:r(void 0,e,n):r(e,t,n)}function s(e,t){if(!Q.isUndefined(t))return r(void 0,t)}function i(e,t){return Q.isUndefined(t)?Q.isUndefined(e)?void 0:r(void 0,e):r(void 0,t)}function a(n,o,s){return s in t?r(n,o):s in e?r(void 0,n):void 0}const c={url:s,method:s,data:s,baseURL:i,transformRequest:i,transformResponse:i,paramsSerializer:i,timeout:i,timeoutMessage:i,withCredentials:i,withXSRFToken:i,adapter:i,responseType:i,xsrfCookieName:i,xsrfHeaderName:i,onUploadProgress:i,onDownloadProgress:i,decompress:i,maxContentLength:i,maxBodyLength:i,beforeRedirect:i,transport:i,httpAgent:i,httpsAgent:i,cancelToken:i,socketPath:i,responseEncoding:i,validateStatus:a,headers:(e,t)=>o(qe(e),qe(t),!0)};return Q.forEach(Object.keys(Object.assign({},e,t)),(function(r){const s=c[r]||o,i=s(e[r],t[r],r);Q.isUndefined(i)&&s!==a||(n[r]=i)})),n}const Me=e=>{const t=Ie({},e);let n,{data:r,withXSRFToken:o,xsrfHeaderName:s,xsrfCookieName:i,headers:a,auth:c}=t;if(t.headers=a=Pe.from(a),t.url=ue(Fe(t.baseURL,t.url),e.params,e.paramsSerializer),c&&a.set("Authorization","Basic "+btoa((c.username||"")+":"+(c.password?unescape(encodeURIComponent(c.password)):""))),Q.isFormData(r))if(be.hasStandardBrowserEnv||be.hasStandardBrowserWebWorkerEnv)a.setContentType(void 0);else if(!1!==(n=a.getContentType())){const[e,...t]=n?n.split(";").map((e=>e.trim())).filter(Boolean):[];a.setContentType([e||"multipart/form-data",...t].join("; "))}if(be.hasStandardBrowserEnv&&(o&&Q.isFunction(o)&&(o=o(t)),o||!1!==o&&Le(t.url))){const e=s&&i&&Ue.read(i);e&&a.set(s,e)}return t},We="undefined"!=typeof XMLHttpRequest&&function(e){return new Promise((function(t,n){const r=Me(e);let o=r.data;const s=Pe.from(r.headers).normalize();let i,{responseType:a}=r;function c(){r.cancelToken&&r.cancelToken.unsubscribe(i),r.signal&&r.signal.removeEventListener("abort",i)}let l=new XMLHttpRequest;function u(){if(!l)return;const r=Pe.from("getAllResponseHeaders"in l&&l.getAllResponseHeaders());De((function(e){t(e),c()}),(function(e){n(e),c()}),{data:a&&"text"!==a&&"json"!==a?l.response:l.responseText,status:l.status,statusText:l.statusText,headers:r,config:e,request:l}),l=null}l.open(r.method.toUpperCase(),r.url,!0),l.timeout=r.timeout,"onloadend"in l?l.onloadend=u:l.onreadystatechange=function(){l&&4===l.readyState&&(0!==l.status||l.responseURL&&0===l.responseURL.indexOf("file:"))&&setTimeout(u)},l.onabort=function(){l&&(n(new Z("Request aborted",Z.ECONNABORTED,r,l)),l=null)},l.onerror=function(){n(new Z("Network Error",Z.ERR_NETWORK,r,l)),l=null},l.ontimeout=function(){let e=r.timeout?"timeout of "+r.timeout+"ms exceeded":"timeout exceeded";const t=r.transitional||fe;r.timeoutErrorMessage&&(e=r.timeoutErrorMessage),n(new Z(e,t.clarifyTimeoutError?Z.ETIMEDOUT:Z.ECONNABORTED,r,l)),l=null},void 0===o&&s.setContentType(null),"setRequestHeader"in l&&Q.forEach(s.toJSON(),(function(e,t){l.setRequestHeader(t,e)})),Q.isUndefined(r.withCredentials)||(l.withCredentials=!!r.withCredentials),a&&"json"!==a&&(l.responseType=r.responseType),"function"==typeof r.onDownloadProgress&&l.addEventListener("progress",ke(r.onDownloadProgress,!0)),"function"==typeof r.onUploadProgress&&l.upload&&l.upload.addEventListener("progress",ke(r.onUploadProgress)),(r.cancelToken||r.signal)&&(i=t=>{l&&(n(!t||t.type?new Be(null,e,l):t),l.abort(),l=null)},r.cancelToken&&r.cancelToken.subscribe(i),r.signal&&(r.signal.aborted?i():r.signal.addEventListener("abort",i)));const d=function(e){const t=/^([-+\w]{1,25})(:?\/\/|:)/.exec(e);return t&&t[1]||""}(r.url);d&&-1===be.protocols.indexOf(d)?n(new Z("Unsupported protocol "+d+":",Z.ERR_BAD_REQUEST,e)):l.send(o||null)}))},ze=(e,t)=>{let n,r=new AbortController;const o=function(e){if(!n){n=!0,i();const t=e instanceof Error?e:this.reason;r.abort(t instanceof Z?t:new Be(t instanceof Error?t.message:t))}};let s=t&&setTimeout((()=>{o(new Z(`timeout ${t} of ms exceeded`,Z.ETIMEDOUT))}),t);const i=()=>{e&&(s&&clearTimeout(s),s=null,e.forEach((e=>{e&&(e.removeEventListener?e.removeEventListener("abort",o):e.unsubscribe(o))})),e=null)};e.forEach((e=>e&&e.addEventListener&&e.addEventListener("abort",o)));const{signal:a}=r;return a.unsubscribe=i,[a,()=>{s&&clearTimeout(s),s=null}]},He=function*(e,t){let n=e.byteLength;if(!t||n<t)return void(yield e);let r,o=0;for(;o<n;)r=o+t,yield e.slice(o,r),o=r},Je=(e,t,n,r,o)=>{const s=async function*(e,t,n){for await(const r of e)yield*He(ArrayBuffer.isView(r)?r:await n(String(r)),t)}(e,t,o);let i=0;return new ReadableStream({type:"bytes",async pull(e){const{done:t,value:o}=await s.next();if(t)return e.close(),void r();let a=o.byteLength;n&&n(i+=a),e.enqueue(new Uint8Array(o))},cancel:e=>(r(e),s.return())},{highWaterMark:2})},Ke=(e,t)=>{const n=null!=e;return r=>setTimeout((()=>t({lengthComputable:n,total:e,loaded:r})))},Ve="function"==typeof fetch&&"function"==typeof Request&&"function"==typeof Response,$e=Ve&&"function"==typeof ReadableStream,Qe=Ve&&("function"==typeof TextEncoder?(Ge=new TextEncoder,e=>Ge.encode(e)):async e=>new Uint8Array(await new Response(e).arrayBuffer()));var Ge;const Xe=$e&&(()=>{let e=!1;const t=new Request(be.origin,{body:new ReadableStream,method:"POST",get duplex(){return e=!0,"half"}}).headers.has("Content-Type");return e&&!t})(),Ye=$e&&!!(()=>{try{return Q.isReadableStream(new Response("").body)}catch(e){}})(),Ze={stream:Ye&&(e=>e.body)};var et;Ve&&(et=new Response,["text","arrayBuffer","blob","formData","stream"].forEach((e=>{!Ze[e]&&(Ze[e]=Q.isFunction(et[e])?t=>t[e]():(t,n)=>{throw new Z(`Response type '${e}' is not supported`,Z.ERR_NOT_SUPPORT,n)})})));const tt={http:null,xhr:We,fetch:Ve&&(async e=>{let{url:t,method:n,data:r,signal:o,cancelToken:s,timeout:i,onDownloadProgress:a,onUploadProgress:c,responseType:l,headers:u,withCredentials:d="same-origin",fetchOptions:f}=Me(e);l=l?(l+"").toLowerCase():"text";let p,h,[m,y]=o||s||i?ze([o,s],i):[];const g=()=>{!p&&setTimeout((()=>{m&&m.unsubscribe()})),p=!0};let w;try{if(c&&Xe&&"get"!==n&&"head"!==n&&0!==(w=await(async(e,t)=>{const n=Q.toFiniteNumber(e.getContentLength());return null==n?(async e=>null==e?0:Q.isBlob(e)?e.size:Q.isSpecCompliantForm(e)?(await new Request(e).arrayBuffer()).byteLength:Q.isArrayBufferView(e)?e.byteLength:(Q.isURLSearchParams(e)&&(e+=""),Q.isString(e)?(await Qe(e)).byteLength:void 0))(t):n})(u,r))){let e,n=new Request(t,{method:"POST",body:r,duplex:"half"});Q.isFormData(r)&&(e=n.headers.get("content-type"))&&u.setContentType(e),n.body&&(r=Je(n.body,65536,Ke(w,ke(c)),null,Qe))}Q.isString(d)||(d=d?"cors":"omit"),h=new Request(t,{...f,signal:m,method:n.toUpperCase(),headers:u.normalize().toJSON(),body:r,duplex:"half",withCredentials:d});let o=await fetch(h);const s=Ye&&("stream"===l||"response"===l);if(Ye&&(a||s)){const e={};["status","statusText","headers"].forEach((t=>{e[t]=o[t]}));const t=Q.toFiniteNumber(o.headers.get("content-length"));o=new Response(Je(o.body,65536,a&&Ke(t,ke(a,!0)),s&&g,Qe),e)}l=l||"text";let i=await Ze[Q.findKey(Ze,l)||"text"](o,e);return!s&&g(),y&&y(),await new Promise(((t,n)=>{De(t,n,{data:i,headers:Pe.from(o.headers),status:o.status,statusText:o.statusText,config:e,request:h})}))}catch(t){if(g(),t&&"TypeError"===t.name&&/fetch/i.test(t.message))throw Object.assign(new Z("Network Error",Z.ERR_NETWORK,e,h),{cause:t.cause||t});throw Z.from(t,t&&t.code,e,h)}})};Q.forEach(tt,((e,t)=>{if(e){try{Object.defineProperty(e,"name",{value:t})}catch(e){}Object.defineProperty(e,"adapterName",{value:t})}}));const nt=e=>`- ${e}`,rt=e=>Q.isFunction(e)||null===e||!1===e,ot=e=>{e=Q.isArray(e)?e:[e];const{length:t}=e;let n,r;const o={};for(let s=0;s<t;s++){let t;if(n=e[s],r=n,!rt(n)&&(r=tt[(t=String(n)).toLowerCase()],void 0===r))throw new Z(`Unknown adapter '${t}'`);if(r)break;o[t||"#"+s]=r}if(!r){const e=Object.entries(o).map((([e,t])=>`adapter ${e} `+(!1===t?"is not supported by the environment":"is not available in the build")));let n=t?e.length>1?"since :\n"+e.map(nt).join("\n"):" "+nt(e[0]):"as no adapter specified";throw new Z("There is no suitable adapter to dispatch the request "+n,"ERR_NOT_SUPPORT")}return r};function st(e){if(e.cancelToken&&e.cancelToken.throwIfRequested(),e.signal&&e.signal.aborted)throw new Be(null,e)}function it(e){return st(e),e.headers=Pe.from(e.headers),e.data=je.call(e,e.transformRequest),-1!==["post","put","patch"].indexOf(e.method)&&e.headers.setContentType("application/x-www-form-urlencoded",!1),ot(e.adapter||Re.adapter)(e).then((function(t){return st(e),t.data=je.call(e,e.transformResponse,t),t.headers=Pe.from(t.headers),t}),(function(t){return xe(t)||(st(e),t&&t.response&&(t.response.data=je.call(e,e.transformResponse,t.response),t.response.headers=Pe.from(t.response.headers))),Promise.reject(t)}))}const at={};["object","boolean","number","function","string","symbol"].forEach(((e,t)=>{at[e]=function(n){return typeof n===e||"a"+(t<1?"n ":" ")+e}}));const ct={};at.transitional=function(e,t,n){function r(e,t){return"[Axios v1.7.2] Transitional option '"+e+"'"+t+(n?". "+n:"")}return(n,o,s)=>{if(!1===e)throw new Z(r(o," has been removed"+(t?" in "+t:"")),Z.ERR_DEPRECATED);return t&&!ct[o]&&(ct[o]=!0,console.warn(r(o," has been deprecated since v"+t+" and will be removed in the near future"))),!e||e(n,o,s)}};const lt={assertOptions:function(e,t,n){if("object"!=typeof e)throw new Z("options must be an object",Z.ERR_BAD_OPTION_VALUE);const r=Object.keys(e);let o=r.length;for(;o-- >0;){const s=r[o],i=t[s];if(i){const t=e[s],n=void 0===t||i(t,s,e);if(!0!==n)throw new Z("option "+s+" must be "+n,Z.ERR_BAD_OPTION_VALUE)}else if(!0!==n)throw new Z("Unknown option "+s,Z.ERR_BAD_OPTION)}},validators:at},ut=lt.validators;class dt{constructor(e){this.defaults=e,this.interceptors={request:new de,response:new de}}async request(e,t){try{return await this._request(e,t)}catch(e){if(e instanceof Error){let t;Error.captureStackTrace?Error.captureStackTrace(t={}):t=new Error;const n=t.stack?t.stack.replace(/^.+\n/,""):"";try{e.stack?n&&!String(e.stack).endsWith(n.replace(/^.+\n.+\n/,""))&&(e.stack+="\n"+n):e.stack=n}catch(e){}}throw e}}_request(e,t){"string"==typeof e?(t=t||{}).url=e:t=e||{},t=Ie(this.defaults,t);const{transitional:n,paramsSerializer:r,headers:o}=t;void 0!==n&&lt.assertOptions(n,{silentJSONParsing:ut.transitional(ut.boolean),forcedJSONParsing:ut.transitional(ut.boolean),clarifyTimeoutError:ut.transitional(ut.boolean)},!1),null!=r&&(Q.isFunction(r)?t.paramsSerializer={serialize:r}:lt.assertOptions(r,{encode:ut.function,serialize:ut.function},!0)),t.method=(t.method||this.defaults.method||"get").toLowerCase();let s=o&&Q.merge(o.common,o[t.method]);o&&Q.forEach(["delete","get","head","post","put","patch","common"],(e=>{delete o[e]})),t.headers=Pe.concat(s,o);const i=[];let a=!0;this.interceptors.request.forEach((function(e){"function"==typeof e.runWhen&&!1===e.runWhen(t)||(a=a&&e.synchronous,i.unshift(e.fulfilled,e.rejected))}));const c=[];let l;this.interceptors.response.forEach((function(e){c.push(e.fulfilled,e.rejected)}));let u,d=0;if(!a){const e=[it.bind(this),void 0];for(e.unshift.apply(e,i),e.push.apply(e,c),u=e.length,l=Promise.resolve(t);d<u;)l=l.then(e[d++],e[d++]);return l}u=i.length;let f=t;for(d=0;d<u;){const e=i[d++],t=i[d++];try{f=e(f)}catch(e){t.call(this,e);break}}try{l=it.call(this,f)}catch(e){return Promise.reject(e)}for(d=0,u=c.length;d<u;)l=l.then(c[d++],c[d++]);return l}getUri(e){return ue(Fe((e=Ie(this.defaults,e)).baseURL,e.url),e.params,e.paramsSerializer)}}Q.forEach(["delete","get","head","options"],(function(e){dt.prototype[e]=function(t,n){return this.request(Ie(n||{},{method:e,url:t,data:(n||{}).data}))}})),Q.forEach(["post","put","patch"],(function(e){function t(t){return function(n,r,o){return this.request(Ie(o||{},{method:e,headers:t?{"Content-Type":"multipart/form-data"}:{},url:n,data:r}))}}dt.prototype[e]=t(),dt.prototype[e+"Form"]=t(!0)}));const ft=dt;class pt{constructor(e){if("function"!=typeof e)throw new TypeError("executor must be a function.");let t;this.promise=new Promise((function(e){t=e}));const n=this;this.promise.then((e=>{if(!n._listeners)return;let t=n._listeners.length;for(;t-- >0;)n._listeners[t](e);n._listeners=null})),this.promise.then=e=>{let t;const r=new Promise((e=>{n.subscribe(e),t=e})).then(e);return r.cancel=function(){n.unsubscribe(t)},r},e((function(e,r,o){n.reason||(n.reason=new Be(e,r,o),t(n.reason))}))}throwIfRequested(){if(this.reason)throw this.reason}subscribe(e){this.reason?e(this.reason):this._listeners?this._listeners.push(e):this._listeners=[e]}unsubscribe(e){if(!this._listeners)return;const t=this._listeners.indexOf(e);-1!==t&&this._listeners.splice(t,1)}static source(){let e;return{token:new pt((function(t){e=t})),cancel:e}}}const ht=pt,mt={Continue:100,SwitchingProtocols:101,Processing:102,EarlyHints:103,Ok:200,Created:201,Accepted:202,NonAuthoritativeInformation:203,NoContent:204,ResetContent:205,PartialContent:206,MultiStatus:207,AlreadyReported:208,ImUsed:226,MultipleChoices:300,MovedPermanently:301,Found:302,SeeOther:303,NotModified:304,UseProxy:305,Unused:306,TemporaryRedirect:307,PermanentRedirect:308,BadRequest:400,Unauthorized:401,PaymentRequired:402,Forbidden:403,NotFound:404,MethodNotAllowed:405,NotAcceptable:406,ProxyAuthenticationRequired:407,RequestTimeout:408,Conflict:409,Gone:410,LengthRequired:411,PreconditionFailed:412,PayloadTooLarge:413,UriTooLong:414,UnsupportedMediaType:415,RangeNotSatisfiable:416,ExpectationFailed:417,ImATeapot:418,MisdirectedRequest:421,UnprocessableEntity:422,Locked:423,FailedDependency:424,TooEarly:425,UpgradeRequired:426,PreconditionRequired:428,TooManyRequests:429,RequestHeaderFieldsTooLarge:431,UnavailableForLegalReasons:451,InternalServerError:500,NotImplemented:501,BadGateway:502,ServiceUnavailable:503,GatewayTimeout:504,HttpVersionNotSupported:505,VariantAlsoNegotiates:506,InsufficientStorage:507,LoopDetected:508,NotExtended:510,NetworkAuthenticationRequired:511};Object.entries(mt).forEach((([e,t])=>{mt[t]=e}));const yt=mt,gt=function e(t){const n=new ft(t),r=d(ft.prototype.request,n);return Q.extend(r,ft.prototype,n,{allOwnKeys:!0}),Q.extend(r,n,null,{allOwnKeys:!0}),r.create=function(n){return e(Ie(t,n))},r}(Re);gt.Axios=ft,gt.CanceledError=Be,gt.CancelToken=ht,gt.isCancel=xe,gt.VERSION="1.7.2",gt.toFormData=oe,gt.AxiosError=Z,gt.Cancel=gt.CanceledError,gt.all=function(e){return Promise.all(e)},gt.spread=function(e){return function(t){return e.apply(null,t)}},gt.isAxiosError=function(e){return Q.isObject(e)&&!0===e.isAxiosError},gt.mergeConfig=Ie,gt.AxiosHeaders=Pe,gt.formToJSON=e=>Ee(Q.isHTMLForm(e)?new FormData(e):e),gt.getAdapter=ot,gt.HttpStatusCode=yt,gt.default=gt;const wt=gt,bt="power_board",Et={validationError:(0,n.__)("Please fill in the required fields of the form to display payment methods",bt),fillDataError:(0,n.__)("The payment service does not accept payment. Please try again later or choose another payment method.",bt),notAvailable:(0,n.__)("Payment method is not avalaible for your country!!!",bt)};let St={initData:null,total:0};((e,t,l,d)=>{const f=`power_board_${e}_wallet_block_data`,p=`power_board_${e}_wallets_gateway`,h=(0,s.getSetting)(f,{}),m=(0,o.decodeEntities)(h.title)||(0,n.__)("PowerBoard Apple Pay",bt),y=(0,i.select)(a.CHECKOUT_STORE_KEY),g=(0,i.select)(a.CART_STORE_KEY);let w;const b=()=>{if(!w.length||St.total===g.getCartTotals()?.total_price)return;jQuery("#"+l).each(((e,t)=>{t.innerHTML=""})),St.total=g.getCartTotals()?.total_price,w.each(((e,t)=>t.innerHTML=""));let t={type:e,order_id:y.getOrderId(),total:g.getCartTotals(),address:g.getCustomerData().billingAddress,shipping_address:g.getCustomerData().shippingAddress,shipping_rates:g.getShippingRates(),items:g.getCartData().items};wt.post("/wp-json/power-board/v1/wallets/charge",t).then((t=>{St.initData=t.data,setTimeout((()=>{u(e,"#"+l,St.initData,h.isSandbox,St.reload)}),100)})).catch((e=>{St.wasInit=!1}))},E=t=>{w=jQuery("#"+l);const{eventRegistration:n,emitResponse:r}=t,{onPaymentSetup:s,onCheckoutValidation:i,onShippingRateSelectSuccess:a}=n,f=g.getCustomerData().billingAddress,p=jQuery(".power-board-country-available");let m=((e,t)=>{for(let n=0;n<t.length;n++)if(!e.hasOwnProperty(t[n])||!e[t[n]])return!1;return!0})(f,d);jQuery(".wc-block-components-checkout-place-order-button").hide(),!m||St.initData||St.wasInit?m&&St.initData&&!w&&(p.hide(),setTimeout((()=>{u(e,"#"+l,St.initData,h.isSandbox)}),100)):(p.hide(),b()),(0,c.useEffect)((()=>{const e=a((async()=>{St.total!==g.getCartTotals()?.total_price&&function(e,t){let n=0,r=0;return e.max&&(r=100*e.max),e.min&&(n=100*e.min),n=t>=n,r=0===r||t<=r,n&&r}(h.total_limitation,g.getCartTotals()?.total_price)&&b()})),t=i((async e=>m&&!!document.getElementById("paymentSourceWalletsToken").value||{type:r.responseTypes.ERROR,errorMessage:Et.fillDataError})),n=s((async e=>document.getElementById("paymentSourceWalletsToken").value?{type:r.responseTypes.SUCCESS,meta:{paymentMethodData:{payment_response:document.getElementById("paymentSourceWalletsToken").value,wallets:JSON.stringify(h.wallets),_wpnonce:h._wpnonce}}}:{type:r.responseTypes.ERROR,errorMessage:Et.fillDataError}));return()=>{n()&&t()&&e()}}),[r.responseTypes.ERROR,r.responseTypes.SUCCESS,s]);const y=(0,c.createElement)("div",null,(0,o.decodeEntities)(h.description||"")),E=(0,c.createElement)("input",{type:"hidden",id:"paymentSourceWalletsToken"}),S=(0,c.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),R=[(0,c.createElement)("div",{id:l,class:"paudock-wallets-buttons"})];return(0,c.createElement)("div",null,y,S,(0,c.createElement)("div",{id:"powerBoardWidgetWallets",class:"power-board-widget-content"},...R),(0,c.createElement)("div",{class:"power-board-validation-error",style:{display:m?"none":""}},Et.validationError),(0,c.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},Et.notAvailable),E)},S={name:p,label:(0,c.createElement)((()=>(0,c.createElement)("div",{className:"power-board-payment-method-label"},(0,c.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/${e}.png`,alt:m,className:`power-board-payment-method-label-icon ${e}`}),"  "+m))),content:(0,c.createElement)(E,null),edit:(0,c.createElement)(E,null),canMakePayment:()=>!0,ariaLabel:m,supports:{features:h.supports}};(0,r.registerPaymentMethod)(S)})("apple-pay",0,"powerBoardWalletApplePayButton",["first_name","last_name","email","address_1","city","state","country","postcode"])})();