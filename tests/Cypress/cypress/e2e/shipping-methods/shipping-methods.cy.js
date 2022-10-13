import AdminLoginAction from "Actions/admin/AdminLoginAction";
import AdminPluginAction from "Actions/admin/AdminPluginAction";
import ShopConfigurationAction from "Actions/admin/ShopConfigurationAction";
import RegisterAction from "Actions/storefront/account/RegisterAction";
import CheckoutAction from "Actions/storefront/checkout/CheckoutAction";
import DummyBasketScenario from "Scenarios/DummyBasketScenario";

const adminLogin = new AdminLoginAction();
const pluginAction = new AdminPluginAction();
const configAction = new ShopConfigurationAction();
const register = new RegisterAction();
const checkoutAction = new CheckoutAction();

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
