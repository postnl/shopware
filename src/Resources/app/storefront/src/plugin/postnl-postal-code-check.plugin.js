import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import Debouncer from 'src/helper/debouncer.helper'
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util'

export default class PostnlPostalCodeCheckPlugin extends window.PluginBaseClass {
    static options = {
        url: window.router['frontend.address.postnl.postal-code-check'],
        countries: [],
        concatPrefix: "",
    }

    /**
     * Init
     */
    init() {
        this._client = new HttpClient();
        const array = new Uint32Array(1);
        this.random = crypto.getRandomValues(array)[0];

        this._registerElements();
        this._makeElementsUnique()

        //Link the datalist
        this.houseNumberAdditionElement.setAttribute('list', this.houseNumberAdditionDatalistElement.id);

        this._showWarningAlert("");

        this._registerEvents();

        this._registerNonRequiredElements(this.defaultAddressRow);
        this._registerNonRequiredElements(this.postNLAddressRow);

        this._updateRequired();
        this._setupLinkedFields();
        this._prepareFormWithExistingData();
    }

    /**
     * Register all the elements for later use
     * @private
     */
    _registerElements() {
        //Custom form elements
        this.zipcodeElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressZipcode');
        this.houseNumberElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumber');
        this.houseNumberAdditionElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumberAddition');
        this.streetElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressStreet');
        this.cityElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressCity');

        //Data list
        this.houseNumberAdditionDatalistElement = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'PostNLAddressHouseNumberAdditionDatalist');

        //Country selector
        this.countrySelectorElement = DomAccess.querySelector(this.el, '.country-select');

        //Non Required elements
        this.nonRequiredElements = [];

        //Rows to hide
        this.postNLAddressRow = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'postNLAddressRow');
        this.defaultAddressRow = DomAccess.querySelector(this.el, '#' + this.options.concatPrefix + 'defaultAddressRow');

        //Alert blocks
        this.postnlWarningAlert = DomAccess.querySelector(this.el, '.postnl-alerts');
        this.postnlLiveRegion = DomAccess.querySelector(this.el, '.postnl-live-region');

        //Parent form
        this.addressForm = this.zipcodeElement.closest('form');

        //Shopware own
        this.zipcodeElementSW = DomAccess.querySelector(this.addressForm, '#' + this.options.concatPrefix + 'AddressZipcode');
        this.streetElementSW = DomAccess.querySelector(this.addressForm, '#' + this.options.concatPrefix + 'AddressStreet');
        this.cityElementSW = DomAccess.querySelector(this.addressForm, '#' + this.options.concatPrefix + 'AddressCity');
    }

    /**
     * Makes all the elements unique for lookup
     * @private
     */
    _makeElementsUnique() {
        this._makeElementUnique(this.zipcodeElement);
        this._makeElementUnique(this.houseNumberElement);
        this._makeElementUnique(this.houseNumberAdditionElement);
        this._makeElementUnique(this.streetElement);
        this._makeElementUnique(this.cityElement);
        this._makeElementUnique(this.houseNumberAdditionDatalistElement);
        this._makeElementUnique(this.postNLAddressRow);
        this._makeElementUnique(this.defaultAddressRow);
        this._makeElementUnique(this.postnlWarningAlert);
    }

    /**
     * @param element
     * @private
     */
    _makeElementUnique(element) {
        element.id += '-' + this.random
    }

    /**
     * @private
     */
    _registerEvents() {
        //Checkbox remove required
        const jsToggle = document.querySelector('#differentShippingAddress');
        if (jsToggle != null) {
            const jsToggleInstance = window.PluginManager.getPluginInstanceFromElement(jsToggle, 'FormFieldToggle');
            jsToggleInstance.$emitter.subscribe('onChange', () => {
                this._updateRequired();
            })
        }

        //Country checker
        DomAccess.querySelectorAll(this.el, '.country-select').forEach(input => {
            input.addEventListener('change', this.onChangeSelectedCountry.bind(this));
        });
        const debounceLookup = Debouncer.debounce(this._lookupAddress.bind(this), 500);

        //shopware field copiers
        this.streetElement.addEventListener('change', this._fillStreetFields.bind(this));
        this.houseNumberElement.addEventListener('change', this._fillStreetFields.bind(this));
        this.houseNumberAdditionElement.addEventListener('change', this._fillStreetFields.bind(this));

        //Address fields
        this.zipcodeElement.addEventListener('keyup', debounceLookup)
        this.houseNumberElement.addEventListener('keyup', debounceLookup)
        this.houseNumberAdditionElement.addEventListener('keyup', debounceLookup)
    }

    /**
     * @private
     */
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

    /**
     * Collects all elements that do not have the required property
     * @param elementsNode
     * @private
     */
    _registerNonRequiredElements(elementsNode) {
        DomAccess.querySelectorAll(elementsNode, 'input,select').forEach(input => {
            if (!input.hasAttribute('required')){
                this.nonRequiredElements.push(input);
            }
        });
    }

    /**
     * @private
     */
    _updateRequired() {
        //Reset the errors
        this.zipcodeElement.setCustomValidity("");
        this.houseNumberElement.setCustomValidity("");
        this.houseNumberAdditionElement.setCustomValidity("");

        //Check which form is hidden, set required as needed
        if (this.postNLAddressRow.getAttribute('hidden') === 'hidden') {
            this._swapRequired(this.defaultAddressRow, this.postNLAddressRow)
        }

        if (this.defaultAddressRow.getAttribute('hidden') === 'hidden') {
            this._swapRequired(this.postNLAddressRow, this.defaultAddressRow)
        }
        //Fix shopware removing disabled on a toggle form
        if (!this.streetElement.value) {
            this.streetElement.setAttribute('disabled', 'disabled');
        }
        if (!this.cityElement.value) {
            this.cityElement.setAttribute('disabled', 'disabled');
        }
    }

    /**
     * @param required
     * @param notRequired
     * @private
     */
    _swapRequired(required, notRequired) {

        DomAccess.querySelectorAll(notRequired, 'input,select').forEach(input => {
            input.removeAttribute('required');
        });

        DomAccess.querySelectorAll(required, 'input,select').forEach(input => {
            //Unless it is required
            let found = this.nonRequiredElements.find(element => {
                return element.id === input.id
            });

            if (!found) {
                input.setAttribute('required', 'required');
            }
        });
    }

    /**
     * @param zipcodeValue
     * @param houseNumberValue
     * @param houseNumberAdditionValue
     * @private
     */
    _checkPostalCode(zipcodeValue, houseNumberValue, houseNumberAdditionValue) {

        const data = {};
        data['postalCode'] = zipcodeValue;
        data['houseNumber'] = houseNumberValue;
        data['houseNumberAddition'] = houseNumberAdditionValue;
        ElementLoadingIndicatorUtil.create(this.el);
        this._client.post(this.options.url, JSON.stringify(data), (content, response) => {
            ElementLoadingIndicatorUtil.remove(this.el);
            this._parseRequest(JSON.parse(content), response);
        });
    }

    /**
     * @param innerHTML
     * @private
     */
    _showWarningAlert(innerHTML) {
        if (innerHTML === "") {
            this.postnlWarningAlert.setAttribute('hidden', 'hidden');
            this.postnlWarningAlert.querySelector('.alert-content').innerHTML = innerHTML
        } else {
            this.postnlWarningAlert.removeAttribute('hidden');
            this.postnlWarningAlert.querySelector('.alert-content').innerHTML = innerHTML
        }
    }

    /**
     * @private
     */
    _unlockFormFields() {
        //Unlock the fields
        this.streetElement.removeAttribute('disabled');
        this.cityElement.removeAttribute('disabled');
    }

    /**
     * @param data
     * @param response
     * @private
     */
    _parseRequest(data, response) {
        this._unlockFormFields();
        //Reset the errors
        this.zipcodeElement.setCustomValidity("");
        this.houseNumberElement.setCustomValidity("");

        if(response.status < 400) {
            this._handleSuccess(data, response);
        } else {
            this._handleError(data, response);
        }

    }

    _handleSuccess(data, response) {//Clear the warnings
        this._showWarningAlert("")

        const postalCode = data[0];

        //Put the data in our fields
        this.cityElement.value = postalCode['city'];
        this.streetElement.value = postalCode['streetName'];

        //Refill the existing fields with the results
        this.zipcodeElement.value = postalCode['postalCode'];
        this.houseNumberElement.value = postalCode['houseNumber'];
        if (postalCode['houseNumberAddition']) {
            //Is there more than one? Fill the datalist with options
            if (data.length > 1) {
                data.forEach(result => {
                    let option = document.createElement('option');
                    option.value = result['houseNumberAddition'];
                    this.houseNumberAdditionDatalistElement.appendChild(option);
                });
            } else {
                this.houseNumberAdditionElement.value = postalCode['houseNumberAddition'];
            }
        }

        //Put the data in shopware fields (street+house number+addition, zipcode, city)
        this._fillStreetFields();
        this.zipcodeElementSW.value = this.zipcodeElement.value;
        this.cityElementSW.value = this.cityElement.value;

        const announce = [
            postalCode['streetName'],
            postalCode['houseNumber'],
            postalCode['houseNumberAddition'],
            ...postalCode['postalCode'],
            postalCode['city']
        ]
            .filter((e) => e !== null && (typeof e !== 'string' || e.length > 0))

        this.postnlLiveRegion.innerHTML = announce.join(' ')
    }

    _handleError(data, response) {7
        //No result, so it is an error
        if (data['type'] === "NotFoundException") {
            this._showWarningAlert(data['message'])
        }
        else if (data['type'] === "InvalidArgumentException") {
            //Known errors with a field connected to it
            if (data['field']) {
                switch (data['field']) {
                    case 'postalcode':
                        this.zipcodeElement.setCustomValidity(data['message']);
                        break;
                    case 'housenumber':
                        this.houseNumberElement.setCustomValidity(data['message']);
                        break;
                }
            }
            else {
                //Unknown errors with a message connected to it
                this._showWarningAlert(data['message'])
            }
        }
        else if (!data['message']) {
            this._showWarningAlert(data);
        }

        this.zipcodeElement.reportValidity();
        this.houseNumberElement.reportValidity();
    }

    /**
     * @param countryId
     * @private
     */
    _adaptFormToSelectedCountryId(countryId) {
        if (this.options.countries[countryId] != null) {
            this._showCustomForm(true);
        } else {
            this._showCustomForm(false);
        }
    }

    /**
     * @param show
     * @private
     */
    _showCustomForm(show) {
        if (show) {
            this.postNLAddressRow.removeAttribute('hidden');
            this.defaultAddressRow.setAttribute('hidden', 'hidden');
        } else {
            this.postNLAddressRow.setAttribute('hidden', 'hidden');
            this.defaultAddressRow.removeAttribute('hidden');
        }
        this._updateRequired();
    }

    /**
     * @private
     */
    _setupLinkedFields() {
        //Postal code
        this._linkFields(this.zipcodeElementSW, this.zipcodeElement)
        this._linkFields(this.zipcodeElement, this.zipcodeElementSW)

        //City
        this._linkFields(this.cityElement, this.cityElementSW)
        this._linkFields(this.cityElementSW, this.cityElement)
    }

    /**
     * @param field1Element
     * @param field2Element
     * @private
     */
    _linkFields(field1Element, field2Element) {
        field1Element.addEventListener('change', this.copyValue.bind(this, field1Element, field2Element));
    }

    /**
     * @private
     */
    _fillStreetFields() {
        //Fill this with street + house number + house number addition
        let elementArray = [this.streetElement.value, this.houseNumberElement.value, this.houseNumberAdditionElement.value];
        this.streetElementSW.value = elementArray.filter((element) => element.trim() !== "").join(" ");
    }

    /**
     * @param sender
     * @param receiver
     */
    copyValue(sender, receiver) {
        receiver.value = sender.value
    }

    /**
     * @param e
     */
    onChangeSelectedCountry(e) {
        this._adaptFormToSelectedCountryId(e.target.value)
    }
}
