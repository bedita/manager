/**
 * Mixins: PaginatedContentMixin
 *
 *
 */

let PaginatedContentMixin = {
    data() {
        return {
            objects: [],
            endpoint: null,

            pagination: {
                page: 1,
                page_size: 20,
            },
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
        getPaginatedObjects() {
            let baseUrl = window.location.href;
            this.objects = [];

            if (this.endpoint) {
                const requestUrl = `${baseUrl}/${this.endpoint}`;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                }

                return fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        this.objects = json.data || [];
                    })
                    .catch((error) => {
                        console.error(error);
                    });
            } else {
                return Promise.reject();
            }
        },
    }
}

;(function(root) {
    root.PaginatedContentMixin = PaginatedContentMixin;
}(this));
