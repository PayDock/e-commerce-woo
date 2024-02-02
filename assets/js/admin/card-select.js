jQuery(window).on('load', function () {
    const json = {
        "options": [
            {
                "image": "/wp-content/plugins/paydock/assets/images/advam.png",
                "title": "Advam",
                "type": "Advam"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/adyen.png",
                "title": "Adyen",
                "type": "Adyen"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/authorize.png",
                "title": "Authorize Net",
                "type": "AuthorizeNet"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/bpoint.png",
                "title": "Bpoint",
                "type": "Bpoint"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/braintree.png",
                "title": "Brain Tree",
                "type": "BrainTree"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/cybersource.png",
                "title": "Cybersource",
                "type": "Cybersource"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/elavon.png",
                "title": "Elavon",
                "type": "Elavon"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/ems.png",
                "title": "Ems",
                "type": "Ems"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/eway.png",
                "title": "Eway",
                "type": "Eway"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/ezidebit.png",
                "title": "Ezidebit",
                "type": "Ezidebit"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/ezidebit35.png",
                "title": "Ezidebit 3.5",
                "type": "Ezidebit3"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/fatzebra.png",
                "title": "Fat Zebra",
                "type": "FatZebra"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/first_american.png",
                "title": "First American",
                "type": "FirstAmerican"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/fiserv.png",
                "title": "Fiserv",
                "type": "Fiserv"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/flow2cash.png",
                "title": "Flo 2 Cash",
                "type": "Flo2Cash"
            }, {
                "image": "/wp-content/plugins/paydock/assets/images/flypay.png",
                "title": "Flypay",
                "type": "Flypay"
            }, {
                "image": "/wp-content/plugins/paydock/assets/images/flypay_v2.png",
                "title": "Flypay V2",
                "type": "FlypayV2"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/forte.png",
                "title": "Forte",
                "type": "Forte"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/bambora.png",
                "title": "Bambora",
                "type": "Bambora"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/mastercard_gateway.png",
                "title": "Mastercard Payment Gateway Services (MPGS)",
                "type": "MasterCard"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/mw.png",
                "title": "Merchantwarrior",
                "type": "Merchantwarrior"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/moneris.png",
                "title": "Moneris",
                "type": "Moneris"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/nab.png",
                "title": "NAB",
                "type": "NAB",
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/paperless.png",
                "title": "Paperless",
                "type": "Paperless"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/windcave.png",
                "title": "Windcave",
                "type": "Windcave"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/westpac.png",
                "title": "Pay Way Classic",
                "type": "PayWayClassic"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/pin.png",
                "title": "Pin",
                "type": "Pin"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/promise-pay.png",
                "title": "Assembly",
                "type": "Assembly"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/qualpay.svg",
                "title": "Qualpay",
                "type": "Qualpay"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/securepay.png",
                "title": "Secure Pay",
                "type": "SecurePay"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/zepto.png",
                "title": "Zepto",
                "type": "Zepto"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/sqid.png",
                "title": "Sqid",
                "type": "Sqid"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/stripe.png",
                "title": "Stripe",
                "type": "Stripe"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/till-payments.png",
                "title": "Till Payments",
                "type": "TillPayments"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/worldpay.png",
                "title": "World Pay",
                "type": "WorldPay"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/worldpay.png",
                "title": "World Pay WPG",
                "type": "WorldPayWPG"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/eftpos.png",
                "title": "EFTPOS",
                "type": "EFTPOS"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/vii.png",
                "title": "Vii",
                "type": "Vii"
            },
            {
                "image": "/wp-content/plugins/paydock/assets/images/diners.png",
                "title": "Diners Club",
                "type": "DinersClub"
            }
        ]
    }
    const inputElement = document.getElementById('card-select');

    const createOptions = () => {
        const dropdown = document.getElementById('multiselect')
        const itemWrap = document.createElement('div')
        const itemUl = document.createElement('ul')

        itemWrap.classList.add('select-wrap')
        itemWrap.innerHTML = `
        <div class="search-wrap">
            <input name="search" type="text" autocomplete="off" placeholder="Search..." class="search">
            <div class="error">No results</div>
        </div>
    `
        json.options.map((option, index) => {
            const itemLi = document.createElement('li')

            itemLi.innerHTML = `
            <input type="checkbox" id="${index}" value="${option.type}" class="checkbox">
            <label for="${index}">
                <i><img src="${option.image}" alt="${option.title}"></i>${option.title}
            </label>
        `

            itemUl.appendChild(itemLi)
            itemWrap.appendChild(itemUl)
            dropdown.appendChild(itemWrap)
        })
    }

    const initMultiSelect = (elem) => {
        const select = elem
        const selectValue = elem.querySelector('.value')
        const search = elem.querySelector('.search')
        const searchError = elem.querySelector('.search-wrap .error')
        const list = elem.querySelectorAll('li')
        const listWrap = elem.querySelector('.select-wrap ul')

        selectValue.innerText = inputElement.value ? inputElement.value : selectValue.innerText;

        const allCheckbox = elem.querySelectorAll('input[type=checkbox]');
        Array.prototype.slice.call(allCheckbox, 0).map(function (checkbox) {
            if (selectValue.innerText.includes(checkbox.value)) {
                checkbox.checked = true;
            }
        });

        const valueDefault = 'Please select payment methods...'

        let timerLeave = null

        select.addEventListener('change', function () {
            const checked = elem.querySelectorAll('input[type=checkbox]:checked')
            const values = Array.prototype.slice.call(checked, 0).map(function (checkbox) {
                return checkbox.value
            });
            inputElement.value = (checked.length ? selectValue.textContent = values.join(', ') : selectValue.textContent = valueDefault)
        })

        search.addEventListener('input', () => {
            list.forEach(item => {
                const text = item.querySelector('label').innerText.toUpperCase()
                item.style.display = text.includes(search.value.toUpperCase()) ? 'block' : 'none'
            })
            listWrap.style.overflowY = listWrap.offsetHeight < 206 ? 'auto' : 'scroll'
            searchError.style.display = listWrap.offsetHeight < 40 ? 'block' : 'none'
        })

        selectValue.addEventListener('click', function () {
            select.classList.toggle('-open')
        })

        document.addEventListener('click', function (e) {
            if (!e.composedPath().includes(select)) {
                select.classList.remove('-open')
            }
        })

        select.onmouseenter = () => {
            clearTimeout(timerLeave)
        }

        select.onmouseleave = () => {
            timerLeave = setTimeout(() => {
                search.value = '';

                list.forEach(item => {
                    const text = item.querySelector('label').innerText.toUpperCase()
                    item.style.display = text.includes(search.value.toUpperCase()) ? 'block' : 'none'
                })
                searchError.style.display = 'none'

                select.classList.remove('-open')
            }, 500)
        }
    }

    const multiselect = document.querySelectorAll('.multiselect')

    if (typeof (multiselect) != 'undefined' && multiselect != null) {
        createOptions()
        multiselect.forEach((item) => {
            initMultiSelect(item)
        });
    }
})
