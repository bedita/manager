/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 * @prop {String} relationName relation name
 * @prop {Array} alreadyInView array of objects already added
 * @prop {Object} relationTypes list of available object types
 * @prop {String} configPaginateSizes list of sizes for pagination
 *
 */

import { PaginatedContentMixin, DEFAULT_PAGINATION } from 'app/mixins/paginated-content';
import { PanelEvents } from 'app/components/panel-view';
import { DragdropMixin } from 'app/mixins/dragdrop';
import sleep from 'sleep-promise';

export default {
    mixins: [ PaginatedContentMixin, DragdropMixin ],

    inject: ['getCSFRToken'],

    components: {
        FilterBoxView: () => import(/* webpackChunkName: "filter-box-view" */'app/components/filter-box'),
    },

    props: {
        relationName: {
            type: String,
            default: '',
        },
        alreadyInView: {
            type: Array,
            default: () => [],
        },
        relationTypes: {
            type: Object,
        },
        configPaginateSizes: {
            type: String,
            default: '[]',
        },
    },

    data() {
        return {
            method: 'relationshipsJson',
            endpoint: '',
            loading: false,
            selectedObjects: [],
            addedObjects: [],
            activeFilter: {},

            // create object form data
            saving: false,
            showCreateObjectForm: false,
            file: null,
            url: null,
            objectType: '',
            titlePlaceholder: '',

            // handle tabs
            activeIndex: 0,
        };
    },

    computed: {
        knownTypes() {
            return ['audio', 'video', 'image'];
        },

        /**
         * is a media object
         *
         * @return {Boolean}
         */
        isMedia() {
            // predefined relations like `children` don't have relationTyps
            if (!this.relationTypes) {
                return true;
            }
            const right = this.relationTypes.right || [];
            // actual types should be read from API
            const mediaTypes = ['media', 'audio', 'files', 'images', 'videos'];
            const intersection = right.filter(x => mediaTypes.includes(x));

            return (intersection.length > 0);
        },
    },

    mounted() {
        // when object is staged for saving in relation view updates the list of alreadyInView
        PanelEvents.listen('relations-view:add-already-in-view', null, this.addToAlreadyInView);
        PanelEvents.listen('relations-view:remove-already-in-view', null, this.removeFromAlreadyInView);
    },

    destroyed() {
        PanelEvents.stop('relations-view:update-already-in-view', null, this.addToAlreadyInView);
        PanelEvents.stop('relations-view:remove-already-in-view', null, this.removeFromAlreadyInView);
    },

    watch: {
        relationName: {
            immediate: true,
            handler(newVal, oldVal) {
                if (newVal) {
                    this.selectedObjects = [];
                    this.endpoint = `${this.method}/${newVal}`;
                    this.loadObjects();
                }
                // clear objects when relationName is empty (panel closed)
                if (newVal === '') {
                    sleep(500).then(() => this.objects = []);
                }
            },
        },

        /**
         * Loading event emit
         *
         * @param {String} value The value associated to loading
         *
         * @emits Event#loading
         *
         * @return {void}
         */
        loading(value) {
            this.$emit('loading', value);
        },
    },

    methods: {
        // Events Listeners

        /**
         * listen to FilterBoxView event filter-objects
         *
         * @param {Object} filter
         *
         * @return {void}
         */
        onFilterObjects(filter) {
            this.activeFilter = filter;
            this.toPage(1, this.activeFilter);
        },

        /**
         * listen to FilterBoxView event filter-update-page-size
         *
         * @param {Number} pageSize
         *
         * @return {void}
         */
        onUpdatePageSize(pageSize) {
            this.setPageSize(pageSize);
            this.loadObjects(this.activeFilter);
        },

        /**
         * listen to FilterBoxView event filter-update-current-page
         *
         * @param {Number} page
         *
         * @return {void}
         */
        onUpdateCurrentPage(page) {
            this.toPage(page, this.activeFilter);
        },

        /**
         * send selected objects to relation view
         *
         * @return {void}
         */
        addRelationsToObject() {
            PanelEvents.sendBack('relations-add:save', this.selectedObjects);
        },

        /**
         * close panel
         *
         * @return {void}
         */
        closePanel() {
            PanelEvents.closePanel();
        },

        /**
         * create new object
         *
         * @param {HTMLElement} target form (Destructuring target from Event Object)
         *
         * @return {void}
         */
        async createObject({ target }) {
            if (!target) {
                // form element might be tempered
                return;
            }

            this.saving = true;
            this.loading = true;

            const formData = new FormData(target);

            // use file name if title is not provided
            if (formData.get('title') === '') {
                formData.set('title', this.titlePlaceholder);
            }

            formData.append('model-type', this.objectType);

            // save object
            const baseUrl = window.location.origin;
            const postUrl = `${baseUrl}/${this.objectType}/saveJson`;

            const options = {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'accept': 'application/json',
                    'X-CSRF-Token': this.getCSFRToken(),
                },
                body: formData,
            };

            try {
                const response = await fetch(postUrl, options);
                const responseJson = await response.json();

                let createdObjects = (Array.isArray(responseJson.data) ? responseJson.data : [responseJson.data]) || [];

                // add newly added object to list / selected
                this.objects = createdObjects.concat(this.objects);
                this.selectedObjects = createdObjects.concat(this.selectedObjects);
                this.resetForm(target);
            } catch (error) {
                // need a modal to better handling of errors
                if (error.code === 20) {
                    throw error;
                } else {
                    alert('Error while uploading/creating new object. Retry');
                    console.error(error);
                }
                this.resetForm(target, true);
            } finally {
                this.saving = false;
                this.loading = false;
            }
        },

        /**
         * check if object is not selectable
         *
         * @param {Number} id
         *
         * @returns {Boolean}
         */
        isUnselectableObject(id) {
            const addedIds = this.addedObjects.map(obj => obj.id);
            this.$attrs && this.$attrs.object && addedIds.push(this.$attrs.object.id);
            return this.alreadyInView.concat(addedIds).indexOf(id) !== -1;
        },

        /**
         * update objects already added
         *
         * @param {Object} object
         *
         * @returns {void}
         */
        addToAlreadyInView(object) {
            this.addedObjects.push(object);
        },

        /**
         * remove object from addedObjects list
         *
         * @returns {void}
         */
        removeFromAlreadyInView(object) {
            this.addedObjects = this.addedObjects.filter((added) => added.id !== object.id);
        },

        // css class on items
        selectClasses(related) {
            return [
                `from-relation-${this.relationName}`,
                {
                    selected: this.selectedObjects.indexOf(related) !== -1,
                    unselectable: this.isUnselectableObject(related.id),
                }
            ]
        },

        /**
         * clear form
         *
         * @param {HTMLElement} form create new object form
         * @param {Boolean} showForm keep showing form (default = false)
         *
         * @return {void}
         */
        resetForm(form, showForm = false) {
            form.reset();
            this.titlePlaceholder = 'title';
            this.showCreateObjectForm = showForm;
        },

        /**
         * set file, object type and placeholder
         *
         * @param {HTMLElement} target input file element
         *
         * @return {Boolean} file process success
         */
        processFile({ target }) {
            this.file = target.files[0];
            if (!this.file) {
                return false;
            }

            this.objectType = this.getObjectType(this.file);
            this.titlePlaceholder = this.file && this.file.name;

            return true;
        },

        /**
         * set object type for url upload
         *
         * @param {Event} event The event
         *
         * @return {void}
         */
        processUrl(event) {
            if (event.target.value.length > 0) {
                this.url = event.target.value;
            } else {
                this.url = null;
            }
            this.objectType = 'videos';
        },

        /**
         * get BEobject type from file's mimetype
         *
         * @param {File} file
         *
         * @return {String} object type
         */
        getObjectType(file) {
            let type = file.type && file.type.split('/')[0];
            const hasPlural = /audio/g.test(type) ? '' : 's';

            if (this.knownTypes.indexOf(type) === -1) {
                type = 'file';
            }
            return `${type}${hasPlural}`;
        },

        /**
         * Verify if right relation types are fine for url embed.
         * Now only allowed type is 'videos' (and 'media')
         *
         * @return {Boolean}
         */
        isEmbeddable() {
            // predefined relations like `children` don't have relationTypes
            if (!this.relationTypes) {
                return true;
            }
            const right = this.relationTypes.right || [];

            return right.includes('videos') || right.includes('media');
        },

        /**
         * Content tab class by index of tab clicked
         *
         * @param {Number} index The tab index
         * @return {string}
         */
        getContentTabClass(index) {
            if (this.activeIndex == index) {
                return 'is-active';
            }

            return '';
        },

        /**
         * Return true if specified pagination page link must be shown
         *
         * @param {Number} page The pagination page number
         *
         * @return {boolean} True if show page link
         */
        paginationPageLinkVisible(page) {
            if (this.pagination.page_count <= 7) {
                // show till 7 links
                return true;
            }
            if (page === 1 || page === this.pagination.page_count) {
                // show first and last page link
                return true;
            }
            if (page >= this.pagination.page - 1 && page <= this.pagination.page + 1) {
                // show previous and next page link
                return true;
            }

            return false;
        },

        /**
         * Add/remove elements to selectedObjects list
         *
         * @param {Object} object The object
         * @param {Event} evt The event
         * @return {void}
         */
        toggle(object, evt) {
            let position = this.selectedObjects.indexOf(object);
            if (position != -1) {
                this.selectedObjects.splice(position, 1);
            } else {
                this.selectedObjects.push(object);
            }
        },

        /**
         * Load objects (using filter and pagination)
         *
         * @emits Event#count
         *
         * @param {Object} filter filter object
         *
         * @return {Promise} repsonse from server
         */
        async loadObjects(filter) {
            this.objects = [];
            this.loading = true;
            let response = await this.getPaginatedObjects(true, filter);
            this.loading = false;
            this.$emit('count', this.pagination.count);

            return response;
        },

        /**
         * Go to specific page
         *
         * @param {Number} page The page number
         * @param {Object} filter filter object
         *
         * @return {Promise} The response from server with new data
         */
        async toPage(page, filter) {
            this.objects = [];
            this.loading = true;
            let response = await PaginatedContentMixin.methods.toPage.call(this, page, filter);
            this.loading = false;

            return response;
        },

        /**
         * helper function: build open view url
         *
         * @param {String} objectType
         * @param {Number} objectId
         *
         * @return {String} url
         */
        buildViewUrl(objectType, objectId) {
            return `${window.location.protocol}/${window.location.host}/${objectType}/view/${objectId}`;
        },
    },
};
