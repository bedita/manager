/**
 * Mixins: DragdropMixin
 *
 * enables drag/drop events on main element $el
 *
 * TO-DO advanced drag-drop feature... sortable...
 *
 * - define drop target with dynamic prop droppable (this.$el as default);
 * - define draggable elements with draggable prop
 * - define accepted-drop element with :accepted-drop as array of
 *   DOM selectors (. for classes, # for ids, <element> for dom elements)
 *   to allow drop of files add from-files selector, or any for everything
 *
 * @requires ObservableMixin
 *
 * @prop {Array} acceptedDrop
 *
 * @emits dragstart
 * @emits dragover
 * @emits dragover-once
 * @emits dragleave
 * @emits drop
 */

import { ObservableMixin } from 'app/mixins/observable';

let dragdropPayload = {};
let draggedData = {};
let draggedElement = null;

export const DragdropMixin = {
    mixins: [ ObservableMixin ],

    props: {
        acceptedDrop: {
            type: Array,
            default: () => [],
        }
    },

    data() {
        return {
            attrs: ['droppable', 'accepted-drop'], // observed attributes
            from: {},
            overElement: null,
            dropElement: null,
            acceptedDropArray: [],
            draggableElements: [],
            dragOverFirst: true,
            antiGlitchTimer: null,
            _dropEnabled: false,
            _sortEnabled: false,
            sortableContainer: null,
            sortableElements: [],
        }
    },

    mounted() {
        this.initDroppableElements();
        this.initDraggableElements();
        this.initSortableContainer();
    },

    updated() {
        this.initDroppableElements();
        this.initDraggableElements();
        this.initSortableContainer();

    },

    destroyed() {
        // clean up
        this.dropElement.removeEventListener('dragover', this.onDragover, false);
        this.dropElement.removeEventListener('dragleave', this.onDragleave, false);
        this.dropElement.removeEventListener('drop', this.onDrop, false);
        if (this.draggableElements.length) {
            this.$el.removeEventListener('dragstart', this.onDragstart, false);
        }
    },

    methods: {
        /**
         * ovveride of ObservableMixin onAttributeChanges
         *
         * @param {Array} mutationsList
         * @param {Function} observer
         *
         * @return {void}
         */
        onAttributeChanges(mutationsList, observer) {
            for (var mutation of mutationsList) {
                if (mutation.type == 'attributes') {
                    if (mutation.attributeName === 'droppable') {
                        this.enableDrop();
                        this.dropElement = mutation.target;
                    } else if (mutation.attributeName === 'accepted-drop') {
                        let accepted = mutation.target.getAttribute('accepted-drop');
                        if (accepted) {
                            this.acceptedDropArray = accepted.split(',');
                        }
                    } else if (mutation.attributeName === 'sortable') {
                        this.enableSort();
                    }
                }
            }
            console.debug(observer);
        },

        /**
         *  init drop events for droppable element
         *
         *  @return {void}
         */
        initDroppableElements() {
            // default droppable element
            this.dropElement = this.$el;

            if (this.dropElement) {
                // search for a specific drop target
                let dropElementInView = this.$el.querySelector('[droppable]');
                if (dropElementInView) {
                    this.enableDrop();
                    this.dropElement = dropElementInView;
                    let accepted = dropElementInView.getAttribute('accepted-drop');
                    if (accepted) {
                        this.acceptedDropArray = accepted.split(',');
                    }
                }

                // setup Drop events
                this.dropElement.addEventListener('drop', this.onDrop, true);
                this.dropElement.addEventListener('dragover', this.onDragover, true);
                this.dropElement.addEventListener('dragleave', this.onDragleave, true);
            }
        },

        /**
         * init drag event on draggable elements
         *
         *  @return {void}
         */
        initDraggableElements() {
            // check if at least 1 draggable is defined in component view
            let draggables = this.$el.querySelectorAll('[draggable]');
            if (draggables.length) {
                // if so set up dragstart event
                this.draggableElements = draggables;
                this.$el.addEventListener('dragstart', this.onDragstart, true);
                this.$el.addEventListener('dragend', this.onDragend, true);
            }
        },

        /**
         * init drag event on draggable elements
         *
         *  @return {void}
         */
        initSortableContainer() {
            // check if at least 1 draggable is defined in component view

            let sortable = this.$el.querySelector('[sortable]');
            if (sortable) {
                this.enableSort();
                // if so set up dragstart event
                this.sortableContainer = sortable;
                this.sortableElements = Array.from(sortable.querySelectorAll('[draggable]'));
            }
        },

        /**
         * set dragdrop info data in Event property dragdrop.
         * ev.dragdrop = {
         *    dragged, // DOM element
         *    over, // DOM element
         *    drop, // DOM element
         *    data // Custom data
         * }
         *
         * @param {Event} ev mouse event
         * @param {Object} data
         *
         * @return {void}
         */
        setDragdropData() {
            dragdropPayload = {
                dragged: draggedElement,
                over: this.overElement,
                drop: this.dropElement,
                data: draggedData,
            }
        },

        /**
         * drag start event callback
         * set current draggedElement
         *
         * @emits Event#dragstart
         *
         * @param {Event} ev mouse event
         *
         * @return {void}
         */
        onDragstart(ev) {
            draggedElement = ev.target;
            let dragData = draggedElement.getAttribute('drag-data');

            try {
                draggedData = JSON.parse(dragData);
            } catch (e) {
                console.error('failed parsing drag data');
            }
            this.setDragdropData();
            this.$emit('dragstart', ev, draggedData);
        },


        /**
         * drag over event callback
         * check if current dragged element is accepted by drop target
         * if true set dragover class to drop target and emits events
         *
         * @param {Event} ev mouse event
         *
         * @emits Event#dragover-once first dragover
         * @emits Event#dragover continuous dragover
         *
         * @return {void}
         */
        onDragover(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            if (!this._dropEnabled) {
                return;
            }

            // check if draggable is accepted for drop target (if no rules are defined all draggable are accepted)
            let draggedElement = dragdropPayload.dragged;

            if (this._sortEnabled && draggedElement) {
                draggedElement.classList.add('sorting');
                this.sortElement(draggedElement, ev);
            }

            if (!this.isAcceptedDrag()) {
                return;
            }

            window.clearTimeout(this.antiGlitchTimer);
            this.overElement = ev.target;

            // set dragdrop data
            this.setDragdropData(ev);
            this.dropElement.classList.add('dragover');

            if (this.dragOverFirst) {
                this.dragOverFirst = false;
                this.$emit('dragover-once', ev);
            }

            this.$emit('dragover', ev);
        },

        /**
         * drag leave event callback
         * check for false positive (if mouse left element to enter in a child element)
         * if leave is legit removes dragover class from drop target
         * emit dragleave event
         *
         * @param {Event} ev mouse event
         *
         * @emits Event#dragleave
         *
         * @return {void}
         */
        onDragleave(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            this.overElement = null,
            this.setDragdropData(ev);

            this.dragOverFirst = true;
            this.antiGlitchTimer = window.setTimeout(() => {
                this.dropElement.classList.remove('dragover');

                // check mouse location
                if (!this.isOverChild(this.dropElement, ev)) {
                    this.$emit('dragleave');
                }
            }, 25);
        },

        onDragend() {
            dragdropPayload = {};
            draggedData = {};
            draggedElement = null;
        },

        /**
         * drop event callback
         * remove dragover class from drop target
         * always emits dragleave
         * check for files in dataTransfer object
         * if true emits drop-files with special payload
         *
         * @param {Event} ev mouse event
         *
         * @emits Event#dragleave
         * @emits Event#drop-files
         * @emits Event#drop
         *
         * @return {void}
         */
        onDrop(ev) {
            ev.preventDefault();
            ev.stopPropagation();

            if (this._sortEnabled) {
                if (draggedElement && draggedElement.classList) {
                    draggedElement.classList.remove('sorting');
                    this.$emit('sort-end', dragdropPayload);
                }
            }

            if (!this._dropEnabled || !this.isAcceptedDrag()) {
                return;
            }

            this.setDragdropData(ev);

            // still emit dragleave and remove class
            this.dropElement.classList.remove('dragover');
            this.$emit('dragleave');

            let files = ev.target.files || ev.dataTransfer.files;

            if (files.length) {
                // add files to payload
                dragdropPayload.files = files;
                this.$emit('drop-files', ev, dragdropPayload);
            } else {
                this.$emit('drop', ev, dragdropPayload);
            }

            draggedElement = null;
        },

        /**
         * check if mouse coordinates are over any child DOM elements of main dropElement
         *
         * @param {Event} ev mouse event
         *
         * @return {Boolean} true if mouse entered a child element
         */
        isOverChild(container, event) {
            if (!container) {
                return false;
            }
            let rect = container.getBoundingClientRect();

            // mouse outside browser
            const actualInnerWidth = document.body.clientWidth;
            const actualInnerHeight = document.body.clientHeight;
            if (event.clientX <= 0 || event.clientY <= 0 || event.clientX > actualInnerWidth || event.clientY > actualInnerHeight) {
                return false;
            }
            // mouse inside child element
            if (event.clientY >= rect.top && event.clientY <= rect.bottom && event.clientX >= rect.left && event.clientX <= rect.right) {
                return true;
            }

            // mouse outside element
            return false;
        },

        /**
         * check if dragged element is accepted from drop target
         *
         * @returns {Boolean}
         */
        isAcceptedDrag() {
            // default: allow for any selector
            if (this.acceptedDropArray.indexOf('any') !== -1) {
                return true;
            }
            let isValid = this.acceptedDropArray.indexOf('from-files') !== -1;
            if (this.acceptedDropArray.length && draggedElement) {
                isValid = this.acceptedDropArray.reduce((status, query) => status = status || draggedElement.matches(query), false);
            }
            return isValid;
        },

        sortElement(draggedElement, ev) {
            let overElement = this.sortableElements.filter(child => this.isOverChild(child, ev));
            if (overElement.length) {
                overElement = overElement[0];
            }

            const findPrev = (el, target) => {
                if (!el) {
                    return null;
                }
                const sibling = el.previousSibling;
                if (sibling == target) {
                    return sibling;
                }
                return findPrev(sibling, target);
            }

            const findNext = (el, target) => {
                if (!el) {
                    return null;
                }
                const sibling = el.nextSibling;
                if (sibling == target) {
                    return sibling;
                }
                return findNext(sibling, target);
            }

            let prev = findPrev(draggedElement, overElement);
            if (prev) {
                this.sortableContainer.insertBefore(draggedElement, prev);
                this.$emit('sort', dragdropPayload);
            } else {
                let next = findNext(draggedElement, overElement);
                if (next) {
                    this.sortableContainer.insertBefore(draggedElement, next.nextSibling);
                    this.$emit('sort', dragdropPayload);
                }
            }
        },

        /**
         * disable drop
         *
         * @returns {void}
         */
        disableDrop() {
            this._dropEnabled = false;
        },

        /**
         * enable drop
         *
         * @returns {void}
         */
        enableDrop() {
            this._dropEnabled = true;
        },

        enableSort() {
            this._sortEnabled = true;
        }
    }
}
