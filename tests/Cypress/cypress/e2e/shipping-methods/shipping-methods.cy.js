import DummyBasketScenario from "Scenarios/DummyBasketScenario";

const scenarioDummyBasket = new DummyBasketScenario(1);

describe('Shipping Methods', () => {

    beforeEach(() => {
        scenarioDummyBasket.execute();
    });

    it('Has both options', () => {
        cy.get('.shipping-method').should('have.length', 2);
    })

    it.only('Has options for pickup point ', () => {
        cy.contains('PostNL Pickup point').click()
        cy.get('.postnl-shipping-method__pickup').children().should('have.length', 5).should('be.visible');
    })

    afterEach(()=>{
        scenarioDummyBasket.destroy();
    })
})
