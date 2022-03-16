import { stringify } from "qs";

// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiBasePath = '') {
        const endpoint = ['postnl'];
        endpoint.push(apiBasePath.replace(/^\/|\/$/g, '').trim());

        super(httpClient, loginService, endpoint.filter(s => s.trim().length > 0).join('/'));
    }

    get(url, data = {}) {
        return this.httpClient
            .get(
                `_action/${this.getApiBasePath()}/${url}?${stringify(data)}`,
                {
                    headers: this.getBasicHeaders(),
                }
            )
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    post(url, data = {}) {
        return this.httpClient
            .post(
                `_action/${this.getApiBasePath()}/${url}`,
                JSON.stringify(data),
                {
                    headers: this.getBasicHeaders(),
                }
            )
            .then((response) => {
                return ApiService.handleResponse(response);
            });
    }

    getBlob(url, data = {}) {
        return this.httpClient
            .get(
                `_action/${this.getApiBasePath()}/${url}?${stringify(data)}`,
                {
                    headers: this.getBasicHeaders(),
                    responseType: 'blob',
                }
            )
    }
}
