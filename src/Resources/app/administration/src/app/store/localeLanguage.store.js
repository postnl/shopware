const { Application, Context, Data: { Criteria }} = Shopware;

export default {
    id: 'postnlLocaleLanguage',

    state: () => ({
        locales: {},
        loadPromise: null,
    }),

    actions: {
        addLanguage(locale, languageId ) {
            if (locale in this.locales) {
                return;
            }

            this.locales[locale] = languageId;
        },

        loadLastKnownLanguage() {
            const locale = Application.getContainer('factory').locale.getLastKnownLocale();

            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals('locale.code', locale));

            return this.loadPromise = Application
                .getContainer('service')
                .repositoryFactory
                .create('language')
                .searchIds(criteria, Context.api)
                .then(result => {
                    const languageId = result.data[0];
                    this.addLanguage(locale, languageId);
                    return languageId;
                })
        }
    },
};
