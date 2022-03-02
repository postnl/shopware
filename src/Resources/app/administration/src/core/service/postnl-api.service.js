import { stringify } from "qs";

// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'postnl') {
        super(httpClient, loginService, apiEndpoint);
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
