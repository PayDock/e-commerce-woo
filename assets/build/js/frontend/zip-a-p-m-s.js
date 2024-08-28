(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,o=window.wc.wcBlocksRegistry,s=window.wp.data,i=window.wc.wcBlocksData,r="paydock",c={defaultLabel:(0,e.__)("Paydock Payments",r),placeOrderButtonLabel:(0,e.__)("Place Order by Paydock",r),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",r),notAvailable:(0,e.__)("The payment method is not available in your country.",r)};let l=!1;((d,p,m,u,y)=>{const _=`paydock_${d}_a_p_m_s_block_data`,g=`paydock_${d}_a_p_m_s_gateway`,h=(0,n.getSetting)(_,{}),f=(0,a.decodeEntities)(h.title)||(0,e.__)("Paydock Zip",r),w=(0,s.select)(i.CART_STORE_KEY),k=e=>{const{eventRegistration:n,emitResponse:o}=e,{onPaymentSetup:s,onCheckoutValidation:i,onShippingRateSelectSuccess:r}=n,p=w.getCustomerData().billingAddress,_=w.getCustomerData().shippingAddress,g=w.getShippingRates(),f=jQuery(".paydock-country-available"),k=jQuery(".paydock-validation-error"),b=jQuery("#"+m),E=jQuery(".wc-block-components-checkout-place-order-button"),v=jQuery("#paymentCompleted");let S=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(p,u),C=!!y.find((e=>e===p.country.toLowerCase())),P=null,R={},T={...h};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",T.total_limitation&&delete T.total_limitation,k.hide(),f.hide(),b.hide(),S?S&&!C?(l=!1,f.show()):S&&C&&b.show():(l=!1,k.show()),setTimeout((()=>{if(S&&!l&&(l=!0,P=new window.paydock.ZipmoneyCheckoutButton("#"+m,h.publicKey,h.gatewayId),T.gatewayType="zippay"),P){P.onFinishInsert('input[name="payment_source_apm_token"]',"paydock_payment_source_token");const e={first_name:_.first_name,last_name:_.last_name,line1:_.address_1,line2:_.address_2,country:_.country,postcode:_.postcode,city:_.city,state:_.state};g.length&&g[0].shipping_rates.length&&g[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),o=h.pickupLocations[n[1]];e.line1=o.address.address_1,e.line2="",e.country=o.address.country,e.postcode=o.address.postcode,e.city=o.address.city,e.state=o.address.state})),R.charge={amount:h.amount,currency:h.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,shipping_address:e,billing_address:{first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state},items:w.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},P.setEnv(h.isSandbox?"preproduction_cba":"production_cba"),P.setMeta(R),P.on("finish",(()=>{h.directCharge&&(T.directCharge=!0),h.fraud&&(T.fraud=!0,T.fraudServiceId=h.fraudServiceId),null!==E&&E.click(),v.show()}))}}),100),(0,t.useEffect)((()=>{const e=r((async()=>{const{total_price:e}=w.getCartTotals(),t=Number(e/100).toFixed(2),a=(e,t)=>void 0!==e?{amount:t}:{};P.setMeta({...R,...a(R.amount,t),charge:{...R.charge,...a(R.charge.amount,t)}})})),t=s((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.paymentSourceToken.length>0||h.selectedToken.length>0?{type:o.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:o.responseTypes.ERROR,message:c.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),l=!1}}),[o.responseTypes.ERROR,o.responseTypes.SUCCESS,s,i]),(0,t.createElement)("div",{id:"paydockWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(h.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`${window.paydockWidgetSettings.pluginUrlPrefix}assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"paydock-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"paydock-country-available",style:{display:"none"}},c.notAvailable))},b={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"paydock-payment-method-label"},(0,t.createElement)("img",{src:`${window.paydockWidgetSettings.pluginUrlPrefix}assets/images/icons/${d}.png`,alt:f,className:`paydock-payment-method-label-icon ${d}`}),"  "+f))),content:(0,t.createElement)(k,null),edit:(0,t.createElement)(k,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let n=0,o=0;return e.max&&(o=100*e.max),e.min&&(n=100*e.min),n=t>=n,o=0===o||t<=o,n&&o}(h.total_limitation,w.getCartTotals()?.total_price),ariaLabel:f,supports:{features:h.supports}};(0,o.registerPaymentMethod)(b)})("zip",0,"paydockAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();