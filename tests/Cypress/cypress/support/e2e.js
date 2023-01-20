// ***********************************************************
// This example support/e2e.js is processed and
// loaded automatically before your test files.
//
// This is a great place to put global configuration and
// behavior that modifies Cypress.
//
// You can change the location of this file or turn off
// automatically serving support files with the
// 'supportFile' configuration option.
//
// You can read more here:
// https://on.cypress.io/configuration
// ***********************************************************

// Import commands.js using ES2015 syntax:
import './commands'

// Alternatively you can use CommonJS syntax:
// require('./commands')



Cypress.Commands.add('switchCountry', (country) => {
    cy.url().then((url)=>{
        if(!url.endsWith("checkout/confirm")){
            cy.get('#billingAddressAddressCountry').select(country)
        }else{
            cy.get('#billing-address-create-new #billing-addressAddressCountry').select(country)
        }
    })
})

Cypress.Commands.add('checkOutProduct', () => {
    cy.visit('/')
    cy.contains('Add to shopping cart').click()
    cy.get('.begin-checkout-btn').click()
})

Cypress.Commands.add('fillAndSubmitCheckoutForm', () => {
    cy.fixture('user').then((user)=>{
        cy.get('#personalSalutation').select(1);
        cy.get('#personalFirstName').type(user.firstName);
        cy.get('#personalLastName').type(user.lastName);
        cy.get('#personalGuest').check({force: true});
        cy.get('#personalMail').type(user.email);
        cy.switchCountry('Netherlands')
        cy.get('[id^=billingAddressPostNLAddressZipcode-]').type(user.zipcode);
        cy.get('[id^=billingAddressPostNLAddressHouseNumber-]').type(user.housenumber);

        cy.intercept('POST', '/widget/address/postnl/postalcode-check').as('postalcode-check')
        cy.wait('@postalcode-check').its('response.statusCode').should('eq', 200)

        cy.get('.register-form').submit()
    })
})


