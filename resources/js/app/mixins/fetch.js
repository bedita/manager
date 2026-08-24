/**
 * Fetch mixin used to comunicate with cake controller's method
 *
 * TO-DO: handle token timeout
 *
 * @requires BEDITA global object
 */

import axios from 'axios';

// private var to prevent tempering
let privateAxios;

export const FetchMixin = {
    created() {
        privateAxios = this.createCustomAxios();
    },

    computed: {
        axios() {
            return privateAxios;
        }
    },

    methods: {
        /**
        * set up axios api
        *
        * @return {Object} custom axios instance
        */
        createCustomAxios() {
            return axios.create({
                baseURL: BEDITA.base,
                timeout: BEDITA?.uploadConfig?.timeout || 30000,
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                    'content-type': 'multipart/form-data',
                    'X-CSRF-Token': this.getCSFRToken(),
                },

                validateStatus: (status) => {
                    return status >= 200 && status < 300; // default
                },
            });
        },

        /**
         * get axios Factory
         */
        getAxios() {
            return axios;
        },
    }
}
