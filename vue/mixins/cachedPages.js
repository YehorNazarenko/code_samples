export default {
    data() {
        return {
            pagesData: {}
        }
    },
    beforeMount() {
        const fetchUrl = this.getFetchUrl();
        this.pagesData[fetchUrl] = this.itemsData;
    },
    methods: {
        getCachedPageData(fetchUrl) {
            const {pathname, search} = window.location;

            if (!fetchUrl || (pathname + search) === fetchUrl) {
                return null;
            }

            if (this.pagesData[fetchUrl]) {
                window.history.pushState(null,'',fetchUrl);
                return this.pagesData[fetchUrl];
            }

            this.$inertia.get(fetchUrl, {}, {
                preserveState: true,
                replace: true
            });

            return null;
        }
    }
}
