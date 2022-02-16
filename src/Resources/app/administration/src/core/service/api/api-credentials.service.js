import PostNlApiService from "../postnl-api.service";

export default class ApiCredentialsService extends PostNlApiService
{
    checkCredentials(apiKey, sandbox = false) {
        return this.post('credentials/test', {
            apiKey: apiKey,
            sandbox: sandbox
        })
    }
}
