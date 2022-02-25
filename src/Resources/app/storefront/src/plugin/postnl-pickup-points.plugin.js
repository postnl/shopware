import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';

export default class PostnlPickupPointsPlugin extends Plugin {
    static options = {
        url: window.router['frontend.checkout.postnl.pickup-point'],
        csrfToken: ''
    }

    init() {
        this._client = new HttpClient();
console.log(this.options);
        this._registerEvents();
    }

    onChangePickupPoint(e) {
        const data = this._getRequestData();
        data['pickupPointLocationCode'] = e.target.value;

        console.log(data, this.options);

        this._client.post(this.options.url, JSON.stringify(data), content => this._parseRequest(JSON.parse(content)));
    }

    _registerEvents() {
        DomAccess.querySelectorAll(this.el, '.postnl-pickup-point-input').forEach(input => {
            input.addEventListener('change', this.onChangePickupPoint.bind(this));
        });
    }

    _parseRequest(data) {
    }

    _getRequestData() {
        const data = {};

        if (window.csrf.enabled && window.csrf.mode === 'twig') {
            data['_csrf_token'] = this.options.csrfToken;
        }

        return data;
    }
}
