import stateModules from './../state';

initModules(stateModules, Shopware.State);

function initModules(modules, state) {
    Object.keys(modules).forEach((storeModule) => {
        state.registerModule(storeModule, modules[storeModule]);
    });
}
