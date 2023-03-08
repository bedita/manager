/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Element/menu/menu.twig
 *
 * <menu> component
 *
 */
import { Icon } from '@iconify/vue2';

export default {
    components: {
        Icon,
    },
    data() {
        return {
            popUpAction: '',
            searchString: '',
        };
    },
    methods: {
        togglePopup(action) {
            if (action == this.popUpAction) {
                this.popUpAction = '';
            } else {
                this.popUpAction = action;
                this.$nextTick(() => {
                    this.$refs.searchInput.focus();
                });
            }
        },

        captureKeys(e) {
            let key = e.which || e.keyCode || 0;
            switch (key) {
                case 13:
                    this.go();
                    break;
                case 27:
                    this.popUpAction = '';
                    break;
            }
        },

        go() {
            let urlPath = '';
            if (this.popUpAction == 'search') {
                urlPath += "/objects?q=";
            } else if (this.popUpAction == 'id') {
                urlPath += "/view/";
            }

            if (this.searchString && urlPath) {
                window.location.href = BEDITA.base + urlPath + this.searchString;
            }
        },
    },
}
