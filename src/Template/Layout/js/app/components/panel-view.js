/**
* Templates that uses this component (directly or indirectly):
*  Template/Elements/panel.twig
*
* <panel-view> component used to show a sub-view in a side panel
*
* Use an event bus, PanelEvents, that needs to be imported.
*
* @example
* // in requester view component
* PanelEvents.listen('editSomething:save', this, this.editSomethingSaveCallback);
*
* // to open panel
* PanelEvents.requestPanel({
*   action: 'editSomething', // used in PanelView to render right component
*   from: this, // used in BusEvents to sendback data to right component
*   data: data, // payload
*  })
*
* // panel's rendered component
* PanelEvents.sendBack('editSomething:save', payload );
*
*/

import Vue from 'vue';
import { t } from 'ttag';

/**
 * Event bus to communicate from/to PanelView
 */
export const PanelEvents = new Vue({
    methods: {
        /**
         * set listener for events and if "from" is set, it extract its _uid and use it to match the evant signature
         *
         * @param {String} evtName event name
         * @param {VueComponent|null} from view component that needs to listen
         * @param {Function} callback event callback
         *
         * @returns {void}
         */
        listen(evtName, from, callback) {
            // listen to eventName@component-unique-identifier (if passed)
            const senderSign = from ? `@${from._uid}` : '';
            this.$on(`${evtName}${senderSign}`, callback);
        },

        /**
        * remove event listener for callback
        *
        * @param {String} evtName event name
        * @param {VueComponent|null} from view component that needs to listen
        * @param {Function} callback event callback
        *
        * @returns {void}
        */
        stop(evtName, from, callback) {
            const senderSign = from ? `@${from._uid}` : '';
            this.$off(`${evtName}${senderSign}`, callback);
        },

        /**
        * send payload to panel view last requester
        *
        * @param {String} evtName event name
        * @param {Object} data payload
        *
        * @returns {void}
        */
        sendBack(evtName, data) {
            this.$emit('panel:send', { action: evtName , data });
        },

        /**
        * send event with payload and if "to" is set, it extract its _uid and use it to match the evant signature
        *
        * @param {String} evtName event name
        * @param {VueComponent|null} to view component recipient
        * @param {Object} payload event callback
        *
        * @returns {void}
        */
        send(evtName, to, payload = {}) {
            const recipientSign = to ? `@${to._uid}` : '';
            this.$emit(`${evtName}${recipientSign}`, payload);
        },

        /**
        * send panel request and pass data
        *
        * @param {Object} data
        *
        * @returns {void}
        */
        requestPanel(request) {
            this.$emit('panel:request', request);
        },

        /**
         * send close panel event
         *
         * @returns {void}
         */
        closePanel() {
            this.$emit('panel:close');
        },
    }
});

/**
 *
 * @listens panel:request request panel with data
 * @listens panel:send send message through panel bus
 * @listens panel:save save event
 * @listens panel:open open panel (just visibility)
 * @listens panel:hide hide panel (keep rendered component alive)
 * @listens panel:close close panle and flush data
 *
 * @emits panel:requested the panel has been requested
 * @emits panel:saved a save event happened
 * @emits panel:opened an open event happened
 * @emits panel:hidden an hide event happened
 * @emits panel:closed a close event happened
 */
export const PanelView = {
    template: /*template*/`
        <aside class="main-panel" :class="open ? 'on' : ''">
            <header v-if="!customHeader" class="tab unselectable open">
                <h2><span v-show="closeButtonVisible"><: t(headerLabel) | humanize :></span> &nbsp;</h2>
                <a href="#" @click.prevent="close()"><: t(closeButtonLabel) :></a>
            </header>

            <section class="fieldset main-panel-body">
                <slot :data="data" :action="action"></slot>
            </section>

            <footer v-if="!customFooter">
                <p>
                    <button v-show="saveButtonVisible" class="has-background-info has-text-white"
                        @click.prevent="save()"><: t(saveButtonLabel) :></button>
                </p>
            </footer>
        </aside>
    `,

    props: {
        customHeader: {
            type: Boolean,
            default: false,
        },
        customFooter: {
            type: Boolean,
            default: false,
        },
        saveButtonVisible: {
            type: Boolean,
            default: true,
        },
        headerLabel: {
            type: String,
            default: () => t('Panel'),
        },
        saveButtonLabel: {
            type: String,
            default: () => t('save'),
        },
        closeButtonLabel: {
            type: String,
            default: () => t('close'),
        },
        closeButtonVisible: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            open: false,
            stack: [],
        }
    },

    computed: {
        /**
         * VueComponent from last request (if present)
         *
         * @returns {VueComponent|null}
         */
        from() {
            return this.stack.length && this.stack.slice().pop().from || null;
        },

        /**
         * action from last request (if present)
         *
         * @returns {String}
         */
        action() {
            return this.stack.length && this.stack.slice().pop().action || '';
        },

        /**
         * data from last request (if present)
         *
         * @returns {Object}
         */
        data() {
            return this.stack.length && this.stack.slice().pop().data || {};
        }
    },

    watch: {
        /**
         * panel open property watcher to determine window body class
         *
         * @param {Boolean} value
         */
        open(value) {
            if (value) {
                window.document.body.classList.add('panel-is-open');
            } else {
                window.document.body.classList.remove('panel-is-open');
            }
        }
    },

    created() {
        // set up global panel listeners

        // used to request panel
        PanelEvents.listen('panel:request', null, (request) => {
            let { data, from } = request;

            this.open = true;
            this.stack.push(request);

            PanelEvents.send('panel:requested', null, request);
        });

        PanelEvents.listen('panel:send', null, (request) => {
            let { action, data } = request;

            PanelEvents.send(action, this.from, data);
        });

        PanelEvents.listen('panel:save', null, (data) => {
            PanelEvents.sendBack('panel:saved', this.from, data);
            this.pop();
        });

        PanelEvents.listen('panel:open', null, (request) => {
            this.open = true;
            PanelEvents.send('panel:opened', null, request);
        });

        PanelEvents.listen('panel:hide', null, (request) => {
            this.open = false;
            PanelEvents.send('panel:hidden', null, request);
        });

        PanelEvents.listen('panel:close', null, () => {
            this.open = false;
            this.pop();
        });
    },

    methods: {
        /**
         * extract last panel request, and close panel if stack is empty
         *
         * @return {void}
         */
        pop() {
            const stack = this.stack;
            const last = stack.pop();
            if (stack.length) {
                this.open = true;
            } else {
                this.open = false;
                PanelEvents.$emit('panel:closed', last);
            }
        },

        /**
        * default save button action, send save event with data
        *
        * @param {Object} data
        *
        * @return {void}
        */
        save(data) {
            PanelEvents.$emit('panel:save', data);
        },

        /**
        * default close button action, send close event
        *
        * @return {void}
        */
        close() {
            PanelEvents.$emit('panel:close');
        },
    }
}
