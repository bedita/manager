/**
 * Mixins: PaginatedContentMixin
 *
 *
 */

export const DEFAULT_PAGINATION = {
    count: 1,
    page: 1,
    page_size: 20,
    page_count: 1,
}

export const PaginatedContentMixin = {
    data() {
        return {
            objects: [],
            endpoint: null,

            pagination: DEFAULT_PAGINATION,
            query: {},
        }
    },

    methods: {
        /**
         * fetch paginated objects based on this.endpoint value
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         * @param {Object} query The query filter object
         *
         * @return {Promise} repsonse from server
         */
        getPaginatedObjects(autoload = true, query = {}) {
            let baseUrl = window.location.href;

            if (this.endpoint) {
                if (query) {
                    this.query = query;
                }
                let requestUrl = `${baseUrl}/${this.endpoint}`;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                };

                requestUrl = this.getUrlWithPaginationAndQuery(requestUrl);

                return fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        let objects = (Array.isArray(json.data) ? json.data : [json.data]) || [];
                        if (!json.data) {
                            // api response with error
                            objects = [];
                        }

                        if (autoload) {
                            this.objects = objects;
                        }
                        this.pagination = json.meta && json.meta.pagination || this.pagination;

                        return objects;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                return Promise.reject();
            }
        },

        /**
         * Add pagination info to endpoint url
         *
         * @param {String} url
         *
         * @return {String} formatted url
         */
        setPagination(url) {
            let pagination = '';
            let qi = '?';
            const separator = '&';

            Object.keys(this.pagination).forEach((key, index) => {
                pagination += `${index ? separator : ''}${key}=${this.pagination[key]}`;
            });

            let hasQueryIdentifier = url.indexOf(qi) === -1;
            if (!hasQueryIdentifier) {
                qi = '&';
            }
            return `${url}${qi}${pagination}`;
        },

        /**
         * Get formatted url with query filter
         *
         * @param {String} url The endpoint url
         * @return {String} The formatted url
         */
        getUrlWithPaginationAndQuery(url) {
            let queryString = '';
            let qi = '?';
            const separator = '&';

            Object.keys(this.pagination).forEach((key, index) => {
                queryString += `${index ? separator : ''}${key}=${this.pagination[key]}`;
            });
            if (queryString.length > 1) {
                queryString += separator;
            }
            Object.keys(this.query).forEach((key, index) => {
                queryString += `${index ? separator : ''}${key}=${this.query[key]}`;
            });

            let hasQueryIdentifier = url.indexOf(qi) === -1;
            if (!hasQueryIdentifier) {
                qi = '&';
            }
            return `${url}${qi}${queryString}`;
        },

        /**
         * find object with specific id
         *
         * @param {Number} id
         *
         * @return {Object}
         */
        findObjectById(id) {
            let obj = this.objects.filter(o => o.id === id);
            return obj.length && obj[0];
        },

        /**
         * append more objects to current array of objects
         *
         * @param {Number} qty number of elements to load
         *
         * @return {Promise} repsonse from server
         */
        async loadMore(qty = DEFAULT_PAGINATION.page_size) {
            if (this.pagination.page_items < this.pagination.count) {
                let moreObjects = await this.nextPage(false);
                this.pagination.page_items = this.pagination.page_items + qty <= this.pagination.count ? this.pagination.page_items + qty : this.pagination.count;
                // this.pagination.page--;

                const last = this.objects.length;
                this.objects.splice(last, 0, ...moreObjects);
            }
        },


        toPage(i) {
            this.pagination.page = i || 1;
            return this.getPaginatedObjects(true);
        },

        /**
         * load first page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        firstPage(autoload = true) {
            if (this.pagination.page !== 1) {
                this.pagination.page = 1;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load last page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        lastPage(autoload = true) {
            if (this.pagination.page !== this.pagination.page_count) {
                this.pagination.page = this.pagination.page_count;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load next page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        nextPage(autoload = true) {
            if (this.pagination.page < this.pagination.page_count) {
                this.pagination.page = this.pagination.page + 1;

                return this.getPaginatedObjects(autoload);
            }

            return Promise.resolve([]);
        },

        /**
         * load previous page of content
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server with new data
         */
        prevPage() {
            if (this.pagination.page > 1) {
                this.pagination.page = this.pagination.page - 1;

                return this.getPaginatedObjects();
            }

            return Promise.resolve();
        },

        /**
         * set Pagination page size
         *
         * @param {Number} size
         *
         * @return {void}
         */
        setPageSize(size) {
            this.pagination.page_size = size;
            this.pagination.page = 1;
        },
    }
}
