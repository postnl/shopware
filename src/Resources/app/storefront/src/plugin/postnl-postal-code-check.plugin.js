import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';

export default class PostnlPostalCodeCheckPlugin extends Plugin{
    static options = {
        url: window.router['frontend.address.postnl.postal-code-check'],
        csrfToken: 'put token here',
        countries:[]
    }
    init() {
        this._client = new HttpClient();
        console.log(this.options);
        this._checkPostalCode();
        // this._registerEvents();
    }

    _checkPostalCode(){
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
}
