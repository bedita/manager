<template>
    <div class="tree-content">
        <header>
            <h2>
                <span><app-icon icon="carbon:document" /></span>
                <span class="tag is-smallest mx-05" :class="`has-background-module-${obj?.type}`">{{ obj?.type }}</span>
                <span
                    class="title-edit"
                    v-title="title"
                    v-if="editField === 'title'"
                >
                    <input
                        type="text"
                        v-model="title"
                        @click.prevent.stop="editField = 'title'"
                    >
                    <button
                        class="button button-primary"
                        @click.prevent.stop="saveTitle()"
                    >
                        <app-icon icon="carbon:save" />
                        <span class="ml-05">{{ msgSave }}</span>
                    </button>
                    <button
                        class="button button-primary"
                        @click.prevent.stop="undoTitle()"
                    >
                        <app-icon icon="carbon:reset" />
                        <span class="ml-05">{{ msgUndo }}</span>
                    </button>
                </span>
                <span
                    class="editable"
                    @click.prevent.stop="editField = 'title'"
                    @mouseover="hoverTitle=true"
                    @mouseleave="hoverTitle=false"
                    v-else
                >
                    {{ truncate(title, 80) }}
                    <template v-if="hoverTitle">
                        <app-icon icon="ph:pencil-fill" color="#00aaff" />
                    </template>
                </span>
                <span class="modified">{{ $helpers.formatDate(obj?.meta?.modified) }}</span>
            </h2>
        </header>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'TreeContent',
    props: {
        obj: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            editField: null,
            hoverTitle: false,
            title: this.obj?.attributes?.title || '',
            msgSave: t`Save`,
            msgUndo: t`Undo`,
        }
    },
    methods: {
        saveTitle() {
            console.log('Saving title:', this.title);
            this.editField = null;
        },
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
        undoTitle() {
            console.log('Undoing title change');
            this.editField = null;
        },
    },
}
</script>
<style scoped>
div.tree-content {
    padding-left: 0.5rem;
}
div.tree-content > header > h2 {
    display: flex;
    flex-direction: row;
    gap: 0.2rem;
    align-items: center;
    justify-content: start;
    border-bottom: dotted 0.1px silver;
}
div.tree-content > header > h2 > span.editable {
    font-size: 0.875rem;
}
div.tree-content > header > h2 > span.modified {
    font-size: 0.7rem;
}
div.tree-content .editable:hover {
    cursor: pointer;
    color: #00aaff;
    text-decoration: underline;
}
div.tree-content span.modified {
    margin-left: auto;
}
</style>
