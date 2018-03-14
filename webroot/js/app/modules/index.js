

/**
 * Templates that uses this component (directly or indirectly)
 *
 * Template/Modules/index.twig
 * - Element/Toolbar/filter.twig
 * - Element/Toolbar/pagination.twig
 *
 */

Vue.component('modules-index', {

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            searchQuery: '',
            pageSize: '100',
            page: '',
            sort: '',
        };
    },

    created() {
        // load url params when component initialized
        this.loadUrlParams();
    },

    /**
     * component methods
     */
    methods: {

        /**
         * extract params from page url
         *
         * @returns {void}
         */
        loadUrlParams() {
            // look for query string params in window url
            if (window.location.search) {
                let urlParams = window.location.search;

                // search for q='some string' both after ? and & tokens
                let queryStringExp = /[?&]q=([^&#]*)/g;
                let matches = urlParams.match(queryStringExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(queryStringExp, '$1'));
                    this.searchQuery = matches[0];
                }

                // search for page_size='some string' both after ? and & tokens
                let pageSizeExp = /[?&]page_size=([^&#]*)/g;
                matches = urlParams.match(pageSizeExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageSizeExp, '$1'));
                    this.pageSize = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for page='some string' both after ? and & tokens
                let pageExp = /[?&]page=([^&#]*)/g;
                matches = urlParams.match(pageExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageExp, '$1'));
                    this.page = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for sort='some string' both after ? and & tokens
                let sortExp = /[?&]sort=([^&#]*)/g;
                matches = urlParams.match(sortExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(sortExp, '$1'));
                    this.sort = matches[0];
                }
            }
        },


        /**
         * build coherent url based on these params:
         * - q= query string
         * - page_size
         *
         * @param {Object} params
         * @returns {String} url
         */
        buildUrlParams(params) {
            let url = `${window.location.origin}${window.location.pathname}`;
            let first = true;

            Object.keys(params).forEach((key) =>  {
                if (params[key] && params[key] !== '') {
                    url += `${first ? '?' : '&'}${key}=${params[key]}`;
                    first = false;
                }
            });

            return url;
        },


        /**
         * update pagination keeping searched string
         *
         * @returns {void}
         */
        updatePagination() {
            this.page = '';
            this.applyFilters();
        },

        /**
         * search queryString keeping pagination options
         *
         * @returns {void}
         */
        search() {
            this.page = '';
            this.applyFilters();
        },


        /**
         * reset queryString in search keeping pagination options
         *
         * @returns {void}
         */
        resetResearch() {
            this.searchQuery = '';
            this.applyFilters();
        },

        /**
         * apply page filters such as query string or pagination
         *
         * @returns {void}
         */
        applyFilters() {
            let url = this.buildUrlParams({
                q: this.searchQuery,
                page_size: this.pageSize,
                page: this.page,
                sort: this.sort,
            });
            window.location.replace(url);
        },

        /**
         * Helper function
         *
         * @param {String|Number} num
         * @returns {Boolean}
         */
        isNumeric(num){
            return !isNaN(num)
        }
    }
});


