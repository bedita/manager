/**
 * Component that creates an input element with dynamic attributes and directives
 *
 * <input-dynamic-attributes> component
 *
 * @prop {Object} value
 * @prop {Object} attrs list of attributes/directives
 */

export default {
    name: "InputDynamicAttributes",

    props: {
        value: {
            type: String,
        },
        attrs: {
            type: Object,
            required: true,
        },
    },

    /**
     * Vue internal render function
     *
     * @param {Function} createElement create VNode function
     * @returns {VNode} input element
     */
    render(createElement) {
        const directivesName = Object.keys(this.attrs).filter(attr => attr.startsWith('v-'));

        // remove 'v-' text from vue directives
        let directives = directivesName.map((name) => {
            const directiveName = name.split('v-').pop();
            return {
                name: directiveName,
            }
        });

        // always force date format without time
        delete this.attrs.time;

        // create and return input element
        return createElement('input', {
            // Custom directives
            directives: directives,
            // Normal HTML attributes
            attrs: this.attrs,
            // DOM properties
            domProps: {
                value: this.value,
            },
            // Event handlers are nested under `on`
            on: {
                input: e => {
                    this.$emit('update:value', e.target.value);
                },
            },
        });
    },
};
