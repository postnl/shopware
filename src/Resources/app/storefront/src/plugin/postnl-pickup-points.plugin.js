import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';
import ElementLoadingIndicatorUtil from 'src/utility/loading-indicator/element-loading-indicator.util';

export default class PostnlPickupPointsPlugin extends Plugin {
    static options = {
        url: window.router['frontend.checkout.postnl.pickup-point'],
    }

    init() {
        this._client = new HttpClient();

        this._registerEvents();
    }

    onChangePickupPoint(e) {
        const data = {}
        data['pickupPointLocationCode'] = e.target.value;

        this._client.post(this.options.url, JSON.stringify(data), content => this._parseRequest(JSON.parse(content)));
    }

    _registerEvents() {
        DomAccess.querySelectorAll(this.el, '.postnl-pickup-point-input').forEach(input => {
            input.addEventListener('change', this.onChangePickupPoint.bind(this));
        });
    }

    _parseRequest(data) {
    }
}
