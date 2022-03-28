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
        concatPrefix: "",
    }

    init() {
        this._client = new HttpClient();
        this._registerElements();
        this._registerEvents();
        this._updateRequired();
        this._setupLinkedFields();
        this._prepareFormWithExistingData();

        console.log(this.options);
    }

    update(){
        console.log('UPDATE')
    }

    _registerElements(){
        //Register elements
        this.zipcodeElement= DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressZipcode');
        this.houseNumberElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumber');
        this.houseNumberAdditionElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumberAddition');
        this.streetElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressStreetShow');
        this.streetElementHidden = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressStreet');
        this.cityElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressCityShow');
        this.cityElementHidden = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressCity');
        //Shopware own
        this.zipcodeElementSW= DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressZipcode');
        this.streetElementSW = DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressStreet');
        this.cityElementSW = DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressCity');
        //Country selector
        this.countrySelectorElement = DomAccess.querySelector(this.el, '.country-select');
        //Excluded elements
        this.excludedElements = [this.houseNumberAdditionElement,this.streetElementHidden,this.cityElementHidden];
    }

    _registerEvents() {
        //Checkbox remove required
        const jsToggle = document.querySelector('#differentShippingAddress');
        if (jsToggle!=null){
            const jsToggleInstance = PluginManager.getPluginInstanceFromElement(jsToggle, 'FormFieldToggle');
            jsToggleInstance.$emitter.subscribe('onChange', () => {
                this._updateRequired();
            })
        }

        //Country checker
        DomAccess.querySelectorAll(this.el, '.country-select').forEach(input => {
            input.addEventListener('change', this.onChangeSelectedCountry.bind(this));
        });
        const debouceLookup = Debouncer.debounce(this._lookupAddress.bind(this), 1500);

        //Address fields
        this.zipcodeElement.addEventListener('keyup', debouceLookup)
        this.houseNumberElement.addEventListener('keyup', debouceLookup)
        this.houseNumberAdditionElement.addEventListener('keyup', debouceLookup)
    }

    _prepareFormWithExistingData(){
        //Setup if filled in
        let selectedCountryValue =this.countrySelectorElement.value
        if(selectedCountryValue!=null&&selectedCountryValue!==""){
            this._adaptFormToSelectedCountryId(selectedCountryValue);
        }
    }

    _lookupAddress() {
        //Are all fields filled?
        if (this.zipcodeElement.value != null && this.houseNumberElement.value != null) {
            this._checkPostalCode(this.zipcodeElement.value, this.houseNumberElement.value, this.houseNumberAdditionElement.value);
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
            //Unless it is excluded
            let found = this.excludedElements.includes(element => element!==input)
            if (found){
                input.setAttribute('required', 'required');
            }
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
            // this._parseRequest(JSON.parse(content))
            this._parseRequest(content) //TODO: swap me above when sdk works
        });
    }

    _parseRequest(data) {
        console.log('Parsing data')

        //TODO: fill in all the data you got here
        let city = "ZAANDAM"
        let streetName = "Wals"

        //Put the data in the our fields
        this.cityElement.value = city;
        this.streetElement.value = streetName;
        this.cityElementHidden.value = city;
        this.streetElementHidden.value = streetName;
        //Put the data in shopware fields (street+house number+addition, zipcode, city)
        this.streetElementSW.value = this.streetElement.value + ' ' + this.houseNumberElement.value + ' ' + this.houseNumberAdditionElement.value
        this.zipcodeElementSW.value = this.zipcodeElement.value;
        this.cityElementSW.value = this.cityElement.value;
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
        this._linkFields(this.zipcodeElementSW,this.zipcodeElement)
        this._linkFields(this.zipcodeElement,this.zipcodeElementSW)

        //City
        this._linkFields(this.cityElement,this.cityElementSW)
        this._linkFields(this.cityElementSW,this.cityElement)

        //Hidden street
        this._linkFields(this.streetElement,this.streetElementHidden)

        //Address (street + house number in shopware)
        //TODO: fix this with converter because the address needs to be compounded
        // this._linkFields('#'+this.options.concatPrefix+'AddressStreet',
        //     '#'+this.options.concatPrefix+'PostNLAddressStreet')
        // this._linkFields('#'+this.options.concatPrefix+'PostNLAddressStreet',
        //     '#'+this.options.concatPrefix+'AddressStreet')

    }

    _linkFields(field1Element, field2Element) {
        field1Element.addEventListener('change', this.copyValue.bind(this, field1Element, field2Element));
    }

    copyValue(sender, receiver) {
        receiver.value = sender.value
    }

    onChangeSelectedCountry(e) {
        this._adaptFormToSelectedCountryId(e.target.value)
    }
}
