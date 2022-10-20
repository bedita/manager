/**
 * Mixins: ObservableMixin
 *
 * enables DOM attrs observation in mixin/components
 *
 */

export const ObservableMixin = {
    data() {
        return {
            _mutationObserver: null,
            attrs: [],
        }
    },

    mounted() {
        this.setMutationObserver(this.attrs);
    },

    destroyed() {
        this._mutationObserver.disconnect();
    },

    methods: {
        /**
         * setup cascade observation of attributes on this.$el DOM element
         *
         * @param {Array} attrs of attributes
         *
         * @return {void}
         */
        setMutationObserver(attrs) {
            this._mutationObserver = new MutationObserver(this.onAttributeChanges);
            this._mutationObserver.observe(this.$el, {
                attributes: true,
                attributeFilter: attrs,
                subtree: true,
                attributeOldValue: true,
            });
        },

        /**
         * set attrs to observe with callback onAttributeChanges()
         *
         * @param {Array} attrs
         */
        setObservableAttrs(attrs) {
            this.attrs = attrs;
            this._mutationObserver.disconnect();
            this.setMutationObserver(this.attrs);
        },

        /**
         * placeholder function, override to apply observable logic
         *
         * @param {Array} mutationsList
         * @param {Object} observer
         */
        onAttributeChanges(mutationsList, observer) {
            console.debug(mutationsList);
            console.debug(observer);
        },
    }
}
