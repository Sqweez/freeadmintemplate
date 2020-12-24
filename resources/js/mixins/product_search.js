import _ from "lodash";

export default {
    data: () => ({
        searchValue: '',
        searchQuery: '',
        awaitingSearch: false,
    }),
    methods: {
        async searchInput(value) {
            if (!this.awaitingSearch) {
                const searchDebouncedFn = _.debounce(() => {
                    if (this.searchValue && this.searchValue.length >= 3) {
                        this.searchQuery = this.searchValue;
                    } else {
                        this.searchQuery = "";
                    }
                    this.awaitingSearch = false;
                }, 500);

                searchDebouncedFn();
            }
            this.awaitingSearch = true;
        }
    }
}
