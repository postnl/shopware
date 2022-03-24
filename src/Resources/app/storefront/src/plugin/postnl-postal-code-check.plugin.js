import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import PluginManager from 'src/plugin-system/plugin.manager';
import Debouncer from 'src/helper/debouncer.helper'
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util'

export default class PostnlPostalCodeCheckPlugin extends Plugin {
    static options = {
        url: window.router['frontend.address.postnl.postal-code-check'],
        csrfToken: 'put token here',
        countries: [],
        concatPrefix: ""
    }


    init() {
        this._client = new HttpClient();

        //Register elements
        this.zipcode= DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressZipcode');
        this.houseNumber = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumber');
        this.houseNumberAddition = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumberAddition');

        this._registerEvents();
        this._updateRequired();
        this._setupLinkedFields();
        console.log(this.options);
    }

    _registerEvents() {
        //Checkbox remove required
        const jsToggle = document.querySelector('#differentShippingAddress');
        const jsToggleInstance = PluginManager.getPluginInstanceFromElement(jsToggle, 'FormFieldToggle');
        jsToggleInstance.$emitter.subscribe('onChange', () => {
            this._updateRequired();
        })
        //Country checker
        DomAccess.querySelectorAll(this.el, '.country-select').forEach(input => {
            input.addEventListener('change', this.onChangeSelectedCountry.bind(this));
        });
        const debouceLookup = Debouncer.debounce(this._lookupAddress.bind(this), 2000);

        //Address fields
        this.zipcode.addEventListener('keyup', debouceLookup)
        this.houseNumber.addEventListener('keyup', debouceLookup)
        this.houseNumberAddition.addEventListener('keyup', debouceLookup)
    }

    _lookupAddress() {
        //Are all fields filled?
        if (this.zipcode.value != null && this.houseNumber.value != null) {
            this._checkPostalCode(this.zipcode.value, this.houseNumber.value, this.houseNumberAddition.value);
        }
    }

    _updateRequired() {
        //Check which form is hidden
        let postElement = DomAccess.querySelector(this.el, '#postNLAddressRow');
        let postHidden = postElement.getAttribute('hidden');
        let defaultElement = DomAccess.querySelector(this.el, '#defaultAddressRow');
        let defaultHidden = defaultElement.getAttribute('hidden');

        if (postHidden === 'hidden') {
            this._swapRequired(defaultElement, postElement)
        }
        if (defaultHidden === 'hidden') {
            this._swapRequired(postElement, defaultElement)
        }
    }

    _swapRequired(required, notRequired) {
        DomAccess.querySelectorAll(notRequired, 'input,select').forEach(input => {
            input.removeAttribute('required');
        });
        DomAccess.querySelectorAll(required, 'input,select').forEach(input => {
            input.setAttribute('required', 'required');
        });
    }

    _checkPostalCode(zipcodeValue, houseNumberValue, houseNumberAdditionValue) {
        const data = this._getRequestData();
        data['postalCode'] = '7821 AC';
        data['houseNumber'] = '20';
        // data['houseNumberAddition'] = 'NL';
        ElementLoadingIndicatorUtil.create(this.el);
        this._client.post(this.options.url, JSON.stringify(data), content =>{
            ElementLoadingIndicatorUtil.remove(this.el);
            this._parseRequest(JSON.parse(content))
        });
    }

    _parseRequest(data) {
        console.log('Logging data')
        console.log(data)
        //TODO: fill in all the data you got here
    }

    _getRequestData() {
        const data = {};
        if (window.csrf.enabled && window.csrf.mode === 'twig') {
            data['_csrf_token'] = this.options.csrfToken;
        }
        return data;
    }

    _adaptFormToSelectedCountryId(countryId) {
        if (this.options.countries[countryId] != null) {
            this._showCustomForm(true);
        } else {
            this._showCustomForm(false);
        }
    }

    _showCustomForm(show) {
        if (show) {
            DomAccess.querySelector(this.el, '#postNLAddressRow').removeAttribute('hidden');
            DomAccess.querySelector(this.el, '#defaultAddressRow').setAttribute('hidden', 'hidden');
        } else {
            DomAccess.querySelector(this.el, '#postNLAddressRow').setAttribute('hidden', 'hidden');
            DomAccess.querySelector(this.el, '#defaultAddressRow').removeAttribute('hidden');
        }
        this._updateRequired();
    }

    _setupLinkedFields() {
        //Postal code
        this._linkFields('#' + this.options.concatPrefix + 'AddressZipcode',
            '#' + this.options.concatPrefix + 'PostNLAddressZipcode')

        this._linkFields('#' + this.options.concatPrefix + 'PostNLAddressZipcode',
            '#' + this.options.concatPrefix + 'AddressZipcode')

        //City
        this._linkFields('#' + this.options.concatPrefix + 'AddressCity',
            '#' + this.options.concatPrefix + 'PostNLAddressCity')
        this._linkFields('#' + this.options.concatPrefix + 'PostNLAddressCity',
            '#' + this.options.concatPrefix + 'AddressCity')

        //Address (street + house number in shopware)
        //TODO: fix this with converter because the address needs to be compounded
        // this._linkFields('#'+this.options.concatPrefix+'AddressStreet',
        //     '#'+this.options.concatPrefix+'PostNLAddressStreet')
        // this._linkFields('#'+this.options.concatPrefix+'PostNLAddressStreet',
        //     '#'+this.options.concatPrefix+'AddressStreet')

    }

    _linkFields(field1Selector, field2Selector) {
        console.log('Linking ', field1Selector, 'with', field2Selector);
        const field1 = document.querySelector(field1Selector);
        const field2 = document.querySelector(field2Selector);
        field1.addEventListener('change', this.copyValue.bind(this, field1, field2));
    }

    copyValue(sender, receiver) {
        receiver.value = sender.value
    }

    onChangeSelectedCountry(e) {
        this._adaptFormToSelectedCountryId(e.target.value)
    }
}
