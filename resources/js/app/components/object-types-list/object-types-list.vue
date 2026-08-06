<template>
    <div class="object-types-list">
        <div
            v-for="type in all"
            :key="type"
        >
            <input
                type="checkbox"
                :checked="type in modified"
                :value="type"
                v-model="modified"
            >
            <a
                :href="ref(type)"
                :class="className(type)"
            >
                {{ type }}
            </a>
        </div>
        <input
            type="hidden"
            :name="name('current')"
            :value="valueCurrent()"
        >
        <input
            type="hidden"
            :name="name('change')"
            :value="valueChange()"
        >
    </div>
</template>
<script>
export default {
    name: 'ObjectTypesList',
    props: {
        all: {
            type: Array,
            default: () => ([]),
        },
        selected: {
            type: Array,
            default: () => ([]),
        },
        side: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            current: [],
            modified: [],
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.current = this.selected;
            this.modified = this.selected;
        });
    },

    methods: {
        className(t) {
            return `tag has-background-module-${t}`;
        },
        name(prefix) {
            return `${prefix}_${this.side}`;
        },
        ref(t) {
            return `/model/object_types/view/${t}`;
        },
        valueCurrent() {
            return this.current.join(',');
        },
        valueChange() {
            return this.modified.join(',');
        },
    },
}
</script>
