(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,s=window.wc.wcBlocksRegistry,o=window.wp.data,i=window.wc.wcBlocksData,r="paydock",c={defaultLabel:(0,e.__)("Paydock Payments",r),placeOrderButtonLabel:(0,e.__)("Place Order by Paydock",r),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",r),notAvailable:(0,e.__)("The payment method is not available in your country.",r)};let l=!1;((d,p,m,u,y)=>{const _=`paydock_${d}_a_p_m_s_block_data`,g=`paydock_${d}_a_p_m_s_gateway`,h=(0,n.getSetting)(_,{}),f=(0,a.decodeEntities)(h.title)||(0,e.__)("Paydock Zip",r),w=(0,o.select)(i.CART_STORE_KEY),k=()=>{const{total_price:e}=w.getCartTotals();return Number(e/100).toFixed(2)},b=e=>{const{eventRegistration:n,emitResponse:s}=e,{onPaymentSetup:o,onCheckoutValidation:i,onShippingRateSelectSuccess:r}=n,p=w.getCustomerData().billingAddress,_=w.getCustomerData().shippingAddress,g=w.getShippingRates(),f=jQuery(".paydock-country-available"),b=jQuery(".paydock-validation-error"),E=jQuery("#"+m),v=jQuery(".wc-block-components-checkout-place-order-button"),S=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(p,u),R=!!y.find((e=>e===p.country.toLowerCase())),T=null,P={},L={...h};return L.customers="",L.styles="",L.supports="",L.pickupLocations="",L.total_limitation&&delete L.total_limitation,b.hide(),f.hide(),E.hide(),C?C&&!R?(l=!1,f.show()):C&&R&&E.show():(l=!1,b.show()),setTimeout((()=>{if(C&&!l&&(l=!0,T=new window.paydock.ZipmoneyCheckoutButton("#"+m,h.publicKey,h.gatewayId),L.gatewayType="zippay"),T){T.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:_.first_name,last_name:_.last_name,line1:_.address_1,line2:_.address_2,country:_.country,postcode:_.postcode,city:_.city,state:_.state};g.length&&g[0].shipping_rates.length&&g[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),s=h.pickupLocations[n[1]];e.line1=s.address.address_1,e.line2="",e.country=s.address.country,e.postcode=s.address.postcode,e.city=s.address.city,e.state=s.address.state})),P.charge={amount:k(),currency:h.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,shipping_address:e,billing_address:{first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state},items:w.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},T.setEnv(h.isSandbox?"sandbox":"production"),T.setMeta(P),T.on("finish",(()=>{h.directCharge&&(L.directCharge=!0),h.fraud&&(L.fraud=!0,L.fraudServiceId=h.fraudServiceId),null!==v&&v.click(),S.show()}))}}),100),(0,t.useEffect)((()=>{const e=r((async()=>{const e=k(),t=(e,t)=>void 0!==e?{amount:t}:{};T.setMeta({...P,...t(P.amount,e),charge:{...P.charge,...t(P.charge.amount,e)}})})),t=o((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return L.paymentSourceToken=e.value,L.paymentSourceToken.length>0||h.selectedToken.length>0?{type:s.responseTypes.SUCCESS,meta:{paymentMethodData:L}}:{type:s.responseTypes.ERROR,message:c.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),l=!1}}),[s.responseTypes.ERROR,s.responseTypes.SUCCESS,o,i]),(0,t.createElement)("div",{id:"paydockWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(h.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"paydock-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"paydock-country-available",style:{display:"none"}},c.notAvailable))},E={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"paydock-payment-method-label"},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/icons/${d}.png`,alt:f,className:`paydock-payment-method-label-icon ${d}`}),"  "+f))),content:(0,t.createElement)(b,null),edit:(0,t.createElement)(b,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let n=0,s=0;return e.max&&(s=100*e.max),e.min&&(n=100*e.min),n=t>=n,s=0===s||t<=s,n&&s}(h.total_limitation,w.getCartTotals()?.total_price),ariaLabel:f,supports:{features:h.supports}};(0,s.registerPaymentMethod)(E)})("zip",0,"paydockAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();