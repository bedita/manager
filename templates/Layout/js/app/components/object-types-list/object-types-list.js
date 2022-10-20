export default {
    template: `<div class="object-types-list">
        <div v-for="type in all">
            <input type="checkbox" :checked="type in selected" :value="type" v-model="selected" />
            <a :href="ref(type)" :class="className(type)">
                <: type :>
            </a>
        </div>
        <input type="hidden" :name="name('current')" :value="valueCurrent()" />
        <input type="hidden" :name="name('change')" :value="valueChange()" />
    </div>`,

    props: {
        all: [],
        selected: [],
        side: '',
    },

    data() {
        return {
            current: [],
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.current = this.selected;
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
            return this.selected.join(',');
        },
    },
}
