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
         * fetch objects based on this.endpoint value
         *
         * TO-DO pagination
         *
         * @return {Promise}
         */
        getPaginatedObjects(avoidReloading = false) {
            let baseUrl = window.location.href;

            this.objects = [];

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
                        this.objects = json.data || [];
                        this.pagination = json.meta.pagination;
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                return Promise.reject();
            }
        },

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

        loadMore(qty = DEFAULT_PAGINATION.page_size) {
            if (this.pagination.page_size + qty <= this.pagination.count ) {
                this.pagination.page_size += qty;
            } else {
                this.pagination.page_size = this.pagination.count;
            }

            this.getPaginatedObjects(true);
        },

        nextPage() {
            if (this.pagination.page < this.pagination.page_count) {
                this.pagination.page = this.pagination.page + 1;

                this.getPaginatedObjects();
            }
        },

        prevPage() {
            if (this.pagination.page > 1) {
                this.pagination.page = this.pagination.page - 1;

                this.getPaginatedObjects();
            }
        },
    }
}

;(function(root) {
    root.PaginatedContentMixin = PaginatedContentMixin;
}(this));
