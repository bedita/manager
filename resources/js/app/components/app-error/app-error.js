import { error as showError } from 'app/components/dialog/dialog';

export default {
    name: 'app-error',

    template: '<div></div>',

    props: {
        check: false,
        message: null,
    },

    async mounted() {
        if (this.check != 1) {
            showError(this.message);
        }
    },
};
