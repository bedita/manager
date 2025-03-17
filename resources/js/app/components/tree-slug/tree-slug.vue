<template>
    <div class="tree-slug">
        <template v-if="mode === 'view'">
            {{ slugPathCompact }}
        </template>
        <template v-if="mode === 'edit'">
            <template v-if="!editing && v === slugContent">
                <span class="slugPath">{{ slugPathCompact }}/{{ v }}</span>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="editing = true"
                >
                    <app-icon icon="carbon:edit" />
                </button>
            </template>
            <template v-if="!editing && v !== slugContent">
                <span class="slugPath">{{ slugPathCompact }}/{{ v }}</span>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="save"
                >
                    <app-icon icon="carbon:save" />
                </button>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="v = slugContent; editing = false"
                >
                    <app-icon icon="carbon:reset" />
                </button>
            </template>
            <template v-if="editing">
                <span class="slugPath">{{ slugPathCompact }}/</span>
                <input
                    type="text"
                    v-model="v"
                >
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="editing = false"
                >
                    <app-icon icon="carbon:checkmark" />
                </button>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="v = slugContent; editing = false"
                >
                    <app-icon icon="carbon:close" />
                </button>
            </template>
        </template>
    </div>
</template>
<script>
export default {
    name: 'TreeSlug',
    props: {
        mode: {
            type: String,
            required: true
        },
        objectId: {
            type: String,
            required: true
        },
        objectType: {
            type: String,
            required: true
        },
        slugContent: {
            type: String,
            default: null
        },
        slugPath: {
            type: Array,
            default: () => []
        },
        slugPathCompact: {
            type: String,
            default: null
        },
    },
    data() {
        return {
            editing: false,
            v: this.slugContent,
        }
    },
    methods: {
        save() {
            console.log('TODO: save', this.slugContent);
            this.editing = false;
        },
    },
}
</script>
<style scoped>
.tree-slug {
    padding: 0.5rem;
    margin: 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-family: Arial, sans-serif;
    width: 100%;
    display: flex;
    flex-direction: row;
}
.tree-slug .slugPath {
    white-space: nowrap;
    border-bottom: 1px dotted #ccc;
}
.tree-slug input {
    border: 1px solid #ccc;
    border-radius: 4px;
    height: 1rem;
    width: fit-content !important;
    field-sizing: content;
}
.tree-slug button {
    cursor: pointer;
    height: 1rem;
}
</style>
