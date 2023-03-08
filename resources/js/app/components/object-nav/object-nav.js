import { Icon } from '@iconify/vue2';

export default {
    template: `<div class="listobjnav">
        <a :href="urlPrev()" v-if="prev">
            <Icon icon="carbon:chevron-left"></Icon>
        </a>
        <Icon icon="carbon:chevron-left" v-else></Icon>
        <a :href="urlNext()" v-if="next">
            <Icon icon="carbon:chevron-right"></Icon>
        </a>
        <Icon icon="carbon:chevron-right" v-else></Icon>
        <div><: index :> / <: total :></div>
    </div>`,

    components: {
        Icon,
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
