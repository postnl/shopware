import RegisterRepository from 'Repositories/storefront/account/RegisterRepository';
import AdminAPIClient from "Services/shopware/AdminAPIClient";

const repo = new RegisterRepository();


export default class RegisterAction {

    /**
     *
     */
    constructor() {
        this.apiClient = new AdminAPIClient();
    }


    /**
     *
     * @param email
     * @param password
     */
    doRegister(email, password) {

        cy.visit('/account');

        repo.getSalutation().select('Mr.');

        repo.getFirstname().clear().type('FirstName');
        repo.getLastname().clear().type('LastName');

        repo.getEmail().clear().type(email);
        repo.getPassword().clear().type(password);

        repo.getCountry().select('Netherlands');

        //We are not waiting for the call to complete

        repo.getNLZipcode().clear({force: true}).type('3443BS', {force: true});
        repo.getNLHousenumber().clear({force: true}).type('107', {force: true});

        cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')
        cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

        // repo.getNLStreet().clear({force: true}).type('TestStreet', {force: true});
        // repo.getNLCity().clear({force: true}).type('TestCity', {force: true});

        repo.getRegisterButton().click();
    }


    doUnregister(email) {
        let data = {
            "query": [
                {
                    "score": 500,
                    "query": {"type": "equals", "field": "email", "value": email}
                }
            ]
        }
        this.apiClient.post('/search-ids/customer', data)
            .then((id) => {
                console.log(id)
                this.apiClient.delete('/customer/'+id)
            });
    }
}
