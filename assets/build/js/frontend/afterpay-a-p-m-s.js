(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,s=window.wc.wcBlocksRegistry,o=window.wp.data,r=window.wc.wcBlocksData,i="paydock",c={defaultLabel:(0,e.__)("Paydock Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by Paydock",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let l=!1;((d,p,m,u,y)=>{const _=`paydock_${d}_a_p_m_s_block_data`,g=`paydock_${d}_a_p_m_s_gateway`,f=(0,n.getSetting)(_,{}),h=(0,a.decodeEntities)(f.title)||(0,e.__)("Paydock Afterpay",i),k=(0,o.select)(r.CART_STORE_KEY),w=()=>{const{total_price:e}=k.getCartTotals();return Number(e/100).toFixed(2)},b=e=>{const{eventRegistration:n,emitResponse:s}=e,{onPaymentSetup:o,onCheckoutValidation:r,onShippingRateSelectSuccess:i}=n,p=k.getCustomerData().billingAddress,_=k.getCustomerData().shippingAddress,g=k.getShippingRates(),h=jQuery(".paydock-country-available"),b=jQuery(".paydock-validation-error"),E=jQuery("#"+m),v=jQuery(".wc-block-components-checkout-place-order-button"),S=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(p,u),R=!!y.find((e=>e===p.country.toLowerCase())),T=null,P={},A={...f};return A.customers="",A.styles="",A.supports="",A.pickupLocations="",A.total_limitation&&delete A.total_limitation,b.hide(),h.hide(),E.hide(),C?C&&!R?(l=!1,h.show()):C&&R&&E.show():(l=!1,b.show()),setTimeout((()=>{if(C&&!l&&(l=!0,T=new window.paydock.AfterpayCheckoutButton("#"+m,f.publicKey,f.gatewayId),P={amount:w(),currency:f.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,address_line:p.address_1,address_line2:p.address_2,address_city:p.city,address_state:p.state,address_postcode:p.postcode,address_country:p.country,phone:p.phone},A.gatewayType="afterpay"),T){T.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:_.first_name,last_name:_.last_name,line1:_.address_1,line2:_.address_2,country:_.country,postcode:_.postcode,city:_.city,state:_.state};g.length&&g[0].shipping_rates.length&&g[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),s=f.pickupLocations[n[1]];e.line1=s.address.address_1,e.line2="",e.country=s.address.country,e.postcode=s.address.postcode,e.city=s.address.city,e.state=s.address.state})),P.charge={amount:w(),currency:f.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,shipping_address:e,billing_address:{first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state},items:k.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},T.setEnv(f.isSandbox?"sandbox":"production"),T.setMeta(P),T.on("finish",(()=>{f.directCharge&&(A.directCharge=!0),f.fraud&&(A.fraud=!0,A.fraudServiceId=f.fraudServiceId),null!==v&&v.click(),S.show()}))}}),100),(0,t.useEffect)((()=>{const e=i((async()=>{const e=w(),t=(e,t)=>void 0!==e?{amount:t}:{};T.setMeta({...P,...t(P.amount,e),charge:{...P.charge,...t(P.charge.amount,e)}})})),t=o((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return A.paymentSourceToken=e.value,A.paymentSourceToken.length>0||f.selectedToken.length>0?{type:s.responseTypes.SUCCESS,meta:{paymentMethodData:A}}:{type:s.responseTypes.ERROR,message:c.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),l=!1}}),[s.responseTypes.ERROR,s.responseTypes.SUCCESS,o,r]),(0,t.createElement)("div",{id:"paydockWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":f.styles.background_color,color:f.styles.success_color,"font-size":f.styles.font_size,"font-family":f.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(f.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"paydock-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"paydock-country-available",style:{display:"none"}},c.notAvailable))},E={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"paydock-payment-method-label"},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/icons/${d}.png`,alt:h,className:`paydock-payment-method-label-icon ${d}`}),"  "+h))),content:(0,t.createElement)(b,null),edit:(0,t.createElement)(b,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let n=0,s=0;return e.max&&(s=100*e.max),e.min&&(n=100*e.min),n=t>=n,s=0===s||t<=s,n&&s}(f.total_limitation,k.getCartTotals()?.total_price),ariaLabel:h,supports:{features:f.supports}};(0,s.registerPaymentMethod)(E)})("afterpay",0,"paydockAPMsAfterpayButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca","uk","gb","fr","it","es","de"])})();