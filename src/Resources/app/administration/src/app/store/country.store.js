const { Application, Context, Data: { Criteria }} = Shopware;

export default {
    id: 'postnlCountryCache',

    state: () => ({
        countries: [],
    }),

    actions: {
        getCountryByIso(iso) {
            return this.countries.find(country => country.iso === iso)
        },

        getCountryById(id) {
            return this.countries.find(country => country.id === id)
        },

        load() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equalsAny('iso', ['NL', 'BE']));

            return Application
                .getContainer('service')
                .repositoryFactory
                .create('country')
                .search(criteria, Context.api)
                .then(result => this.countries = result)
        }
    },
};
