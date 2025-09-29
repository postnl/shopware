import { stringify } from "qs";

const { Application, Store } = Shopware;
// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiBasePath = '') {
        const endpoint = ['postnl'];
        endpoint.push(apiBasePath.replace(/^\/|\/$/g, '').trim());

        super(httpClient, loginService, endpoint.filter(s => s.trim().length > 0).join('/'));
    }

    getEndpoint(url) {
        return [this.getApiBasePath(), url]
            .filter(s => s.trim().length > 0)
            .join('/')
    }

    async get(url, data = {}) {
        return this.httpClient
            .get(
                `_action/${ this.getEndpoint(url) }?${ stringify(data) }`,
                {
                    headers: await this.buildHeaders(),
                }
            )
            .then(response => ApiService.handleResponse(response))
            .catch(error => Promise.reject(ApiService.handleResponse(error.response)))
    }

    async post(url, data = {}) {
        return this.httpClient
            .post(
                `_action/${ this.getEndpoint(url) }`,
                JSON.stringify(data),
                {
                    headers: await this.buildHeaders(),
                }
            )
            .then(response => ApiService.handleResponse(response))
            .catch(error => Promise.reject(ApiService.handleResponse(error.response)))
    }

    async getBlob(url, data = {}) {
        return this.httpClient
            .get(
                `_action/${ this.getEndpoint(url) }?${ stringify(data) }`,
                {
                    headers: await this.buildHeaders(),
                    responseType: 'blob',
                }
            )
    }

    async buildHeaders() {
        const languageId = await this.buildLocaleCache();

        const headers = {
            'sw-language-id': languageId
        }

        return this.getBasicHeaders(headers);
    }

    async buildLocaleCache() {
        const languageStore = Store.get('postnlLocaleLanguage');
        const locale = Application.getContainer('factory').locale.getLastKnownLocale();

        if (languageStore.locales.hasOwnProperty(locale)) {
            return languageStore.locales[locale];
        }

        if(languageStore.loadPromise) {
            return await languageStore.loadPromise;
        }

        return await languageStore.loadLastKnownLanguage();
    }

}
