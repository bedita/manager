Vue.use(VueHotkey);

window._vueInstance = new Vue({
    el: 'main',

    data() {
        return {
            vueLoaded: false,
            urlPagination: '',
            searchQuery: '',
            pageSize: '100',
            page: '',
            sort: '',
            panelIsOpen: false,
            addRelation: {},
        }
    },

    created() {
        this.vueLoaded = true;

        // load url params when component initialized
        this.loadUrlParams();
    },

    methods: {
        // panel
        onRequestPanelToggle(evt) {
            this.panelIsOpen = !this.panelIsOpen;

            // return data from panel
            if(evt.returnData) {
                if(evt.returnData.relationName){
                    this.$refs["moduleView"]
                        .$refs[evt.returnData.relationName]
                        .$refs["relation"].appendRelations(evt.returnData.objects);
                }
            }

            // open panel for relations add
            if(this.panelIsOpen && evt.relation && evt.relation.name) {
                this.addRelation = evt.relation;
            } else {
                sleep(500).then(() => this.addRelation = {}); // 500ms is the panel transition duration
            }
        },


        /**
         * extract params from page url
         *
         * @returns {void}
         */
        loadUrlParams() {
            // look for query string params in window url
            if (window.location.search) {
                const urlParams = window.location.search;

                // search for q='some string' both after ? and & tokens
                const queryStringExp = /[?&]q=([^&#]*)/g;
                let matches = urlParams.match(queryStringExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(queryStringExp, '$1'));
                    this.searchQuery = matches[0];
                }

                // search for page_size='some string' both after ? and & tokens
                const pageSizeExp = /[?&]page_size=([^&#]*)/g;
                matches = urlParams.match(pageSizeExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageSizeExp, '$1'));
                    this.pageSize = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for page='some string' both after ? and & tokens
                const pageExp = /[?&]page=([^&#]*)/g;
                matches = urlParams.match(pageExp);
                if (matches && matches.length) {
                    matches = matches.map(e => e.replace(pageExp, '$1'));
                    this.page = this.isNumeric(matches[0]) ? matches[0] : '';
                }

                // search for sort='some string' both after ? and & tokens
                const sortExp = /[?&]sort=([^&#]*)/g;
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
            window.location.replace(this.urlPagination);
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
        isNumeric(num) {
            return !isNaN(num);
        },
    }
});

// helper functions
async function sleep(t) {
    return new Promise(resolve => setTimeout(resolve, t));
}

function humanize(s) {
    if(!s) {
        return '';
    }

    // decamelize (credits: https://github.com/sindresorhus/decamelize)
    var separator = '_';
    var regex1 = XRegExp('([\\p{Ll}\\d])(\\p{Lu})', 'g');
    var regex2 = XRegExp('(\\p{Lu}+)(\\p{Lu}[\\p{Ll}\\d]+)', 'g');
    s = s.replace(regex1, `$1${separator}$2`)
    .replace(regex2, `$1${separator}$2`)
    .toLowerCase();

    // humanize (credits: https://github.com/sindresorhus/decamelize)
    s = s.toLowerCase().replace(/[_-]+/g, ' ').replace(/\s{2,}/g, ' ').trim();
    s = s.charAt(0).toUpperCase() + s.slice(1);
    return s;
}

