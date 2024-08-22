(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,r=window.wc.wcSettings,n=window.wc.wcBlocksRegistry,o=window.wp.data,s=window.wc.wcBlocksData,i="power_board",c={defaultLabel:(0,e.__)("PowerBoard Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by PowerBoard",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let l=!1,d=null;((p,m,u,_,y)=>{const g=`power_board_${p}_a_p_m_s_block_data`,w=`power_board_${p}_a_p_m_s_gateway`,f=(0,r.getSetting)(g,{}),b=(0,a.decodeEntities)(f.title)||(0,e.__)("PowerBoard Afterpay",i),h=(0,o.select)(s.CART_STORE_KEY),E=e=>{const{eventRegistration:r,emitResponse:n}=e,{onPaymentSetup:o,onCheckoutValidation:s,onShippingRateSelectSuccess:i}=r,m=h.getCustomerData().billingAddress,g=h.getCustomerData().shippingAddress,w=h.getShippingRates(),b=jQuery(".power-board-country-available"),E=jQuery(".power-board-validation-error"),v=jQuery("#"+u),k=jQuery(".wc-block-components-checkout-place-order-button"),S=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(m,_),T=!!y.find((e=>e===m.country.toLowerCase())),x={},B={...f};return B.customers="",B.styles="",B.supports="",B.pickupLocations="",B.total_limitation&&delete B.total_limitation,E.hide(),b.hide(),v.hide(),C?C&&!T?(l=!1,b.show()):C&&T&&v.show():(l=!1,E.show()),setTimeout((()=>{if(C&&!l&&(l=!0,d=new window.cba.AfterpayCheckoutButton("#"+u,f.publicKey,f.gatewayId),B.gatewayType="afterpay"),C&&(x={amount:Number((h.getCartTotals().total_price/100).toFixed(3)).toFixed(2),currency:f.currency,email:m.email,first_name:m.first_name,last_name:m.last_name,address_line:m.address_1,address_line2:m.address_2,address_city:m.city,address_state:m.state,address_postcode:m.postcode,address_country:m.country,phone:m.phone}),d){d.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:g.first_name,last_name:g.last_name,line1:g.address_1,line2:g.address_2,country:g.country,postcode:g.postcode,city:g.city,state:g.state};w.length&&w[0].shipping_rates.length&&w[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const r=t.rate_id.split(":"),n=f.pickupLocations[r[1]];e.line1=n.address.address_1,e.line2="",e.country=n.address.country,e.postcode=n.address.postcode,e.city=n.address.city,e.state=n.address.state})),x.charge={amount:Number((h.getCartTotals().total_price/100).toFixed(3)).toFixed(2),currency:f.currency,email:m.email,first_name:m.first_name,last_name:m.last_name,shipping_address:e,billing_address:{first_name:m.first_name,last_name:m.last_name,line1:m.address_1,line2:m.address_2,country:m.country,postcode:m.postcode,city:m.city,state:m.state},items:h.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},d.setEnv(f.isSandbox?"preproduction_cba":"production_cba"),d.setMeta(x),d.on("finish",(()=>{f.directCharge&&(B.directCharge=!0),f.fraud&&(B.fraud=!0,B.fraudServiceId=f.fraudServiceId),null!==k&&k.click(),S.show()}))}}),100),(0,t.useEffect)((()=>{const e=i((async()=>{const{total_price:e}=h.getCartTotals(),t=Number(e/100).toFixed(2),a=(e,t)=>void 0!==e?{amount:t}:{};d.setMeta({...x,...a(x.amount,t),charge:{...x.charge,...a(x.charge.amount,t)}})})),t=o((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return B.paymentSourceToken=e.value,B.amount=Number((h.getCartTotals().total_price/100).toFixed(3)).toFixed(2),B.paymentSourceToken.length>0||f.selectedToken.length>0?{type:n.responseTypes.SUCCESS,meta:{paymentMethodData:B}}:{type:n.responseTypes.ERROR,message:c.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e)}}),[n.responseTypes.ERROR,n.responseTypes.SUCCESS,o,s]),(0,t.createElement)("div",{id:"powerBoardWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":f.styles.background_color,color:f.styles.success_color,"font-size":f.styles.font_size,"font-family":f.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(f.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:u,class:`btn-apm btn-apm-${p}`,style:{display:"none"}},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/${p}.png`}))),(0,t.createElement)("div",{class:"power-board-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},c.notAvailable))},v={name:w,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"power-board-payment-method-label"},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/${p}.png`,alt:b,className:`power-board-payment-method-label-icon ${p}`}),"  "+b))),content:(0,t.createElement)(E,null),edit:(0,t.createElement)(E,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let r=0,n=0;return e.max&&(n=100*e.max),e.min&&(r=100*e.min),r=t>=r,n=0===n||t<=n,r&&n}(f.total_limitation,h.getCartTotals()?.total_price),ariaLabel:b,supports:{features:f.supports}};(0,n.registerPaymentMethod)(v)})("afterpay",0,"powerBoardAPMsAfterpayButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca","uk","gb","fr","it","es","de"])})();