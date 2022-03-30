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
        this._showWarningAlert("");
        this._registerEvents();
        this._updateRequired();
        this._setupLinkedFields();
        this._prepareFormWithExistingData();

        console.log(this.options);
    }

    _registerElements() {
        //Register elements

        //Custom form elements
        this.zipcodeElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressZipcode');
        this.houseNumberElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumber');
        this.houseNumberAdditionElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumberAddition');
        this.streetElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressStreet');
        this.cityElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressCity');

        //Shopware own
        this.zipcodeElementSW = DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressZipcode');
        this.streetElementSW = DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressStreet');
        this.cityElementSW = DomAccess.querySelector(document, '#' + this.options.concatPrefix + 'AddressCity');
        //Country selector
        this.countrySelectorElement = DomAccess.querySelector(this.el, '.country-select');

        //Excluded elements
        this.excludedElements = [this.houseNumberAdditionElement];

        //Rows to hide
        this.postNLAddressRow = DomAccess.querySelector(this.el, '#postNLAddressRow');
        this.defaultAddressRow = DomAccess.querySelector(this.el, '#defaultAddressRow');

        //Alert blocks
        this.postnlWarningAlert = DomAccess.querySelector(this.el, '.postnl-alerts .alert-warning');

        console.log(this.zipcodeElement);
        console.log(this.zipcodeElement.closest('form'));
        this.addressForm = this.zipcodeElement.closest('form');
    }

    _registerEvents() {
        //Checkbox remove required
        const jsToggle = document.querySelector('#differentShippingAddress');
        if (jsToggle != null) {
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

        //shopware field copiers
        this.streetElement.addEventListener('change', this._fillStreetFields.bind(this));
        this.houseNumberElement.addEventListener('change', this._fillStreetFields.bind(this));
        this.houseNumberAdditionElement.addEventListener('change', this._fillStreetFields.bind(this));

        //Address fields
        this.zipcodeElement.addEventListener('keyup', debouceLookup)
        this.houseNumberElement.addEventListener('keyup', debouceLookup)
        this.houseNumberAdditionElement.addEventListener('keyup', debouceLookup)

        //Form submit
        const submitForm = this.submitForm.bind(this);
        this.addressForm.addEventListener('submit', submitForm);
    }

    _prepareFormWithExistingData() {
        //Setup if filled in
        let selectedCountryValue = this.countrySelectorElement.value
        if (selectedCountryValue != null && selectedCountryValue !== "") {
            this._adaptFormToSelectedCountryId(selectedCountryValue);
        }
    }

    _lookupAddress() {
        //Are all fields filled?
        if (this.zipcodeElement.value !== "" && this.houseNumberElement.value !== "") {
            this._checkPostalCode(this.zipcodeElement.value, this.houseNumberElement.value, this.houseNumberAdditionElement.value);
        }
    }

    _updateRequired() {
        //Check which form is hidden
        if (this.postNLAddressRow.getAttribute('hidden') === 'hidden') {
            this._swapRequired(this.defaultAddressRow, this.postNLAddressRow)
        }
        if (this.defaultAddressRow.getAttribute('hidden') === 'hidden') {
            this._swapRequired(this.postNLAddressRow, this.defaultAddressRow)
        }
    }

    _swapRequired(required, notRequired) {
        DomAccess.querySelectorAll(notRequired, 'input,select').forEach(input => {
            input.removeAttribute('required');
        });
        DomAccess.querySelectorAll(required, 'input,select').forEach(input => {
            //Unless it is excluded
            let found = this.excludedElements.find(element => {
                return element.id !== input.id
            });
            if (found) {
                input.setAttribute('required', 'required');
            }
        });
    }

    _checkPostalCode(zipcodeValue, houseNumberValue, houseNumberAdditionValue) {
        const data = this._getRequestData();
        data['postalCode'] = zipcodeValue//'7821 AC';
        data['houseNumber'] = houseNumberValue//'20';
        data['houseNumberAddition'] = houseNumberAdditionValue//'NL';
        ElementLoadingIndicatorUtil.create(this.el);
        this._client.post(this.options.url, JSON.stringify(data), content => {
            ElementLoadingIndicatorUtil.remove(this.el);
            console.log(content);
            this._parseRequest(JSON.parse(content))
        });
    }

    _showWarningAlert(innerHTML) {
        if (innerHTML === "") {
            this.postnlWarningAlert.setAttribute('hidden', 'hidden');
            this.postnlWarningAlert.querySelector('.alert-content').innerHTML = innerHTML
        } else {
            this.postnlWarningAlert.removeAttribute('hidden');
            this.postnlWarningAlert.querySelector('.alert-content').innerHTML = innerHTML
        }

    }

    _parseRequest(data) {
        console.log('Parsing data')
        //Unlock the fields
        this.streetElement.removeAttribute('disabled');
        this.cityElement.removeAttribute('disabled');

        if (data['PostalCodeResult'] && Array.isArray(data['PostalCodeResult'])) {
            console.log(data['PostalCodeResult']);
            if (data['PostalCodeResult'].length === 0) {
                //Address not found
                this._showWarningAlert("ENTER DETAILS AT OWN PERIL")
            } else {
                this._showWarningAlert("")
                //Might have more
                let postalCode = data['PostalCodeResult'].at(0)
                console.log(postalCode);
                //Put the data in our fields
                this.cityElement.value = postalCode['city'];
                this.streetElement.value = postalCode['streetName'];

                //Refill the existing fields with the results
                this.zipcodeElement.value = postalCode['postalCode'];
                this.houseNumberElement.value = postalCode['houseNumber'];
                if (postalCode['houseNumberAddition']) {
                    this.houseNumberAdditionElement.value = postalCode['houseNumberAddition'];
                }

                //Put the data in shopware fields (street+house number+addition, zipcode, city)
                this.streetElementSW.value = this.streetElement.value + ' ' + this.houseNumberElement.value + ' ' + this.houseNumberAdditionElement.value
                this.zipcodeElementSW.value = this.zipcodeElement.value;
                this.cityElementSW.value = this.cityElement.value;

                this.zipcodeElement.setCustomValidity("");
                this.houseNumberElement.setCustomValidity("");
                this.houseNumberAdditionElement.setCustomValidity("");
            }
        } else {
            console.log('not valid', data);
            //Actual error
            //TODO:check the error type and do something with it
            this.zipcodeElement.setCustomValidity("OW FUCK");
            this.zipcodeElement.reportValidity();
            this.houseNumberElement.setCustomValidity("OW FUCK");
            this.houseNumberElement.reportValidity();
            if (this.houseNumberAdditionElement.value) {
                this.houseNumberAdditionElement.setCustomValidity("OW FUCK");
                this.houseNumberAdditionElement.reportValidity();
            }
        }
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
        this._linkFields(this.zipcodeElementSW, this.zipcodeElement)
        this._linkFields(this.zipcodeElement, this.zipcodeElementSW)

        //City
        this._linkFields(this.cityElement, this.cityElementSW)
        this._linkFields(this.cityElementSW, this.cityElement)

    }

    _linkFields(field1Element, field2Element) {
        field1Element.addEventListener('change', this.copyValue.bind(this, field1Element, field2Element));
    }

    _fillStreetFields() {
        //Fill this with street + house number + house number addition
        this.streetElementSW.value = this.streetElement.value + " " + this.houseNumberElement.value + this.houseNumberAdditionElement.value
    }

    submitForm(event) {
        this.streetElement.removeAttribute('disabled');
        this.cityElement.removeAttribute('disabled');
        return true;
    }

    copyValue(sender, receiver) {
        receiver.value = sender.value
    }

    onChangeSelectedCountry(e) {
        this._adaptFormToSelectedCountryId(e.target.value)
    }
}
