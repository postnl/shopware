// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'postnl') {
        super(httpClient, loginService, apiEndpoint);
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
}
