

function checkNonDutch() {
    cy.switchCountry('Germany')
    cy.get('[id*=postNLAddressRow-]').should('not.be.visible')
}

function checkDutch() {
    cy.switchCountry('Netherlands')
    cy.get('[id*=postNLAddressRow-]').should('be.visible')
}

function checkDisabledFields(type) {

    cy.get('[id^='+type+'AddressPostNLAddressStreet-]').should('be.disabled')
    cy.get('[id^='+type+'AddressPostNLAddressCity-]').should('be.disabled')
}

function lookUpAddress(type) {
    cy.fixture('user').then((user) => {
        cy.get('[id^='+type+'AddressPostNLAddressZipcode-]').type(user.zipcode);
        cy.get('[id^='+type+'AddressPostNLAddressHouseNumber-]').type(user.housenumber);
    })

    cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

    cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

    cy.get('[id^='+type+'AddressPostNLAddressStreet-]').should('have.value', 'Leidsestraatweg')
    cy.get('[id^='+type+'AddressPostNLAddressCity-]').should('have.value', 'WOERDEN')
    cy.get('[id^='+type+'AddressPostNLAddressHouseNumberAdditionDatalist-] > option').should('have.length', 13)
}

function lookUpAddressError(type) {

    cy.fixture('user').then((user) => {
        cy.get('[id^='+type+'AddressPostNLAddressZipcode-]').type(user.zipcode);
        cy.get('[id^='+type+'AddressPostNLAddressHouseNumber-]').type('5');
    })

    cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

    cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

    cy.get('[id^='+type+'AddresspostNLAddressRow-]').contains('No address has been found, check if your input is correct.')
    cy.get('[id^='+type+'AddressPostNLAddressStreet-]').should('not.be.disabled')
    cy.get('[id^='+type+'AddressPostNLAddressCity-]').should('not.be.disabled')
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
        cy.switchCountry('Netherlands')
        checkDisabledFields('billing');
    })

    it('Should look up an address', () => {
        cy.switchCountry('Netherlands')
        lookUpAddress('billing');
    })

    it('Should show an error on invalid address and unlock the fields', () => {
        cy.switchCountry('Netherlands')
        lookUpAddressError('billing');
    })
})

describe('Checkout page checks', () => {

    function switchShippingCountry(country) {
        cy.get('#shippingAddressAddressCountry').select(country)
    }

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
        cy.switchCountry('Netherlands')
        checkDisabledFields('billing');

        cy.get('#differentShippingAddress').check({force: true});
        switchShippingCountry('Netherlands');
        checkDisabledFields('shipping');
    })

    it
    ('Should look up an address', () => {
        cy.switchCountry('Netherlands')
        lookUpAddress('billing');

        cy.get('#differentShippingAddress').check({force: true});
        switchShippingCountry('Netherlands');
        lookUpAddress('shipping');
    })

    it('Should show an error on invalid address and unlock the fields', () => {
        cy.switchCountry('Netherlands')
        lookUpAddressError('billing');

        cy.get('#differentShippingAddress').check({force: true});
        switchShippingCountry('Netherlands');
        lookUpAddressError('shipping');
    })

    it('should work on shipping address', function () {

        cy.get('[id*=shippingAddresspostNLAddressRow-]').should('be.visible')

        cy.fixture('user').then((user) => {
            cy.get('[id^=shippingAddressPostNLAddressZipcode-]').type(user.zipcode);
            cy.get('[id^=shippingAddressPostNLAddressHouseNumber-]').type(user.housenumber);
        })

        cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')

        cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

        cy.get('[id^=shippingAddressPostNLAddressStreet-]').should('have.value', 'Leidsestraatweg')
        cy.get('[id^=shippingAddressPostNLAddressCity-]').should('have.value', 'WOERDEN')
        cy.get('[id^=shippingAddressPostNLAddressHouseNumberAdditionDatalist-] > option').should('have.length', 13)
    });
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
        cy.get('#billing-address-create-new [id*=postNLAddressRow-]').scrollIntoView().should('be.visible')
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

    it('Should show an error on invalid address and unlock the fields', () => {
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
