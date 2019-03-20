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

import FilterBoxView from 'app/components/filter-box';
import { PaginatedContentMixin, DEFAULT_PAGINATION } from 'app/mixins/paginated-content';
import { PanelEvents } from 'app/components/panel-view';
import { DragdropMixin } from 'app/mixins/dragdrop';
import sleep from 'sleep-promise';

export default {
    mixins: [ PaginatedContentMixin, DragdropMixin ],

    inject: ['getCSFRToken'],

    components: {
        FilterBoxView,
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
            objectType: '',
            titlePlaceholder: 'title',
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
            return this.relationTypes && this.relationTypes.right.indexOf('media') !== -1;
        },
    },

    mounted() {
        // when object is staged for saving in relation view updates the list of alreadyInView
        PanelEvents.listen('relations-view:update-already-in-view', null, this.updateAlreadyInView);
    },

    destroyed() {
        PanelEvents.stop('relations-view:update-already-in-view', null, this.updateAlreadyInView);
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
            return this.alreadyInView.concat(addedIds).indexOf(id) !== -1;
        },

        /**
         * update objects already added
         *
         * @param {Object} object
         *
         * @returns {void}
         */
        updateAlreadyInView(object) {
            this.addedObjects.push(object);
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
