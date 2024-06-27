(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,s=window.wc.wcBlocksRegistry,o=window.wp.data,i=window.wc.wcBlocksData,r="pay_dock",c={defaultLabel:(0,e.__)("Paydock Payments",r),placeOrderButtonLabel:(0,e.__)("Place Order by Paydock",r),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",r),notAvailable:(0,e.__)("The payment method is not available in your country.",r)};let l=!1;((d,p,m,y,u)=>{const _=`paydock_${d}_a_p_m_s_block_data`,g=`paydock_${d}_a_p_m_s_gateway`,h=(0,n.getSetting)(_,{}),w=(0,a.decodeEntities)(h.title)||(0,e.__)("Paydock Zip",r),k=(0,o.select)(i.CART_STORE_KEY),f=e=>{const{eventRegistration:n,emitResponse:s}=e,{onPaymentSetup:o,onCheckoutValidation:i}=n,r=k.getCustomerData().billingAddress,p=k.getCustomerData().shippingAddress,_=k.getShippingRates(),g=jQuery(".paydock-country-available"),w=jQuery(".paydock-validation-error"),f=jQuery("#"+m),b=jQuery(".wc-block-components-checkout-place-order-button"),E=jQuery("#paymentCompleted");let v=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(r,y),C=!!u.find((e=>e===r.country.toLowerCase())),S=null,R={},T={...h};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",T.total_limitation&&delete T.total_limitation,w.hide(),g.hide(),f.hide(),v?v&&!C?(l=!1,g.show()):v&&C&&f.show():(l=!1,w.show()),setTimeout((()=>{if(v&&!l&&(l=!0,S=new window.paydock.ZipmoneyCheckoutButton("#"+m,h.publicKey,h.gatewayId),T.gatewayType="zippay"),S){S.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state};_.length&&_[0].shipping_rates.length&&_[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),s=h.pickupLocations[n[1]];e.line1=s.address.address_1,e.line2="",e.country=s.address.country,e.postcode=s.address.postcode,e.city=s.address.city,e.state=s.address.state})),R.charge={amount:h.amount,currency:h.currency,email:r.email,first_name:r.first_name,last_name:r.last_name,shipping_address:e,billing_address:{first_name:r.first_name,last_name:r.last_name,line1:r.address_1,line2:r.address_2,country:r.country,postcode:r.postcode,city:r.city,state:r.state},items:k.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},S.setEnv(h.isSandbox?"sandbox":"production"),S.setMeta(R),S.on("finish",(()=>{h.directCharge&&(T.directCharge=!0),h.fraud&&(T.fraud=!0,T.fraudServiceId=h.fraudServiceId),null!==b&&b.click(),E.show()}))}}),100),(0,t.useEffect)((()=>{const e=o((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.paymentSourceToken.length>0||h.selectedToken.length>0?{type:s.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:s.responseTypes.ERROR,message:c.fillDataError}}));return()=>{e()}}),[s.responseTypes.ERROR,s.responseTypes.SUCCESS,o,i]),(0,t.createElement)("div",{id:"paydockWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(h.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"paydock-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"paydock-country-available",style:{display:"none"}},c.notAvailable))},b={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"paydock-payment-method-label"},(0,t.createElement)("img",{src:`/wp-content/plugins/paydock/assets/images/icons/${d}.png`,alt:w,className:`paydock-payment-method-label-icon ${d}`}),"  "+w))),content:(0,t.createElement)(f,null),edit:(0,t.createElement)(f,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){let a=0,n=0;return e.max&&(n=100*e.max),e.min&&(a=100*e.min),a=t>=a,n=0===n||t<=n,a&&n}(h.total_limitation,k.getCartTotals()?.total_price),ariaLabel:w,supports:{features:h.supports}};(0,s.registerPaymentMethod)(b)})("zip",0,"paydockAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();