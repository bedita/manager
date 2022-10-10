/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Elements/relations.twig
 *
 * <relations-add> component used for Panel
 *
 * @prop {String} relationName relation name
 * @prop {String} relationLabel relation label
 * @prop {Array} alreadyInView array of objects already added
 * @prop {Object} relationTypes list of available object types
 * @prop {String} configPaginateSizes list of sizes for pagination
 *
 */

import { ACCEPTABLE_MEDIA } from 'config/config';
import { PaginatedContentMixin } from 'app/mixins/paginated-content';
import { PanelEvents } from 'app/components/panel-view';
import { DragdropMixin } from 'app/mixins/dragdrop';
import { error as showError } from 'app/components/dialog/dialog';
import sleep from 'sleep-promise';
import { t } from 'ttag';
import { warning } from 'app/components/dialog/dialog';

const createData = (type = '') => ({
    type,
    attributes: {
        status: 'draft',
    },
});

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
        relationLabel: {
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
            method: 'relationships',
            endpoint: '',
            loading: false,
            selectedObjects: [],
            addedObjects: [],
            activeFilter: {},

            // create object form data
            saving: false,
            file: null,
            url: null,
            showCreateObjectForm: false,
            object: createData('_choose'),
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
            if (!this.object) {
                return true;
            }

            return ACCEPTABLE_MEDIA.indexOf(this.object.type) !== -1;
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
        relationTypes: {
            immediate: true,
            handler(newVal) {
                if (!newVal || !newVal.right) {
                    this.object.type = null;
                    return;
                }

                this.object.type = newVal.right[0];
            },
        },

        relationName: {
            immediate: true,
            handler(newVal) {
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

        resetForms() {
            this.showCreateObjectForm = !this.showCreateObjectForm;
            this.resetType();
        },

        formCheck() {
            const fields = document.getElementById(`${this.object.type}-form-fields`).querySelectorAll('.required');
            for (let i = 0; i < fields.length; i++) {
                if (fields[i].dataset.name === 'status') {
                    if (this.object.attributes.status == '') {
                        return fields[i].dataset.name;
                    }
                } else if (fields[i].value == '') {
                    return fields[i].dataset.name;
                }
            }

            return true;
        },

        /**
         * create new object
         *
         * @param {HTMLElement} target form
         *
         * @return {void}
         */
        async createObject(event) {
            if (!event.target) {
                // form element might be tempered
                return;
            }
            const required = this.formCheck();
            if (required !== true) {
                const msg = t`Missing required data "${required}". Retry`;
                warning(msg);

                return;
            }

            this.saving = true;
            this.loading = true;

            const type = this.object.type;

            // save object
            const baseUrl = window.location.origin;
            const postUrl = `${baseUrl}/${type}/save`;

            // set form data
            const formData = new FormData(document.getElementById(`${type}-form`));
            formData.set('status', this.object.attributes.status);

            if (this.file) {
                formData.set('file', this.file);
            }

            if (this.url) {
                formData.set('url', this.url);
            }

            if (this.file || this.url) {
                formData.append('model-type', type);
            }

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
                if (responseJson.error) {
                    throw new Error(responseJson.error);
                }

                let createdObjects = (Array.isArray(responseJson.data) ? responseJson.data : [responseJson.data]) || [];

                // add newly added object to list / selected
                this.objects = createdObjects.concat(this.objects);
                this.selectedObjects = createdObjects.concat(this.selectedObjects);
            } catch (error) {
                if (error.code === 20) {
                    throw error;
                }

                showError(t`Error while creating new object.`);
                console.error(error);
            } finally {
                this.resetForm(event, type);
                this.resetType(type);
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
                    unselectable: this.isUnselectableObject(related?.id),
                }
            ]
        },

        /**
         * clear form
         * @return {void}
         */
        resetForm(event, type) {
            event.preventDefault();

            this.file = null;
            this.url = null;
            let t = type ? type : this.relationTypes.right[0];
            this.object = createData(this.relationTypes && t);
            const fields = document.querySelectorAll('.fastCreateField');
            for (let i = 0; i < fields.length; i++) {
                fields[i].value = '';
                if (fields[i].jsonEditor) {
                    fields[i].jsonEditor.update({"text": ""});
                }
            }
        },

        resetType(type) {
            if (type) {
                this.object.type = type;

                return;
            }
            if (this.relationTypes.right.length === 1) {
                this.object.type = this.relationTypes.right[0];
            } else {
                this.object.type = '_choose';
            }
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
        async loadObjects(filter = {}) {
            filter.sort = '-created';
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
        async toPage(page, filter = {}) {
            filter.sort = '-created';
            this.objects = [];
            this.loading = true;
            let response = await PaginatedContentMixin.methods.toPage.call(this, page, filter);
            this.loading = false;

            return response;
        },

        /**
         * set file, object type and placeholder
         *
         * @param {Event} event input file change event
         */
        processFile(event) {
            this.file = event.target.files[0];
            if (!this.file) {
                return false;
            }

            if (!this.object.attributes.title) {
                this.object.attributes.title = this.file.name;
            }
        },
    },
};
