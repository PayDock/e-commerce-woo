(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,o=window.wc.wcBlocksRegistry,r=window.wp.data,s=window.wc.wcBlocksData,l="power_board",i={defaultLabel:(0,e.__)("Power Board Payments",l),placeOrderButtonLabel:(0,e.__)("Place Order by Power Board",l),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",l),notAvailable:(0,e.__)("The payment method is not available in your country.",l)};let c=!1;((d,p,m,u,y)=>{const _=`power_board_${d}_a_p_m_s_block_data`,w=`power_board_${d}_a_p_m_s_gateway`,b=(0,n.getSetting)(_,{}),f=(0,a.decodeEntities)(b.title)||(0,e.__)("Power Board Afterpay",l),g=e=>{const n=(0,r.select)(s.CART_STORE_KEY),{eventRegistration:o,emitResponse:l}=((0,r.select)(s.CHECKOUT_STORE_KEY),e),{onPaymentSetup:p,onCheckoutValidation:_}=o,w=n.getCustomerData().billingAddress,f=jQuery(".power-board-country-available"),g=jQuery(".power-board-validation-error"),E=jQuery("#"+m),h=jQuery(".wc-block-components-checkout-place-order-button"),k=jQuery("#paymentCompleted");let v=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(w,u),C=!!y.find((e=>e===w.country.toLowerCase())),S=null,P=null,R={...b};return R.customers="",R.styles="",R.supports="",g.hide(),f.hide(),E.hide(),v?v&&!C?(c=!1,f.show()):v&&C&&E.show():(c=!1,g.show()),setTimeout((()=>{v&&!c&&(c=!0,S=new window.cba.AfterpayCheckoutButton("#"+m,b.publicKey,b.gatewayId),P={amount:b.amount,currency:b.currency,email:w.email,first_name:w.first_name,last_name:w.last_name},R.gatewayType="afterpay"),S&&(S.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token"),S.setMeta(P),S.on("finish",(()=>{b.directCharge&&(R.directCharge=!0),b.fraud&&(R.fraud=!0,R.fraudServiceId=b.fraudServiceId),null!==h&&h.click(),k.show()})))}),100),(0,t.useEffect)((()=>{const e=p((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return R.paymentSourceToken=e.value,R.paymentSourceToken.length>0||b.selectedToken.length>0?{type:l.responseTypes.SUCCESS,meta:{paymentMethodData:R}}:{type:l.responseTypes.ERROR,message:i.fillDataError}}));return()=>{e()}}),[l.responseTypes.ERROR,l.responseTypes.SUCCESS,p,_]),(0,t.createElement)("div",{id:"powerBoardWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":b.styles.background_color,color:b.styles.success_color,"font-size":b.styles.font_size,"font-family":b.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(b.description||"")),(0,t.createElement)("div",{class:"logo-comm-bank"},(0,t.createElement)("img",{src:"/wp-content/plugins/power_board/assets/images/logo.png"})),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/power_board/assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"power-board-validation-error"},i.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},i.notAvailable))},E={name:w,label:(0,t.createElement)((e=>{const{PaymentMethodLabel:a}=e.components;return(0,t.createElement)(a,{text:f})}),null),content:(0,t.createElement)(g,null),edit:(0,t.createElement)(g,null),placeOrderButtonLabel:i.placeOrderButtonLabel,canMakePayment:()=>!0,ariaLabel:f,supports:{features:b.supports}};(0,o.registerPaymentMethod)(E)})("afterpay",0,"powerBoardAPMsAfterpayButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca","uk","gb","fr","it","es","de"])})();