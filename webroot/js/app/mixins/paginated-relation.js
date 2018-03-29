let PaginatedRelationMixin = {
    data() {
        return {
            objects: [],
            method: '/relatedJson/',
            pagination: {},
        }
    },

    methods: {
        getRelatedObjects() {
            let baseUrl = window.location.href;

            if (this.relationName) {
                const requestUrl = baseUrl + this.method + this.relationName;
                const options =  {
                    credentials: 'same-origin',
                    headers: {
                        'accept': 'application/json',
                    }
                }

                fetch(requestUrl, options)
                    .then((response) => response.json())
                    .then((json) => {
                        console.log(json);

                        this.objects = json.data;
                    }
                );
            }
        }
    }
}

;(function(root) {
    root.PaginatedRelationMixin = PaginatedRelationMixin;
}(this));
