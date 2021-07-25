export default class LoadingService {
    constructor(store) {
        this.store = store;
    }

    enable() {
        this.store.commit('enableLoading');
    }

    disable() {
        this.store.commit('disableLoading');
    }

    on() {
        this.enable();
    }

    off() {
        this.disable();
    }
}
