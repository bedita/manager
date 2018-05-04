/**
 * Mixins: PaginatedContentMixin
 *
 *
 */

const DEFAULT_PAGINATION = {
    count: 1,
    page: 1,
    page_size: 20,
    page_count: 1,
}

let PaginatedContentMixin = {
    data() {
        return {
            objects: [],
            endpoint: null,

            pagination: DEFAULT_PAGINATION,
        }
    },

    methods: {
        /**
         * fetch paginated objects based on this.endpoint value
         *
         * @param {Boolean} autoload if false it doesn't update this.objects [DEFAULT = true]
         *
         * @return {Promise} repsonse from server
         */
        getPaginatedObjects(autoload = true) {
            let baseUrl = window.location.href;

            if (autoload) {
                this.objects = [];
            }

            if (this.endpoint) {
                let requestUrl = `${baseUrl}/${this.endpoint}`;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                }

                requestUrl = this.setPagination(requestUrl);

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

;(function(root) {
    root.PaginatedContentMixin = PaginatedContentMixin;
}(this));
