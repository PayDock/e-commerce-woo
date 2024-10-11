(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,o=window.wc.wcBlocksRegistry,r=window.wp.data,s=window.wc.wcBlocksData,i="power_board",l={defaultLabel:(0,e.__)("PowerBoard Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by PowerBoard",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let c=!1;((d,p,m,u,y)=>{const _=`power_board_${d}_a_p_m_s_block_data`,w=`power_board_${d}_a_p_m_s_gateway`,g=(0,n.getSetting)(_,{}),h=(0,a.decodeEntities)(g.title)||(0,e.__)("PowerBoard Zip",i),f=(0,r.select)(s.CART_STORE_KEY),b=()=>{const{total_price:e}=f.getCartTotals();return Number(e/100).toFixed(2)},E=e=>{const{eventRegistration:n,emitResponse:o}=e,{onPaymentSetup:r,onCheckoutValidation:s,onShippingRateSelectSuccess:i}=n,p=f.getCustomerData().billingAddress,_=f.getCustomerData().shippingAddress,w=f.getShippingRates(),h=jQuery(".power-board-country-available"),E=jQuery(".power-board-validation-error"),v=jQuery("#"+m),k=jQuery(".wc-block-components-checkout-place-order-button"),S=jQuery("#paymentCompleted");let C=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(p,u),B=!!y.find((e=>e===p.country.toLowerCase())),P=null,R={},T={...g};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",T.total_limitation&&delete T.total_limitation,E.hide(),h.hide(),v.hide(),C?C&&!B?(c=!1,h.show()):C&&B&&v.show():(c=!1,E.show()),setTimeout((()=>{if(C&&!c&&(c=!0,P=new window.cba.ZipmoneyCheckoutButton("#"+m,g.publicKey,g.gatewayId),T.gatewayType="zippay"),P){P.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:_.first_name,last_name:_.last_name,line1:_.address_1,line2:_.address_2,country:_.country,postcode:_.postcode,city:_.city,state:_.state};w.length&&w[0].shipping_rates.length&&w[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),o=g.pickupLocations[n[1]];e.line1=o.address.address_1,e.line2="",e.country=o.address.country,e.postcode=o.address.postcode,e.city=o.address.city,e.state=o.address.state})),R.charge={amount:b(),currency:g.currency,email:p.email,first_name:p.first_name,last_name:p.last_name,shipping_address:e,billing_address:{first_name:p.first_name,last_name:p.last_name,line1:p.address_1,line2:p.address_2,country:p.country,postcode:p.postcode,city:p.city,state:p.state},items:f.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},P.setEnv(g.environment),P.setMeta(R),P.on("finish",(()=>{g.directCharge&&(T.directCharge=!0),g.fraud&&(T.fraud=!0,T.fraudServiceId=g.fraudServiceId),null!==k&&k.click(),S.show()}))}}),100),(0,t.useEffect)((()=>{const e=i((async()=>{const e=b(),t=(e,t)=>void 0!==e?{amount:t}:{};P.setMeta({...R,...t(R.amount,e),charge:{...R.charge,...t(R.charge.amount,e)}})})),t=r((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.amount=b(),T.paymentSourceToken.length>0||g.selectedToken.length>0?{type:o.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:o.responseTypes.ERROR,message:l.fillDataError}}));return()=>{const a=e=>"function"==typeof e?e():null;a(t),a(e),c=!1}}),[o.responseTypes.ERROR,o.responseTypes.SUCCESS,r,s]),(0,t.createElement)("div",{id:"powerBoardWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":g.styles.background_color,color:g.styles.success_color,"font-size":g.styles.font_size,"font-family":g.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(g.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"power-board-validation-error"},l.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},l.notAvailable))},v={name:w,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"power-board-payment-method-label"},(0,t.createElement)("img",{src:`${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/icons/${d}.png`,alt:h,className:`power-board-payment-method-label-icon ${d}`}),"  "+h))),content:(0,t.createElement)(E,null),edit:(0,t.createElement)(E,null),placeOrderButtonLabel:l.placeOrderButtonLabel,canMakePayment:()=>function(e,t){if(null===(a=e)||"object"!=typeof a)return!0;var a;let n=0,o=0;return e.max&&(o=100*e.max),e.min&&(n=100*e.min),n=t>=n,o=0===o||t<=o,n&&o}(g.total_limitation,f.getCartTotals()?.total_price),ariaLabel:h,supports:{features:g.supports}};(0,o.registerPaymentMethod)(v)})("zip",0,"powerBoardAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();