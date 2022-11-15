export default class RegisterRepository {

    /**
     *
     * @returns {*}
     */
    getSalutation() {
        return cy.get('#personalSalutation');
    }

    /**
     *
     * @returns {*}
     */
    getFirstname() {
        return cy.get('#personalFirstName');
    }

    /**
     *
     * @returns {*}
     */
    getLastname() {
        return cy.get('#personalLastName');
    }

    /**
     *
     * @returns {*}
     */
    getEmail() {
        return cy.get('#personalMail');
    }

    /**
     *
     * @returns {*}
     */
    getPassword() {
        return cy.get('#personalPassword');
    }

    /**
     *
     * @returns {*}
     */
    getStreet() {
        return cy.get('#billingAddressAddressStreet');
    }

    /**
     *
     * @returns {*}
     */
    getNLStreet(){
        return cy.get('[id^=billingAddressPostNLAddressStreet-]');
    }


    /**
     *
     * @returns {*}
     */
    getZipcode() {
        return cy.get('#billingAddressAddressZipcode');
    }

    /**
     *
     * @returns {*}
     */
    getNLZipcode() {
        return cy.get('[id^=billingAddressPostNLAddressZipcode-]');
    }

    /**
     *
     * @returns {*}
     */
    getNLHousenumber() {
        return cy.get('[id^=billingAddressPostNLAddressHouseNumber-]');
    }

    /**
     *
     * @returns {*}
     */
    getCity() {
        return cy.get('#billingAddressAddressCity');
    }

    /**
     *
     * @returns {*}
     */
    getNLCity() {
        return cy.get('[id^=billingAddressPostNLAddressCity-]');
    }

    /**
     *
     * @returns {*}
     */
    getCountry() {
        return cy.get('#billingAddressAddressCountry');
    }

    /**
     *
     * @returns {*}
     */
    getRegisterButton() {
        return cy.get('.register-submit > .btn');
    }

}
