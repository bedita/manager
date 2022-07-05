export default {
    template: `<div class="object-types-list">
        <div v-for="type in all">
            <input type="checkbox" :checked="type in selected" :value="type" v-model="selected" />
            <a :href="ref(type)" :class="className(type)">
                <: type :>
            </a>
        </div>
        <input type="hidden" :name="name()" :value="value()" />
    </div>`,

    props: {
        all: [],
        selected: [],
        side: '',
    },

    data() {
        return {
        };
    },

    methods: {
        className(t) {
            return `tag has-background-module-${t}`;
        },
        name() {
            return `current_${this.side}`;
        },
        ref(t) {
            return `/model/object_types/view/${t}`;
        },
        value() {
            return this.selected.join(',');
        },
    },
}
