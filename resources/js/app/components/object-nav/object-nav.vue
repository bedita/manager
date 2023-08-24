<template>
    <div class="listobjnav">
        <div>
            <a :href="urlPrev()" v-if="prev">
                <app-icon icon="carbon:chevron-left" class="hw-30"></app-icon>
            </a>
            <span v-else>
                <app-icon icon="carbon:chevron-left" class="hw-30"></app-icon>
            </span>
            <a :href="urlNext()" v-if="next">
                <app-icon icon="carbon:chevron-right" class="hw-30"></app-icon>
            </a>
            <span v-else>
                <app-icon icon="carbon:chevron-right" class="hw-30"></app-icon>
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
