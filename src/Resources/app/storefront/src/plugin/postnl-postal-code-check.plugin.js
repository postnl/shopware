import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import PluginManager from 'src/plugin-system/plugin.manager';


export default class PostnlPostalCodeCheckPlugin extends Plugin {
    static options = {
        url: window.router['frontend.address.postnl.postal-code-check'],
        csrfToken: 'put token here',
        countries: []
    }

    init() {
        this._client = new HttpClient();
        // this._checkPostalCode();
        this._registerEvents();
        this._updateRequired();
        this._setupLinkedFields();
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
    }

    _updateRequired(){
        //Check which form is hidden
        const postElement = DomAccess.querySelector(this.el, '#postNLAddressRow');
        const postHidden = postElement.getAttribute('hidden');
        const defaultElement = DomAccess.querySelector(this.el, '#defaultAddressRow');
        const defaultHidden = defaultElement.getAttribute('hidden');

        if (postHidden==='hidden'){
            this._swapRequired(defaultElement,postElement)
        }
        if (defaultHidden==='hidden'){
            this._swapRequired(postElement,defaultElement)
        }
    }

    _swapRequired(required,notRequired){
        DomAccess.querySelectorAll(notRequired, 'input,select').forEach(input => {
            input.removeAttribute('required');
        });
        DomAccess.querySelectorAll(required, 'input,select').forEach(input => {
            input.setAttribute('required','required');
        });
    }

    _checkPostalCode() {
        const data = this._getRequestData();
        data['postalCode'] = '7821 AC';
        data['houseNumber'] = '20';
        // data['houseNumberAddition'] = 'NL';
        this._client.post(this.options.url, JSON.stringify(data), content => this._parseRequest(JSON.parse(content)));
    }

    _parseRequest(data) {
        console.log('Logging data')
        console.log(data)
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

    _showCustomForm(show){
        if (show){
            DomAccess.querySelector(this.el, '#postNLAddressRow').removeAttribute('hidden');
            DomAccess.querySelector(this.el, '#defaultAddressRow').setAttribute('hidden','hidden');
        }else{
            DomAccess.querySelector(this.el, '#postNLAddressRow').setAttribute('hidden','hidden');
            DomAccess.querySelector(this.el, '#defaultAddressRow').removeAttribute('hidden');
        }
        this._updateRequired();
    }

    _setupLinkedFields(){
        //Postal code
        this._linkFields('#billingAddressAddressZipcode','#billingAddressPostNLAddressZipcode')
        this._linkFields('#shippingAddressAddressZipcode','#shippingAddressPostNLAddressZipcode')
        //Address
        //TODO: fix this with converter because the address needs to be added
        this._linkFields('#billingAddressAddressStreet','#shippingAddressPostNLAddressStreet')
        this._linkFields('#shippingAddressAddressStreet','#shippingAddressPostNLAddressStreet')
        //City
        this._linkFields('#billingAddressAddressCity','#shippingAddressPostNLAddressCity')
        this._linkFields('#shippingAddressAddressCity','#shippingAddressPostNLAddressCity')

    }

    _linkFields(field1Selector,field2Selector){
        const field1 = document.querySelector(field1Selector);
        const field2 = document.querySelector(field2Selector);
        field1.addEventListener('change', this.copyValue.bind(this,field1,field2));
    }

    copyValue(sender,receiver){
        receiver.value = sender.value
    }

    onChangeSelectedCountry(e) {
        this._adaptFormToSelectedCountryId(e.target.value)
    }
}
