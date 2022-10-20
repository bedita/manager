/**
 * Component that creates an input element with dynamic attributes and directives
 *
 * <input-dynamic-attributes> component
 *
 * @prop {Object} value
 * @prop {Object} attrs list of attributes/directives
 */

export default {
    name: 'InputDynamicAttributes',

    props: {
        form: String,
        value: {
            type: [String, Boolean],
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
        const attrs = { ...(this.attrs || {}), form: this.form };
        const directivesName = Object.keys(attrs).filter(attr => attr.startsWith('v-'));

        // remove 'v-' text from vue directives
        const directives = directivesName.map((name) => {
            const directiveName = name.split('v-').pop();
            return {
                name: directiveName,
            };
        });

        let domProps = { value: this.value };
        let on = { input: e => { this.$emit('update:value', e.target.value); } };

        // if checkbox is to be rendered a different mapping is needed
        if (attrs.type === 'checkbox') {
            domProps = { checked: this.value === 'true' };
            on = { input: e => { this.$emit('update:value', e.target.checked); } };
        }

        // create and return input element
        return createElement('input', {
            // Custom directives
            directives,
            // Normal HTML attributes
            attrs,
            // DOM properties
            domProps,
            // Event handlers are nested under `on`
            on,
        });
    },
};
