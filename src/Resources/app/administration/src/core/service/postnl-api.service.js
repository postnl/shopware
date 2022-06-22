import { stringify } from "qs";

const { Application, Context, State } = Shopware;
const { Criteria } = Shopware.Data;
// eslint-disable-next-line no-undef
const ApiService = Shopware.Classes.ApiService;

export default class PostNlApiService extends ApiService {
    constructor(httpClient, loginService, apiBasePath = '') {
        const endpoint = ['postnl'];
        endpoint.push(apiBasePath.replace(/^\/|\/$/g, '').trim());

        super(httpClient, loginService, endpoint.filter(s => s.trim().length > 0).join('/'));
    }

    async get(url, data = {}) {
        return this.httpClient
            .get(
                `_action/${ this.getApiBasePath() }/${ url }?${ stringify(data) }`,
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
                `_action/${ this.getApiBasePath() }/${ url }`,
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
                `_action/${ this.getApiBasePath() }/${ url }?${ stringify(data) }`,
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
        const cache = State.get('postnlLocaleLanguage').locales;
        const locale = Application.getContainer('factory').locale.getLastKnownLocale();

        if (cache.hasOwnProperty(locale)) {
            return cache[locale];
        }

        const criteria = new Criteria();
        criteria.addFilter(Criteria.equals('locale.code', locale));

        return await Application
            .getContainer('service')
            .repositoryFactory
            .create('language')
            .searchIds(criteria, Context.api)
            .then(result => {
                const languageId = result.data[0];
                State.commit('postnlLocaleLanguage/addLanguage', { locale, languageId });
                return languageId;
            })
    }

}
