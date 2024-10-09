(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,r=window.wc.wcBlocksRegistry,s=window.wp.data,o=window.wc.wcBlocksData,i="power_board",c={defaultLabel:(0,e.__)("PowerBoard Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by PowerBoard",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let l=!1;((d,p,m,u,y)=>{const _=`power_board_${d}_a_p_m_s_block_data`,g=`power_board_${d}_a_p_m_s_gateway`,w=(0,n.getSetting)(_,{}),f=(0,a.decodeEntities)(w.title)||(0,e.__)("PowerBoard Afterpay",i),h=(0,s.select)(o.CART_STORE_KEY),b=()=>{const{total_price:e}=h.getCartTotals();return Number(e/100).toFixed(2)},E=e=>{const{eventRegistration:n,emitResponse:r}=e,{onPaymentSetup:s,onCheckoutValidation:o,onShippingRateSelectSuccess:i}=n,p=h.getCustomerData().billingAddress,_=h.getCustomerData().shippingAddress,g=h.getShippingRates(),f=jQuery(".power-board-country-available"),E=jQuery(".power-board-validation-error"),v=jQuery("#"+m),k=jQuery(".wc-block-components-checkout-place-order-button"),S=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(p,u),B=!!y.find((e=>e===p.country.toLowerCase())),P=null,R={},T={...w};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",T.total_limitation&&delete T.total_limitation,E.hide(),f.hide(),v.hide(),C?C&&!B?(l=!1,f.show()):C&&B&&v.show():(l=!1,E.show()),setTimeout((()=>{if(C&&!l&&(l=!0,P=new window.cba.AfterpayCheckoutButton("#"+m,w.publicKey,w.gatewayId),T.gatewayType="afterpay"),C&&(R={amount:b(),currency:w.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,address_line:p.address_1,address_line2:p.address_2,address_city:p.city,address_state:p.state,address_postcode:p.postcode,address_country:p.country,phone:p.phone}),P){P.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:_.first_name,last_name:_.last_name,line1:_.address_1,line2:_.address_2,country:_.country,postcode:_.postcode,city:_.city,state:_.state};g.length&&g[0].shipping_rates.length&&g[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),r=w.pickupLocations[n[1]];e.line1=r.address.address_1,e.line2="",e.country=r.address.country,e.postcode=r.address.postcode,e.city=r.address.city,e.state=r.address.state})),R.charge={amount:b(),currency:w.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,shipping_address:e,billing_address:{first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state},items:h.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},P.setEnv(w.isSandbox?"staging_cba":"production_cba"),P.setMeta(R),P.on("finish",(()=>{w.directCharge&&(T.directCharge=!0),w.fraud&&(T.fraud=!0,T.fraudServiceId=w.fraudServiceId),null!==k&&k.click(),S.show()}))}}),100),(0,t.useEffect)((()=>{const e=i((async()=>{const e=b(),t=(e,t)=>void 0!==e?{amount:t}:{};P.setMeta({...R,...t(R.amount,e),charge:{...R.charge,...t(R.charge.amount,e)}})})),t=s((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.amount=b(),T.paymentSourceToken.length>0||w.selectedToken.length>0?{type:r.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:r.responseTypes.ERROR,message:c.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),l=!1}}),[r.responseTypes.ERROR,r.responseTypes.SUCCESS,s,o]),(0,t.createElement)("div",{id:"powerBoardWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":w.styles.background_color,color:w.styles.success_color,"font-size":w.styles.font_size,"font-family":w.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(w.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"power-board-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},c.notAvailable))},v={name:g,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"power-board-payment-method-label"},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/${d}.png`,alt:f,className:`power-board-payment-method-label-icon ${d}`}),"  "+f))),content:(0,t.createElement)(E,null),edit:(0,t.createElement)(E,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let n=0,r=0;return e.max&&(r=100*e.max),e.min&&(n=100*e.min),n=t>=n,r=0===r||t<=r,n&&r}(w.total_limitation,h.getCartTotals()?.total_price),ariaLabel:f,supports:{features:w.supports}};(0,r.registerPaymentMethod)(v)})("afterpay",0,"powerBoardAPMsAfterpayButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca","uk","gb","fr","it","es","de"])})();