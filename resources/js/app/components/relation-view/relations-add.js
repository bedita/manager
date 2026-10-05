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
        Thumbnail:() => import(/* webpackChunkName: "thumbnail" */'app/components/thumbnail/thumbnail'),
        ClipboardItem: () => import(/* webpackChunkName: "clipboard-item" */'app/components/clipboard-item/clipboard-item'),
        FastCreate: () => import(/* webpackChunkName: "fast-create" */'app/components/fast-create/fast-create'),
        FastCreateContainer: () => import(/* webpackChunkName: "fast-create-container" */'app/components/fast-create/fast-create-container'),
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
            dataList: false,
            selectAll: false,

            // create object form data
            saving: false,
            file: null,
            url: null,
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
        this.$nextTick(async () => {
            // when object is staged for saving in relation view updates the list of alreadyInView
            PanelEvents.listen('relations-view:add-already-in-view', null, this.addToAlreadyInView);
            PanelEvents.listen('relations-view:remove-already-in-view', null, this.removeFromAlreadyInView);

            // alreadyInView doesn't get out of pagination objects. We need to "re-add" all objects
            try {
                if (this.$attrs.object?.type && this.$attrs.object?.id) {
                    const related = await this.fetchRelated(this.$attrs.object.type, this.$attrs.object.id);
                    for (const obj of related) {
                        if (this.alreadyInView.indexOf(obj.id) === -1) {
                            this.addToAlreadyInView(obj);
                        }
                    }
                }
            } catch (error) {
                console.error(error);
            }
        });
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
        async fetchRelated(type, id) {
            const pageSize = 100;
            const baseUrl = new URL(BEDITA.base).pathname;
            const response = await fetch(`${baseUrl}api/${type}/${id}/${this.relationName}?page_size=${pageSize}&page=1`, {
                credentials: 'same-origin',
                headers: {
                    accept: 'application/json',
                }
            });
            const responseJson = await response.json();
            let count = responseJson?.meta?.pagination?.page_count || 0;
            if (count > 1) {
                for (let i = 2; i <= count; i++) {
                    const r = await fetch(`${baseUrl}api/${type}/${id}/${this.relationName}?page_size=${pageSize}&page=${i}`, {
                        credentials: 'same-origin',
                        headers: {
                            accept: 'application/json',
                        }
                    });
                    const rj = await r.json();
                    responseJson.data = responseJson.data.concat(rj.data);
                }
            }

            return responseJson?.data || [];
        },
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

        addCreated(object) {
            let createdObjects = (Array.isArray(object) ? object : [object]) || [];
            this.objects = createdObjects.concat(this.objects);
            this.selectedObjects = createdObjects.concat(this.selectedObjects);
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
                if (responseJson?.error) {
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
                `from-relation-${this.relationName} has-status-${related.attributes.status}`,
                {
                    selected: this.selectedObjects.indexOf(related) !== -1,
                    unselectable: this.isUnselectableObject(related?.id),
                }
            ]
        },

        resetForm(event, type) {
            event.preventDefault();
            if (this.$refs[`${type}-form`]) {
                this.$refs[`${type}-form`].reset();
            }
            this.file = null;
            this.url = null;
            const t = type || this.relationTypes.right[0];
            this.object = createData(this.relationTypes && t);
        },

        resetType(type) {
            if (type) {
                this.object.type = type;

                return;
            }
            if (this.relationTypes?.right?.length === 1) {
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
         * @return {void}
         */
        toggle(object) {
            let position = this.selectedObjects.indexOf(object);
            if (position != -1) {
                this.selectedObjects.splice(position, 1);
            } else {
                this.selectedObjects.push(object);
            }
        },

        /**
         * Select/deselect all objects
         *
         * @param {Event} event The event object
         * @return {void}
         */
        toggleAll(event) {
            this.selectedObjects = event.target.checked ? [...this.objects] : [];
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

        fileAcceptMimeTypes(type) {
            return this.$helpers.acceptMimeTypes(type);
        },

        datesInfo(obj) {
            const created = new Date(obj.meta.created).toLocaleDateString() + ' ' + new Date(obj.meta.created).toLocaleTimeString();
            const modified = new Date(obj.meta.modified).toLocaleDateString() + ' ' + new Date(obj.meta.modified).toLocaleTimeString();
            if (!obj?.attributes?.publish_start) {
                return t`Created on ${created}.` + ' ' + t`Modified on ${modified}.`;
            }
            const published = new Date(obj.meta.publish_start).toLocaleDateString() + ' ' + new Date(obj.attributes.publish_start).toLocaleTimeString();

            return t`Created on ${created}.` + ' ' + t`Modified on ${modified}.` + ' ' + t`Publish start on ${published}.`;
        },

        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
    },
};
