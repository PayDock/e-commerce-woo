jQuery(window).on('load', function () {
    const json = {
        "options": [{
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/ausbc.png`,
            "title": "Australian Bank Card",
            "type": "ausbc"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/diners.png`,
            "title": "Diner's Club, Diner's Club International, Diner's Club / Carte Blanche",
            "type": "diners"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/japcb.png`,
            "title": "Japanese Credit Bureau",
            "type": "japcb"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/maestro.png`, "title": "Maestro", "type": "maestro"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/laser.png`, "title": "Laser", "type": "laser"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/solo.png`, "title": "Solo (Paymentech)", "type": "solo"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/mastercard.png`,
            "title": "MasterCard",
            "type": "mastercard"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/discover.png`, "title": "Discover", "type": "discover"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/amex.png`, "title": "American Express", "type": "amex"
        }, {
            "image": `${window.powerBoardWidgetSettings.pluginUrlPrefix}assets/images/visa.png`,
            "title": "Visa, Visa Electron",
            "type": "visa"
        },]
    }
    const inputElement = document.getElementById('card-select');

    const createOptions = () => {
        const dropdown = document.getElementById('multiselect-power-board')
        if (dropdown === null) {
            return
        }
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
            <input type="checkbox" id="power-board-card-type-${index}" value="${option.type}" class="checkbox">
            <label for="power-board-card-type-${index}">
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

    const multiselect = document.querySelectorAll('.multiselect-power-board')

    if (typeof (multiselect) != 'undefined' && multiselect != null) {
        createOptions()
        multiselect.forEach((item) => {
            initMultiSelect(item)
        });
    }
})
