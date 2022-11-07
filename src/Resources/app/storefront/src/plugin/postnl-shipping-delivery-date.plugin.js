import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';

export default class PostnlShippingDeliveryDatePlugin extends Plugin {
    static options = {
        url: window.router['frontend.checkout.postnl.shipping-date'],
        csrfToken: ''
    }

    init() {
        this._client = new HttpClient();
        console.log(this.options);
        this._registerEvents();
    }

    onChangeDeliveryDate(e) {
        const data = this._getRequestData();
        data['deliveryDate'] = e.target.value;
        
        ElementLoadingIndicatorUtil.create(this.el);
        this._client.post(this.options.url, JSON.stringify(data), content => {
            ElementLoadingIndicatorUtil.remove(this.el);
            this._parseRequest(JSON.parse(content))
        });
    }

    _registerEvents() {
        DomAccess.querySelectorAll(this.el, '[name="postnl-shipping-delivery-time"]').forEach(input => {
            input.addEventListener('change', this.onChangeDeliveryDate.bind(this));
        });
    }

    _getRequestData() {
        const data = {};

        if (window.csrf.enabled && window.csrf.mode === 'twig') {
            data['_csrf_token'] = this.options.csrfToken;
        }

        return data;
    }
}
