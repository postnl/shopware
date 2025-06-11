export default {
    id: 'postnlLocaleLanguage',

    state: () => ({
        locales: {},
    }),

    actions: {
        addLanguage(locale, languageId ) {
            if (locale in this.locales) {
                return;
            }

            this.locales[locale] = languageId;
        },
    },
};
