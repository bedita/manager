/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

import PropertyView from 'app/components/property-view/property-view';
import RelationView from 'app/components/relation-view/relation-view';

export default {
    components: {
        PropertyView,
    },

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,
        };
    },

    computed: {
        keyEvents() {
            return {
                'esc': {
                    keyup: this.toggleTabs,
                },
            }
        }
    },

    methods: {
        toggleTabs() {
            return this.tabsOpen = !this.tabsOpen;
        },

        /**
         * Force download using a syntetic element
         *
         * @param {*} blob
         * @param {*} filename
         */
        forceDownload(blob, filename) {
            let a = document.createElement('a');
            a.download = filename;
            a.href = blob;
            a.click();
        },

        /**
         * download a resource as a blob to avoid cors restrictions
         *
         * @param {string} url
         * @param {string} filename
         */
        downloadResource(url, filename) {
            if (!filename) {
                filename = url.split('\\').pop().split('/').pop();
            }

            const options = {
                headers: new Headers({
                    'Origin': location.origin
                }),
                mode: 'cors'
            }

            fetch(url, options)
                .then(response => response.blob())
                .then(blob => {
                    let blobUrl = window.URL.createObjectURL(blob);
                    this.forceDownload(blobUrl, filename);
                })
                .catch(e => console.error(e));
        }
    }
}
