import AdminAPIClient from "Services/shopware/AdminAPIClient";
import Shopware from "Services/shopware/Shopware"

const shopware = new Shopware();

export default class ShopConfigurationAction {

    /**
     *
     */
    constructor() {
        this.apiClient = new AdminAPIClient();
    }


    setupShop() {

        // this._prepareShippingMethods();

        this.setupPlugin();

    }


    setupPlugin() {

        cy.wait(2000);
        this._configurePostNLPlugin(null);
        this._createTestUser();
        // assign all payment methods to
        // all available sales channels
        // this.apiClient.get('/sales-channel').then(channels => {
        //
        //     if (channels === undefined || channels === null) {
        //         throw new Error('Attention, No Sales Channels found trough Shopware API');
        //     }
        //
        //     channels.forEach(channel => {
        //         // this._configureSalesChannel(channel.id);
        //         this._configurePostNLPlugin(channel.id);
        //     });
        // });
    }

    /**
     *
     * @param channelId
     * @private
     */
    _configurePostNLPlugin(channelId) {
        const data = {};

        data[channelId] = {
            "PostNLShopware.config.apiMode": "sandbox",
            "PostNLShopware.config.debugMode": true,
        };

        this.apiClient.post('/_action/system-config/batch', data);
    }


    /**
     * Make sure no availability rules are set
     * that could block our shipping method from being used.
     * Also add some shipping costs for better tests.
     * @private
     */
    _prepareShippingMethods() {
        this.apiClient.get('/rule').then(rules => {

            if (rules === undefined || rules === null) {
                rules = [];
            }

            rules.forEach(rule => {

                // get the all customers rule
                // so we allow our shipping methods to be used by everybody
                if (rule.attributes.name === 'All customers') {

                    this.apiClient.get('/shipping-method').then(shippingMethods => {

                        if (shippingMethods === undefined || shippingMethods === null) {
                            throw new Error('Attention, No shippingMethods trough Shopware API');
                        }

                        shippingMethods.forEach(element => {

                            this.apiClient.get('/shipping-method/' + element.id + '/prices').then(price => {

                                const shippingData = {
                                    "id": element.id,
                                    "active": true,
                                    "availabilityRuleId": rule.id,
                                    "prices": [
                                        {
                                            "id": price.id,
                                            "currencyPrice": [
                                                {
                                                    "currencyId": price.attributes.currencyPrice[0].currencyId,
                                                    "net": 4.19,
                                                    "gross": 4.99,
                                                    "linked": false
                                                }
                                            ]
                                        }
                                    ]
                                };

                                this.apiClient.patch('/shipping-method/' + element.id, shippingData);
                            });
                        });
                    });
                }
            });
        });
    }

    /**
     *
     * @param id
     * @private
     */
    _configureSalesChannel(id) {
        this.apiClient.get('/payment-method').then(payments => {

            if (payments === undefined || payments === null) {
                throw new Error('Attention, No payments trough Shopware API');
            }

            let paymentMethodsIds = [];

            payments.forEach(element => {
                paymentMethodsIds.push({
                    "id": element.id
                });
            });

            const data = {
                "id": id,
                "paymentMethods": paymentMethodsIds
            };

            this.apiClient.patch('/sales-channel/' + id, data);
        });
    }

    /**
     *
     * @returns {*}
     */
    _clearCache() {
        return this.apiClient.delete('/_action/cache').catch((err) => {
            console.log('Cache could not be cleared')
        });
    }

}
