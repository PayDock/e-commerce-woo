(()=>{"use strict";var e={20:(e,t,a)=>{var r=a(609),o=Symbol.for("react.element"),n=(Symbol.for("react.fragment"),Object.prototype.hasOwnProperty),s=r.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,i={key:!0,ref:!0,__self:!0,__source:!0};t.jsx=function(e,t,a){var r,c={},l=null,d=null;for(r in void 0!==a&&(l=""+a),void 0!==t.key&&(l=""+t.key),void 0!==t.ref&&(d=t.ref),t)n.call(t,r)&&!i.hasOwnProperty(r)&&(c[r]=t[r]);if(e&&e.defaultProps)for(r in t=e.defaultProps)void 0===c[r]&&(c[r]=t[r]);return{$$typeof:o,type:e,key:l,ref:d,props:c,_owner:s.current}}},848:(e,t,a)=>{e.exports=a(20)},609:e=>{e.exports=window.React}},t={};function a(r){var o=t[r];if(void 0!==o)return o.exports;var n=t[r]={exports:{}};return e[r](n,n.exports,a),n.exports}const r=window.wp.i18n;var o=a(609);const n=window.wp.htmlEntities,s=window.wc.wcSettings,i=window.wc.wcBlocksRegistry,c=window.wp.data,l=window.wc.wcBlocksData;var d=a(848);const p="power_board",u={defaultLabel:(0,r.__)("PowerBoard Payments",p),placeOrderButtonLabel:(0,r.__)("Place Order by PowerBoard",p),validationError:(0,r.__)("Please fill in the required fields of the form to display payment methods",p),notAvailable:(0,r.__)("The payment method is not available in your country.",p)};let m=!1,_=null;((e,t,a,y,w)=>{const f=`power_board_${e}_a_p_m_s_block_data`,g=`power_board_${e}_a_p_m_s_gateway`,b=(0,s.getSetting)(f,{}),h=(0,n.decodeEntities)(b.title)||(0,r.__)("PowerBoard Zip",p),v=(0,c.select)(l.CART_STORE_KEY),E=t=>{const{eventRegistration:r,emitResponse:s}=t,{onPaymentSetup:i,onCheckoutValidation:c,onShippingRateSelectSuccess:l}=r,d=v.getCustomerData().billingAddress,p=v.getCustomerData().shippingAddress,f=v.getShippingRates(),g=jQuery(".power-board-country-available"),h=jQuery(".power-board-validation-error"),E=jQuery("#"+a),S=jQuery(".wc-block-components-checkout-place-order-button"),k=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(d,y),x=!!w.find((e=>e===d.country.toLowerCase())),R={},T={...b};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",T.total_limitation&&delete T.total_limitation,h.hide(),g.hide(),E.hide(),C?C&&!x?(m=!1,g.show()):C&&x&&E.show():(m=!1,h.show()),setTimeout((()=>{if(C&&!m&&(m=!0,_=new window.cba.ZipmoneyCheckoutButton("#"+a,b.publicKey,b.gatewayId),T.gatewayType="zippay"),_){_.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state};f.length&&f[0].shipping_rates.length&&f[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const r=t.rate_id.split(":"),o=b.pickupLocations[r[1]];e.line1=o.address.address_1,e.line2="",e.country=o.address.country,e.postcode=o.address.postcode,e.city=o.address.city,e.state=o.address.state})),R.charge={amount:Number((v.getCartTotals().total_price/100).toFixed(3)).toFixed(2),currency:b.currency,email:d.email,first_name:d.first_name,last_name:d.last_name,shipping_address:e,billing_address:{first_name:d.first_name,last_name:d.last_name,line1:d.address_1,line2:d.address_2,country:d.country,postcode:d.postcode,city:d.city,state:d.state},items:v.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},_.setEnv(b.isSandbox?"preproduction_cba":"production_cba"),_.setMeta(R),_.on("finish",(()=>{b.directCharge&&(T.directCharge=!0),b.fraud&&(T.fraud=!0,T.fraudServiceId=b.fraudServiceId),null!==S&&S.click(),k.show()}))}}),100),(0,o.useEffect)((()=>{const e=l((async()=>{const{total_price:e}=v.getCartTotals(),t=Number(e/100).toFixed(2),a=(e,t)=>void 0!==e?{amount:t}:{};_.setMeta({...R,...a(R.amount,t),charge:{...R.charge,...a(R.charge.amount,t)}})})),t=i((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.amount=Number((v.getCartTotals().total_price/100).toFixed(3)).toFixed(2),T.paymentSourceToken.length>0||b.selectedToken.length>0?{type:s.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:s.responseTypes.ERROR,message:u.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),m=!1}}),[s.responseTypes.ERROR,s.responseTypes.SUCCESS,i,c]),(0,o.createElement)("div",{id:"powerBoardWidgetApm"},(0,o.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":b.styles.background_color,color:b.styles.success_color,"font-size":b.styles.font_size,"font-family":b.styles.font_family}},"Payment Details Collected"),(0,o.createElement)("div",null,(0,n.decodeEntities)(b.description||"")),(0,o.createElement)("div",{class:"apms-button-wrapper"},(0,o.createElement)("button",{type:"button",id:a,class:`btn-apm btn-apm-${e}`,style:{display:"none"}},(0,o.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/${e}.png`}))),(0,o.createElement)("div",{class:"power-board-validation-error"},u.validationError),(0,o.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,o.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},u.notAvailable))},S={name:g,label:(0,o.createElement)((()=>(0,o.createElement)("div",{className:"power-board-payment-method-label"},(0,o.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/${e}.png`,alt:h,className:`power-board-payment-method-label-icon ${e}`}),"  "+h))),content:(0,d.jsx)(E,{}),edit:(0,d.jsx)(E,{}),placeOrderButtonLabel:u.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let r=0,o=0;return e.max&&(o=100*e.max),e.min&&(r=100*e.min),r=t>=r,o=0===o||t<=o,r&&o}(b.total_limitation,v.getCartTotals()?.total_price),ariaLabel:h,supports:{features:b.supports}};(0,i.registerPaymentMethod)(S)})("zip",0,"powerBoardAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();