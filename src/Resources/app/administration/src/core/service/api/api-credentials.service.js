import PostNlApiService from "../postnl-api.service";

export default class ApiCredentialsService extends PostNlApiService
{
    constructor(httpClient, loginService, apiBasePath = 'credentials') {
        super(httpClient, loginService, apiBasePath);
        this.name = 'PostNlApiCredentialsService'
    }

    checkCredentials(apiKey, sandbox = false) {
        return this.post('test', {
            apiKey: apiKey,
            sandbox: sandbox
        })
    }
}
