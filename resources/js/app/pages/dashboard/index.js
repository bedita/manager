export default {
    name: 'DashboardIndex',

    components: {
        RecentActivity:() => import(/* webpackChunkName: "recent-activity" */'app/components/recent-activity/recent-activity'),
    },

    props: {
        q: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            searchId: '',
            searchString: '',
        };
    },

    created() {
        if (document.referrer.endsWith('/login') && window.top !== window) {
            window.top.postMessage('login', BEDITA.base);
        }

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
        goToID() {
            if (!this.searchId) {
                return;
            }
            window.location.href = `${BEDITA.base}/view/${this.searchId}`;
        },
        searchObjects() {
            if (this.searchString) {
                this.$refs.searchSubmit.classList.add('is-loading-spinner');
                window.location.href = BEDITA.base + '/objects?q=' + this.searchString;
            }
        },
    }
}
