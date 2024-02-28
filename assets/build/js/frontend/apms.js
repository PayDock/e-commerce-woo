(()=>{"use strict";const e=window.React,t=window.wp.i18n,a=window.wc.wcBlocksRegistry,n=window.wp.htmlEntities,o=(0,window.wc.wcSettings.getSetting)("power_board_apms_data",{}),r="power_board",s={defaultLabel:(0,t.__)("PowerBoard Payments",r),placeOrderButtonLabel:(0,t.__)("Place Order by PowerBoard",r),validationError:(0,t.__)("Please fill required fields of the form to display payment methods",r),availableAfterpay:(0,t.__)("Payment method Afterpay is not avalaible for your country!!!",r),availableZippay:(0,t.__)("Payment method Zippay is not avalaible for your country!!!",r)},l=(0,n.decodeEntities)(o.title)||s.defaultLabel,p=t=>{const{eventRegistration:a,emitResponse:r}=t,{onPaymentSetup:l,onCheckoutValidation:p}=a;return(0,e.useEffect)((()=>{const e=l((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e){if(o.paymentSourceToken=e.value,o.paymentSourceToken.length>0||o.selectedToken.length>0){const e={...o};return e.customers="",e.styles="",e.supports="",{type:r.responseTypes.SUCCESS,meta:{paymentMethodData:e}}}return{type:r.responseTypes.ERROR,message:s.fillDataError}}}));return()=>{e()}}),[r.responseTypes.ERROR,r.responseTypes.SUCCESS,l,p]),(0,e.createElement)("div",null,(0,e.createElement)("div",null,(0,n.decodeEntities)(o.description||"")),(0,e.createElement)("div",{class:"logo-comm-bank"},(0,e.createElement)("img",{src:"/wp-content/plugins/power_board/assets/images/logo.png"})),(0,e.createElement)("div",{id:"powerBoardWidgetApm",class:"power_board-widget-content",style:{display:"none","text-align":"center"}},(0,e.createElement)("button",{type:"button",src:"/wp-content/plugins/power_board/assets/images/zip_money.png",id:"zippay",class:"btn-apm btn-apm-zippay",style:{display:"none"}},(0,e.createElement)("img",{src:"/wp-content/plugins/power_board/assets/images/zip_money.png"})),(0,e.createElement)("button",{type:"button",src:"/wp-content/plugins/power_board/assets/images/zip_money.png",id:"afterpay",class:"btn-apm btn-apm-afterpay",style:{display:"none"}},(0,e.createElement)("img",{src:"/wp-content/plugins/power_board/assets/images/afterpay_icon.png"}))),(0,e.createElement)("div",{class:"power_board-validation-error"},s.validationError),(0,e.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,e.createElement)("div",{class:"power_board-country-available-afterpay",style:{display:"none"}},s.availableAfterpay),(0,e.createElement)("div",{class:"power_board-country-available-zippay",style:{display:"none"}},s.availableZippay))},i={name:"power_board_apms_gateway",label:(0,e.createElement)((t=>{const{PaymentMethodLabel:a}=t.components;return(0,e.createElement)(a,{text:l})}),null),content:(0,e.createElement)(p,null),edit:(0,e.createElement)(p,null),placeOrderButtonLabel:s.placeOrderButtonLabel,canMakePayment:()=>!0,ariaLabel:l,supports:{features:o.supports}};(0,a.registerPaymentMethod)(i)})();