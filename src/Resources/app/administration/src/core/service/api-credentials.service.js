import PostNlApiService from "./postnl-api.service";

export default class ApiCredentialsService extends PostNlApiService
{
    checkCredentials(apiKey) {
        console.log(apiKey);
        return this.post('credentials/test', {
            apiKey: apiKey
        })
    }
}
