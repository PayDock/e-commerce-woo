(()=>{"use strict";const e=window.wp.i18n,t=window.React,a=window.wp.htmlEntities,n=window.wc.wcSettings,r=window.wc.wcBlocksRegistry,s=window.wp.data,o=window.wc.wcBlocksData,i="power_board",c={defaultLabel:(0,e.__)("PowerBoard Payments",i),placeOrderButtonLabel:(0,e.__)("Place Order by PowerBoard",i),validationError:(0,e.__)("Please fill in the required fields of the form to display payment methods",i),notAvailable:(0,e.__)("The payment method is not available in your country.",i)};let l=!1;((d,p,m,u,y)=>{const _=`power_board_${d}_a_p_m_s_block_data`,w=`power_board_${d}_a_p_m_s_gateway`,g=(0,n.getSetting)(_,{}),h=(0,a.decodeEntities)(g.title)||(0,e.__)("PowerBoard Zip",i),b=e=>{const n=(0,s.select)(o.CART_STORE_KEY),{eventRegistration:r,emitResponse:i}=e,{onPaymentSetup:p,onCheckoutValidation:_}=r,w=n.getCustomerData().billingAddress,h=n.getCustomerData().shippingAddress,b=n.getShippingRates(),f=jQuery(".power-board-country-available"),E=jQuery(".power-board-validation-error"),k=jQuery("#"+m),v=jQuery(".wc-block-components-checkout-place-order-button"),C=jQuery("#paymentCompleted");let S=((e,t)=>{for(let a=0;a<t.length;a++)if(!e.hasOwnProperty(t[a])||!e[t[a]])return!1;return!0})(w,u),R=!!y.find((e=>e===w.country.toLowerCase())),B=null,P={},T={...g};return T.customers="",T.styles="",T.supports="",T.pickupLocations="",E.hide(),f.hide(),k.hide(),S?S&&!R?(l=!1,f.show()):S&&R&&k.show():(l=!1,E.show()),setTimeout((()=>{if(S&&!l&&(l=!0,B=new window.cba.ZipmoneyCheckoutButton("#"+m,g.publicKey,g.gatewayId),T.gatewayType="zippay"),B){B.onFinishInsert('input[name="payment_source_apm_token"]',"payment_source_token");const e={first_name:h.first_name,last_name:h.last_name,line1:h.address_1,line2:h.address_2,country:h.country,postcode:h.postcode,city:h.city,state:h.state};b.length&&b[0].shipping_rates.length&&b[0].shipping_rates.forEach(((t,a)=>{if(!t.selected)return;if(e.amount=Number((t.price/100).toFixed(3)).toFixed(2),e.currency=t.currency_code,"pickup_location"!==t.method_id)return;const n=t.rate_id.split(":"),r=g.pickupLocations[n[1]];e.line1=r.address.address_1,e.line2="",e.country=r.address.country,e.postcode=r.address.postcode,e.city=r.address.city,e.state=r.address.state})),P.charge={amount:g.amount,currency:g.currency,email:w.email,first_name:w.first_name,last_name:w.last_name,shipping_address:e,billing_address:{first_name:w.first_name,last_name:w.last_name,line1:w.address_1,line2:w.address_2,country:w.country,postcode:w.postcode,city:w.city,state:w.state},items:n.getCartData().items.map((e=>{const t={name:e.name,amount:e.prices.price/100,quantity:e.quantity,reference:e.short_description};return e.images.length>0&&(t.image_uri=e.images[0].src),t}))},B.setMeta(P),B.on("finish",(()=>{g.directCharge&&(T.directCharge=!0),g.fraud&&(T.fraud=!0,T.fraudServiceId=g.fraudServiceId),null!==v&&v.click(),C.show()}))}}),100),(0,t.useEffect)((()=>{const e=p((async()=>{const e=document.querySelector('input[name="payment_source_apm_token"]');if(null!==e)return T.paymentSourceToken=e.value,T.paymentSourceToken.length>0||g.selectedToken.length>0?{type:i.responseTypes.SUCCESS,meta:{paymentMethodData:T}}:{type:i.responseTypes.ERROR,message:c.fillDataError}}));return()=>{e()}}),[i.responseTypes.ERROR,i.responseTypes.SUCCESS,p,_]),(0,t.createElement)("div",{id:"powerBoardWidgetApm"},(0,t.createElement)("div",{id:"paymentCompleted",style:{display:"none","background-color":g.styles.background_color,color:g.styles.success_color,"font-size":g.styles.font_size,"font-family":g.styles.font_family}},"Payment Details Collected"),(0,t.createElement)("div",null,(0,a.decodeEntities)(g.description||"")),(0,t.createElement)("div",{class:"apms-button-wrapper"},(0,t.createElement)("button",{type:"button",id:m,class:`btn-apm btn-apm-${d}`,style:{display:"none"}},(0,t.createElement)("img",{src:`/wp-content/plugins/power-board/assets/images/${d}.png`}))),(0,t.createElement)("div",{class:"power-board-validation-error"},c.validationError),(0,t.createElement)("input",{type:"hidden",name:"payment_source_apm_token"}),(0,t.createElement)("div",{class:"power-board-country-available",style:{display:"none"}},c.notAvailable))},f={name:w,label:(0,t.createElement)((()=>(0,t.createElement)("div",{className:"power-board-payment-method-label"},(0,t.createElement)("img",{src:`/wp-content/plugins/power-board/assets/images/icons/${d}.png`,alt:h,className:`power-board-payment-method-label-icon ${d}`}),"  "+h))),content:(0,t.createElement)(b,null),edit:(0,t.createElement)(b,null),placeOrderButtonLabel:c.placeOrderButtonLabel,canMakePayment:()=>!0,ariaLabel:h,supports:{features:g.supports}};(0,r.registerPaymentMethod)(f)})("zip",0,"powerBoardAPMsZipButton",["first_name","last_name","email","address_1","city","state","country","postcode"],["au","nz","us","ca"])})();