export default {
    namespaced: true,

    state: {
        locales: {},
    },

    mutations: {
        addLanguage(state, { locale, languageId }) {
            if (state.locales.hasOwnProperty(locale)) {
                return;
            }

            state.locales[locale] = languageId;
        },
    },
};
