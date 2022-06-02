import { buildSearchParams } from '../../libs/urlUtils.js';

/**
 * Default pagination object
 */
export const DEFAULT_PAGINATION = {
    count: 0,
    page: 1,
    page_size: 20,
    page_count: 1,
};

/**
 * Default filter object
 */
export const getDefaultFilter = () => ({
    q: '',
    filter: {
        type: '',
    }
});

/**
 * Mixins: PaginatedContentMixin
 */
export const PaginatedContentMixin = {
    data() {
        return {
            requestsQueue: [], // array of queued fetch requests
            requestController: new AbortController(), // AbortController instance

            objects: [],
            endpoint: null,

            pagination: DEFAULT_PAGINATION,
            query: {},
            formatObjectsFilter: ['params', 'priority', 'position', 'url'],
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

                // if requestQueue is populated then abort all fetch request and start over
                if (this.requestsQueue.length > 0) {
                    this.requestController.abort();
                    this.requestController = new AbortController();
                }

                options.signal = this.requestController.signal;

                let currentRequest = fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        this.requestsQueue.pop();

                        let objects = [];
                        if (json && 'data' in json) {
                            objects = (Array.isArray(json.data) ? json.data : [json.data]) || [];
                        }

                        // if requestQueue is empty it means that this request is the last of the queue
                        // therefore it can load objects and pagination
                        if (this.requestsQueue.length < 1) {
                            if (autoload) {
                                this.objects = objects;
                            }
                            this.pagination = json && json.meta && json.meta.pagination || this.pagination;

                            return objects;
                        }

                        return false;
                    })
                    .catch((error) => {
                        this.requestsQueue.pop();
                        // code 20 is aborted fetch by user which needs to be passed down the promise road
                        if (error.code === 20) {
                            throw error;
                        } else {
                            console.error(error);
                        }
                    });

                this.requestsQueue.push(currentRequest);

                return currentRequest;
            } else {
                return Promise.reject();
            }
        },

        /**
         * format objects for api calls
         *
         * @param {Array} objects
         *
         * @returns {Array} of formatted objects
         */
        formatObjects(objects) {
            if (objects === undefined) {
                return [];
            }
            const formattedObjects = [];

            objects.forEach((obj) => {
                let formattedObj = {};
                // keep id and type
                formattedObj.id = obj.id;
                formattedObj.type = obj.type;

                // search for meta.relation using this.formatObjectsFilter
                const metaRelation = obj.meta.relation;
                if (metaRelation) {
                    let formattedMeta = {};
                    this.formatObjectsFilter.forEach((filter) => {
                        if (metaRelation[filter]) {
                            formattedMeta[filter] = metaRelation[filter];
                        }
                    });

                    if (Object.keys(formattedMeta).length) {
                        formattedObj.meta = { relation: formattedMeta };
                    }
                }
                formattedObjects.push(formattedObj);
            });

            return formattedObjects;
        },

        /**
         * Get formatted url with query filter
         *
         * @param {String} url The endpoint url
         * @return {String} The formatted url
         */
        getUrlWithPaginationAndQuery(url) {
            const parsed = new URL(url);
            parsed.search = buildSearchParams({ ...this.pagination, ...this.query }, parsed.searchParams).toString();

            return parsed.href;
        },

        /**
         * append more objects to current array of objects
         *
         * @param {Number} qty number of elements to load
         *
         * @return {Promise} repsonse from server
         */
        async loadMore(qty = DEFAULT_PAGINATION.page_size) {
            if (this.pagination.page_items >= this.pagination.count) {
                return null;
            }
            let moreObjects = await this.nextPage(false);
            this.pagination.page_items = this.pagination.page_items + qty <= this.pagination.count ? this.pagination.page_items + qty : this.pagination.count;
            // this.pagination.page--;

            const last = this.objects.length;
            this.objects.splice(last, 0, ...moreObjects);
            return moreObjects;
        },

        /**
         * Load page by page number and query string
         *
         * @param {Number} page The page number
         * @param {Object} query The query object (i.e. { q: 'search me' })
         */
        toPage(page, query = {}) {
            this.pagination.page = page || 1;
            return this.getPaginatedObjects(true, query);
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
