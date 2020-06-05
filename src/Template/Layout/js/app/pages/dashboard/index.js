/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Pages/Dashboard/index.twig
 *
 * <dashboard> component used for Dashboard -> Index
 *
 */

export default {
    props: {
        q: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            searchString: '',
        };
    },
    created() {
        this.searchString = this.q;
    },
    methods: {
        captureKeys(e) {
            let key = e.which || e.keyCode || 0;
            switch (key) {
                case 13:
                    this.searchObjects();
                    break;
                case 27:
                    this.popUpAction = '';
                    break;
            }
        },

        searchObjects() {
            if (this.searchString) {
                this.$refs.searchSubmit.classList.add('is-loading-spinner');
                window.location.href = BEDITA.base + '/objects?q=' + this.searchString;
            }
        },
    }
}
