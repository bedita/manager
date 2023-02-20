export default {
    template: `<div class="listobjnav">
        <a :href="urlPrev()" v-if="prev">
            <icon-chevron-left></icon-chevron-left>
        </a>
        <icon-chevron-left v-else></icon-chevron-left>
        <a :href="urlNext()" v-if="next">
            <icon-chevron-right></icon-chevron-right>
        </a>
        <icon-chevron-right v-else></icon-chevron-right>
        <div><: index :> / <: total :></div>
    </div>`,

    components: {
        IconChevronLeft: () => import(/* webpackChunkName: "icon-chevron-left" */'@carbon/icons-vue/es/chevron--left/32.js'),
        IconChevronRight: () => import(/* webpackChunkName: "icon-chevron-right" */'@carbon/icons-vue/es/chevron--right/32.js'),
    },

    props: {
        obj: {},
    },

    data() {
        return {
            index: '',
            next: '',
            object_type: '',
            prev: '',
            total: '',
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.index = this.obj?.index || '';
            this.next = this.obj?.next || '';
            this.object_type = this.obj?.object_type || '';
            this.prev = this.obj?.prev || '';
            this.total = this.obj?.total || '';
        });
    },

    methods: {
        urlNext() {
            return `${BEDITA.base}/view/${this.next}`;
        },
        urlPrev() {
            return `${BEDITA.base}/view/${this.prev}`;
        },
    },
}
