

function checkNonDutch() {
    cy.switchCountry('Germany')
    cy.get('[id*=postNLAddressRow-]').should('not.be.visible')
}

function checkDutch() {
    cy.switchCountry('Netherlands')
    cy.get('[id*=postNLAddressRow-]').should('be.visible')
}

function checkDisabledFields() {
    cy.switchCountry('Netherlands')

    cy.get('[id^=billingAddressPostNLAddressStreet-]').should('be.disabled')
    cy.get('[id^=billingAddressPostNLAddressCity-]').should('be.disabled')
}

function lookUpAddress() {
    cy.switchCountry('Netherlands')

    cy.fixture('user').then((user) => {
        cy.get('[id^=billingAddressPostNLAddressZipcode-]').type(user.zipcode);
        cy.get('[id^=billingAddressPostNLAddressHouseNumber-]').type(user.housenumber);
    })

    cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

    cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

    cy.get('[id^=billingAddressPostNLAddressStreet-]').should('have.value', 'Leidsestraatweg')
    cy.get('[id^=billingAddressPostNLAddressCity-]').should('have.value', 'WOERDEN')
    cy.get('[id^=billingAddressPostNLAddressHouseNumberAdditionDatalist-] > option').should('have.length', 13)
}

function lookUpAddressError() {
    cy.switchCountry('Netherlands')

    cy.fixture('user').then((user) => {
        cy.get('[id^=billingAddressPostNLAddressZipcode-]').type(user.zipcode);
        cy.get('[id^=billingAddressPostNLAddressHouseNumber-]').type('5');
    })

    cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

    cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

    cy.get('[id^=billingAddresspostNLAddressRow-]').contains('No address has been found, check if your input is correct.')
    cy.get('[id^=billingAddressPostNLAddressStreet-]').should('not.be.disabled')
    cy.get('[id^=billingAddressPostNLAddressCity-]').should('not.be.disabled')
}

describe('Register page check', () => {
    beforeEach(() => {
        cy.visit('/account/login')
    })

    it('Should not appear on non Dutch addresses', () => {
        checkNonDutch();
    })

    it('Should appear on Dutch addresses', () => {
        checkDutch();
    })

    it('Should have disabled fields on Dutch addresses', () => {
        checkDisabledFields();
    })

    it('Should look up an address', () => {
        lookUpAddress();
    })

    it('Should show an error on invalid address and unlock the fields', () => {
        lookUpAddressError();
    })
})

describe('Checkout page checks', () => {

    beforeEach(() => {
        cy.checkOutProduct()
    })

    it('Should not appear on non Dutch addresses', () => {
        checkNonDutch();
    })

    it('Should appear on Dutch addresses', () => {
        checkDutch();
    })

    it('Should have disabled fields on Dutch addresses', () => {
        checkDisabledFields();
    })

    it('Should look up an address', () => {
        lookUpAddress();
    })

    it('Should show an error on invalid address and unlock the fields', () => {
        lookUpAddressError();
    })
})

describe('Shipping page add address checks', () => {

    beforeEach(() => {
        cy.checkOutProduct()
        cy.fillAndSubmitCheckoutForm()
        cy.get('.confirm-billing-address').contains('Change billing address').click()
        cy.get('.js-pseudo-modal').contains('Add address').click()
    })

    it('Should not appear on non Dutch addresses', () => {
        cy.switchCountry('Germany')
        cy.get('#billing-address-create-new [id*=postNLAddressRow-]').should('not.be.visible')
    })

    it('Should appear on Dutch addresses', () => {
        cy.switchCountry('Netherlands')
        cy.get('#billing-address-create-new [id*=postNLAddressRow-]').should('be.visible')
    })

    it('Should have disabled fields on Dutch addresses', () => {
        cy.switchCountry('Netherlands')

        cy.get('#billing-address-create-new [id*=ostNLAddressStreet-]').should('be.disabled')
        cy.get('#billing-address-create-new [id*=ostNLAddressCity-]').should('be.disabled')
    })

    it('Should look up an address', () => {
        cy.switchCountry('Netherlands')

        cy.fixture('user').then((user) => {
            cy.get('#billing-address-create-new [id*=ostNLAddressZipcode-]').type(user.zipcode);
            cy.get('#billing-address-create-new [id*=ostNLAddressHouseNumber-]').type(user.housenumber);
        })

        cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

        cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

        cy.get('#billing-address-create-new [id*=ostNLAddressStreet-]').should('have.value', 'Leidsestraatweg')
        cy.get('#billing-address-create-new [id*=ostNLAddressCity-]').should('have.value', 'WOERDEN')
        cy.get('#billing-address-create-new [id*=ostNLAddressHouseNumberAdditionDatalist-] > option').should('have.length', 13)
    })

    it.only('Should show an error on invalid address and unlock the fields', () => {
        cy.switchCountry('Netherlands')

        cy.fixture('user').then((user) => {
            cy.get('#billing-address-create-new [id*=ostNLAddressZipcode-]').type(user.zipcode);
            cy.get('#billing-address-create-new [id*=ostNLAddressHouseNumber-]').type('5');
        })

        cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

        cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

        cy.get('#billing-address-create-new [id*=ostNLAddressRow-]').contains('No address has been found, check if your input is correct.')
        cy.get('#billing-address-create-new [id*=ostNLAddressStreet-]').should('not.be.disabled')
        cy.get('#billing-address-create-new [id*=ostNLAddressCity-]').should('not.be.disabled')
    })
})
