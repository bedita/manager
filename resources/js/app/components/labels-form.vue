<template>
    <div class="labels-form">
        <div class="grid">
            <button
                @click.prevent.stop="toggleShow"
                class="button button-outlined"
                style="min-width: 32px; border-top-left-radius: 0; border-bottom-left-radius: 0;"
            >
                <app-icon icon="carbon:row-collapse" v-if="show"></app-icon>
                <app-icon icon="carbon:row-expand" v-if="!show"></app-icon>
                <span v-if="Object.keys(labels).length - 1 > 0">{{ Object.keys(labels).length - 1 }}</span>
            </button>
            <input
                type="text"
                name="label"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                v-model="labels['default']"
                @change="changeLabel"
            >
            <button disabled="true">
                <app-icon icon="carbon:trash-can"></app-icon>
            </button>
        </div>
        <template v-for="lang in Object.keys(labels)">
            <div
                class="grid"
                v-if="lang !== 'default'"
                :key="lang"
                v-show="show"
            >
                <span>{{ languages.find(l => l.id === lang).label }}</span>
                <input
                    type="text"
                    name="label"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    v-model="labels[lang]"
                    @change="changeLabel"
                >
                <button @click.prevent="removeLabel(lang)">
                    <app-icon icon="carbon:trash-can"></app-icon>
                </button>
            </div>
        </template>
        <div
            class="grid"
            v-show="show"
        >
            <select v-model="newlang">
                <option
                    v-for="lang in languages.filter(lang => !Object.keys(labels).includes(lang.id))"
                    :key="lang.id"
                    :value="lang.id"
                >
                    {{ lang.label }}
                </option>
            </select>
            <input
                type="text"
                name="label"
                autocomplete="off"
                autocorrect="off"
                autocapitalize="off"
                v-model="newlabel"
            >
            <button
                :disabled="newlang == 'default' || newlabel.length < 3"
                @click.prevent="addLabel"
            >
                <app-icon icon="carbon:checkmark"></app-icon>
            </button>
        </div>
    </div>
</template>
<script>
export default {
    name: 'LabelsForm',
    props: {
        labelsSource: {
            type: Object,
            required: true,
        },
        language: {
            type: String,
            default: '',
        },
        languages: {
            type: Array,
            default: () => ([]),
        },
    },
    data() {
        return {
            labels: this.labelsSource || {'default': ''},
            newlabel: '',
            newlang: 'default',
            show: false,
        };
    },
    computed: {
        langs() {
            return this.languages.filter(lang => !Object.keys(this.labels).includes(lang.id));
        },
    },
    methods: {
        addLabel() {
            this.labels[this.newlang] = this.newlabel;
            this.newlabel = '';
            this.newlang = 'default';
            this.$emit('change', this.labels);
        },
        changeLabel() {
            this.$emit('change', this.labels);
        },
        removeLabel(lang) {
            delete this.labels[lang];
            this.$forceUpdate();
            this.$emit('change', this.labels);
        },
        toggleShow() {
            this.show = !this.show;
        },
    },
};
</script>
<style>
.labels-form > .grid {
    display: grid;
    grid-template-columns: 70px 200px 30px;
    text-align: center;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
}
.labels-form> .grid > span {
    padding: 4px 8px;
    white-space: normal;
}
</style>
