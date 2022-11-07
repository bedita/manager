import { error as showError } from 'app/components/dialog/dialog';

export default {
    name: 'app-error',

    template: '<div></div>',

    props: {
        message: null,
    },

    async mounted() {
        showError(this.message);
    },
};
