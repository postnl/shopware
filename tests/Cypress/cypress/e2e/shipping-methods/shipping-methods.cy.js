import DummyBasketScenario from "Scenarios/DummyBasketScenario";

const scenarioDummyBasket = new DummyBasketScenario(1);

describe('Shipping Methods', () => {

    beforeEach(() => {
        scenarioDummyBasket.execute();
    });

    it('Has both options', () => {
        cy.get('.shipping-method').should('have.length', 2);
    })

    it('Has options for pickup point ', () => {
        cy.contains('Pickup at PostNL point').click()
        cy.get('.postnl-shipping-method__pickup').children().should('have.length', 5).should('be.visible');
        cy.get('#confirmFormSubmit').click()
    })

    it('Should have pickup point in the order ', () => {
        cy.intercept('POST', '/checkout/configure').as('pickup-point')

        cy.get('input.checkout-confirm-tos-checkbox').click({force: true})

        cy.contains('Pickup at PostNL point').click()

        cy.wait('@pickup-point').its('response.statusCode').should('eq', 302)
        cy.get('#confirmFormSubmit').click()
        cy.contains('Shipping method: Pickup at PostNL point').should('be.visible')
    })

    afterEach(()=>{
        scenarioDummyBasket.destroy();
    })
})
