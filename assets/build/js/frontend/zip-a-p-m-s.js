(()=>{"use strict";const e=window.wp.i18n,t=window.React,n=window.wp.htmlEntities,a=window.wc.wcSettings,i=window.wc.wcBlocksRegistry,s=window.wp.data,o=window.wc.wcBlocksData,r=window.widgetSettings.pluginPrefix,l=window.widgetSettings.pluginTextDomain,c=window.widgetSettings.pluginTextName,d={defaultLabel:(0,e.__)(c+" Payments",l),placeOrderButtonLabel:(0,e.__)("Place Order by "+c,l),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",l),notAvailable:(0,e.__)("The payment method is not available in your country.",l)};let p=!1;((c,u,m,y,_)=>{const g=`${r}_${c}_a_p_m_s_block_data`,w=`${r}_${c}_a_p_m_s_gateway`,h=(0,a.getSetting)(g,{}),f=(0,n.decodeEntities)(h.title)||(0,e.__)(u,l),b=(0,s.select)(o.CART_STORE_KEY),E=()=>{const{total_price:e}=b.getCartTotals();return Number(e/100).toFixed(2)},S=e=>{const{eventRegistration:a,emitResponse:i}=e,{onPaymentSetup:s,onCheckoutValidation:o,onShippingRateSelectSuccess:r}=a,l=b.getCustomerData().billingAddress,u=b.getCustomerData().shippingAddress,g=b.getShippingRates(),w=jQuery(".plugin-country-available"),f=jQuery(".plugin-validation-error"),S=jQuery("#"+m),v=jQuery(".wc-block-components-checkout-place-order-button"),k=jQuery("#paymentCompleted");let C=((e,t)=>{for(let n=0;n<t.length;n++)if(!e.hasOwnProperty(t[n])||!e[t[n]])return!1;return!0})(l,y),T=!!_.find((e=>e===l.country.toLowerCase())),R=null,x={},P={...h};return P.customers="",P.styles="",P.supports="",P.pickupLocations="",P.total_limitation&&delete P.total_limitation,f.hide(),w.hide(),S.hide(),C?C&&!T?(p=!1,w.show()):C&&T&&S.show():(p=!1,f.show()),setTimeout((()=>{if(C&&!p&&(p=!0,R=new window.cba.ZipmoneyCheckoutButton("#"+m,h.publicKey,h.gatewayId),P.gatewayType="zippay"),R){R.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:u.first_name,last_name:u.last_name,line1:u.address_1,line2:u.address_2,country:u.country,postcode:u.postcode,city:u.city,state:u.state};g.length&&g[0].shipping_rates.length&&g[0].shipping_rates.forEach(((t,n)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const a=t.rate_id.split(":"),i=h.pickupLocations[a[1]];e.line1=i.address.address_1,e.line2="",e.country=i.address.country,e.postcode=i.address.postcode,e.city=i.address.city,e.state=i.address.state})),x.charge={amount:E(),currency:h.currency,email:l.email,first_name:l.first_name,last_name:l.last_name,shipping_address:e,billing_address:{first_name:l.first_name,last_name:l.last_name,line1:l.address_1,line2:l.address_2,country:l.country,postcode:l.postcode,city:l.city,state:l.state},items:b.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},R.setEnv(h.isSandbox?"preproduction_cba":"production_cba"),R.setMeta(x),R.on("finish",(()=>{h.directCharge&&(P.directCharge=!0),h.fraud&&(P.fraud=!0,P.fraudServiceId=h.fraudServiceId),null!==v&&v.click(),k.show()}))}}),100),(0,t.useEffect)((()=>{const e=r((async()=>{const e=E(),t=(e,t)=>void 0!==e?{amount:t}:{};R.setMeta({...x,...t(x.amount,e),charge:{...x.charge,...t(x.charge.amount,e)}})})),t=s((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return P.paymentSourceToken=e.value,P.paymentSourceToken.length>0||h.selectedToken.length>0?{type:i.responseTypes.SUCCESS,meta:{paymentMethodData:P}}:{type:i.responseTypes.ERROR,message:d.fillDataError}}));return()=>{const n=e=>"function"==typeof e?e():null;n(t),n(e),p=!1}}),[i.responseTypes.ERROR,i.responseTypes.SUCCESS,s,o]),(0,t.createElement)("div",{id:"pluginWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":h.styles.background_color,color:h.styles.success_color,"font-size":h.styles.font_size,"font-family":h.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,n.decodeEntities)(h.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${c}`,style:{display:"none"}},(0,t.createElement)("img",{src:`${window.widgetSettings.pluginUrlPrefix}assets/images/${c}.png`}))),(0,t.createElement)("div",{class:"plugin-validation-error"},d.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"plugin-country-available",style:{display:"none"}},d.notAvailable))},v={name:w,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"plugin-payment-method-label"},(0,t.createElement)("img",{src:`${window.widgetSettings.pluginUrlPrefix}assets/images/icons/${c}.png`,alt:f,className:`plugin-payment-method-label-icon ${c}`}),"  "+f))),content:(0,t.createElement)(S,null),edit:(0,t.createElement)(S,null),placeOrderButtonLabel:d.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(n=e)||"object"!=typeof n)return!0;var n;let a=0,i=0;return e.max&&(i=100*e.max),e.min&&(a=100*e.min),a=t>=a,i=0===i||t<=i,a&&i}(h.total_limitation,b.getCartTotals()?.total_price),ariaLabel:f,supports:{features:h.supports}};(0,i.registerPaymentMethod)(v)})("zip",window.widgetSettings.pluginTextName+" Zip","pluginAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();