(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,s=window.wc.wcBlocksRegistry,o=window.wp.data,r=window.wc.wcBlocksData,i="pay_dock",c={defaultLabel:(0,e.__)("Paydock Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by Paydock",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let d=!1;((l,p,m,y,u)=>{const _=`paydock_${l}_a_p_m_s_block_data`,g=`paydock_${l}_a_p_m_s_gateway`,f=(0,n.getSetting)(_,{}),h=(0,a.decodeEntities)(f.title)||(0,e.__)("Paydock Afterpay",i),k=e=>{const n=(0,o.select)(r.CART_STORE_KEY),{eventRegistration:s,emitResponse:i}=e,{onPaymentSetup:p,onCheckoutValidation:_}=s,g=n.getCustomerData().billingAddress,h=n.getCustomerData().shippingAddress,k=n.getShippingRates(),w=jQuery(".paydock-country-available"),b=jQuery(".paydock-validation-error"),E=jQuery("#"+m),v=jQuery(".wc-block-components-checkout-place-order-button"),C=jQuery("#paymentCompleted");let S=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(g,y),R=!!u.find((e=>e===g.country.toLowerCase())),P=null,T={},A={...f};return A.customers="",A.styles="",A.supports="",A.pickupLocations="",b.hide(),w.hide(),E.hide(),S?S&&!R?(d=!1,w.show()):S&&R&&E.show():(d=!1,b.show()),setTimeout((()=>{if(S&&!d&&(d=!0,P=new window.paydock.AfterpayCheckoutButton("#"+m,f.publicKey,f.gatewayId),T={amount:f.amount,currency:f.currency,email:g.email,first_name:g.first_name,last_name:g.last_name,address_line:g.address_1,address_line2:g.address_2,address_city:g.city,address_state:g.state,address_postcode:g.postcode,address_country:g.country,phone:g.phone},A.gatewayType="afterpay"),P){P.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:h.first_name,last_name:h.last_name,line1:h.address_1,line2:h.address_2,country:h.country,postcode:h.postcode,city:h.city,state:h.state};k.length&&k[0].shipping_rates.length&&k[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),s=f.pickupLocations[n[1]];e.line1=s.address.address_1,e.line2="",e.country=s.address.country,e.postcode=s.address.postcode,e.city=s.address.city,e.state=s.address.state})),T.charge={amount:f.amount,currency:f.currency,email:g.email,first_name:g.first_name,last_name:g.last_name,shipping_address:e,billing_address:{first_name:g.first_name,last_name:g.last_name,line1:g.address_1,line2:g.address_2,country:g.country,postcode:g.postcode,city:g.city,state:g.state},items:n.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},P.setMeta(T),P.on("finish",(()=>{f.directCharge&&(A.directCharge=!0),f.fraud&&(A.fraud=!0,A.fraudServiceId=f.fraudServiceId),null!==v&&v.click(),C.show()}))}}),100),(0,t.useEffect)((()=>{const e=p((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return A.paymentSourceToken=e.value,A.paymentSourceToken.length>0||f.selectedToken.length>0?{type:i.responseTypes.SUCCESS,meta:{paymentMethodData:A}}:{type:i.responseTypes.ERROR,message:c.fillDataError}}));return()=>{e()}}),[i.responseTypes.ERROR,i.responseTypes.SUCCESS,p,_]),(0,t.createElement)("div",{id:"paydockWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":f.styles.background_color,color:f.styles.success_color,"font-size":f.styles.font_size,"font-family":f.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(f.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${l}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/${l}.png`}))),(0,t.createElement)("div",{class:"paydock-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"paydock-country-available",style:{display:"none"}},c.notAvailable))},w={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"paydock-payment-method-label"},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/icons/${l}.png`,alt:h,className:`paydock-payment-method-label-icon ${l}`}),"  "+h))),content:(0,t.createElement)(k,null),edit:(0,t.createElement)(k,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>!0,ariaLabel:h,supports:{features:f.supports}};(0,s.registerPaymentMethod)(w)})("afterpay",0,"paydockAPMsAfterpayButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca","uk","gb","fr","it","es","de"])})();