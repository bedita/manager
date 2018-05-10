/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

Vue.component('modules-view', {

    /**
     * component properties
     *
     * @param {Boolean} tabsOpen
     * @param {Object} validate define validation rules according to vee-validate plugin
     *
     * @returns {Object}
     */
    data() {
        return {
            tabsOpen: true,


            validate: {
                password: {
                    required: false,
                    min: 6,
                    confirmed: 'confirm-password',
                },
                confirmPassword: {
                    confirmed: 'password',
                },
            },

        };
    },

    computed: {
        /**
         * map keyboard key's to actions:
         * - 'esc' to toggleTabs()
         */
        keyEvents() {
            return {
                'esc': {
                    keyup: this.toggleTabs,
                }
            }
        },
    },

    methods: {
        /**
         * toggle all property-view components open/closed
         *
         * @return {void}
         */
        toggleTabs() {
            this.tabsOpen = !this.tabsOpen;
        },

        /**
         * compute css classes to display validation results
         *
         * @param {String} key
         *
         * @return {String} class names
         */
        validateResult(key) {
            let field = this.fields[key];
            if (!field) {
                return;
            }
            let validation = [];

            if (field.changed && field.valid) {
                validation.push('valid-field');
            } else {
                if (key === 'password') {
                    let isConfirmation = this.errors.firstByRule(key, 'confirmed');
                    if (isConfirmation && isConfirmation.length) {
                        validation.push('need-confirmation');
                    }
                }

                if(this.errors.has(key)) {
                    validation.push('has-error');
                }
            }

            return validation.join(' ');;
        },


        /**
         * check form validation before submit
         *
         * @param {Event} event
         *
         * @return {Boolean} true if form is valid
         */
        async validateBeforeSubmit(event) {
            let validate = false;
            validate = await this.$validator.validateAll();
            if (validate) {
                return true;
            }

            event.preventDefault();
            // TO-DO message to the user

            return false;
        },
    }
});


