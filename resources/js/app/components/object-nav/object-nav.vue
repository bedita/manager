<template>
    <div class="listobjnav">
        <div>
            <a :href="urlPrev()" v-if="prev">
                <Icon icon="carbon:chevron-left" class="hw-30"></Icon>
            </a>
            <span v-else>
                <Icon icon="carbon:chevron-left" class="hw-30"></Icon>
            </span>
            <a :href="urlNext()" v-if="next">
                <Icon icon="carbon:chevron-right" class="hw-30"></Icon>
            </a>
            <span v-else>
                <Icon icon="carbon:chevron-right" class="hw-30"></Icon>
            </span>
        </div>
        <div>{{ index }} / {{ total }}</div>
    </div>
</template>

<script>
export default {

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
</script>
