// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

// const qs = require('qs');

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'postnl') {
        super(httpClient, loginService, apiEndpoint);
    }

    get(url, data = {}) {
        // for(const key in data) {
        //     console.log(key, data[key]);
        // }

        //https://www.shopware.com/en/news/installing-your-own-dependencies-via-npm/
        // const x = qs.stringify(data);
        // console.log(x);


        return this.httpClient
            .get(
                `_action/${this.getApiBasePath()}/${url}`,
                {
                    headers: this.getBasicHeaders(),
                    params: data,
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
}
