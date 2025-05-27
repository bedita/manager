<template>
    <div class="tree-content">
        <template v-if="createNew || editMode">
            <div
                class="backdrop"
                style="display: block; z-index: 9998;"
                @click="closePanel()"
            />
            <aside
                class="main-panel-container on"
                custom-footer="true"
                custom-header="true"
            >
                <div class="main-panel fieldset">
                    <header class="mx-1 mt-1 tab tab-static unselectable">
                        <h2 v-if="createNew">{{ msgCreateNew }}</h2>
                        <h2 v-if="editMode">{{ msgEdit }}</h2>
                        <button
                            class="button button-outlined close"
                            v-title="msgClose"
                            @click="closePanel()"
                        >
                            <app-icon icon="carbon:close" />
                        </button>
                    </header>
                    <div class="container">
                        TODO: form fields required + non required
                        <div class="buttons">
                            <button
                                class="button button-primary"
                                :class="{'is-loading-spinner': saving}"
                                :disabled="saveDisabled"
                                @click.prevent="save"
                            >
                                <app-icon icon="carbon:save" />
                                <span class="ml-05">
                                    {{ msgSave }}
                                </span>
                            </button>
                            <button
                                class="button button-primary"
                                @click="closePanel()"
                            >
                                <app-icon icon="carbon:close" />
                                <span class="ml-05">
                                    {{ msgClose }}
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </template>
        <header>
            <h2>
                <span><app-icon icon="carbon:document" /></span>
                <span
                    class="tag is-smallest mx-05"
                    :class="`has-background-module-${obj?.type}`"
                >
                    {{ obj?.type }}
                </span>
                <template v-if="canSave()">
                    <span
                        class="editable content"
                        :class="obj?.attributes?.status"
                        @click.prevent.stop="editMode = true"
                        @mouseover="hoverTitle=true"
                        @mouseleave="hoverTitle=false"
                    >
                        {{ truncate(title, 80) }}
                        <template v-if="hoverTitle">
                            <app-icon
                                icon="ph:pencil-fill"
                                color="#00aaff"
                            />
                        </template>
                    </span>
                </template>
                <template v-else>
                    <span
                        class="content"
                        :class="obj?.attributes?.status"
                        v-title="obj?.attributes?.title"
                    >
                        {{ truncate(obj?.attributes?.title, 80) }}
                    </span>
                </template>
                <div class="object-info-container">
                    <object-info
                        border-color="transparent"
                        color="white"
                        :object-data="obj"
                    />
                </div>
                <a
                    :href="`/view/${obj?.id}`"
                    target="_blank"
                    class="mr-05"
                    v-title="msgOpenInNewTab"
                >
                    <app-icon icon="carbon:launch" />
                </a>
                <span class="modified">{{ $helpers.formatDate(obj?.meta?.modified) }}</span>
            </h2>
        </header>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'TreeContent',
    components: {
        ObjectInfo: () => import(/* webpackChunkName: "object-info" */'app/components/object-info/object-info'),
    },
    props: {
        canSaveMap: {
            type: Object,
            required: true
        },
        obj: {
            type: Object,
            required: true
        },
    },
    data() {
        return {
            createNew: false,
            editMode: false,
            hoverTitle: false,
            title: this.obj?.attributes?.title || '',
            msgClose: t`Close`,
            msgCreateNew: t`Create new`,
            msgEdit: t`Edit`,
            msgOpenInNewTab: t`Open in new tab`,
            msgSave: t`Save`,
            msgUndo: t`Undo`,
        }
    },
    methods: {
        canSave() {
            return this.canSaveMap?.[this.obj?.type] || false;
        },
        closePanel() {
            this.createNew = false;
            this.editMode = false;
        },
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
    },
}
</script>
<style scoped>
div.tree-content {
    padding-left: 0.5rem;
}
div.tree-content aside.main-panel-container {
    z-index: 9999;
}
div.tree-content aside.main-panel {
    margin: 1rem;
    padding: 1rem;
}
div.tree-content button.close {
    border: solid transparent 0px;
    min-width: 36px;
    max-width: 36px;
}
div.tree-content .container {
    padding: 1rem;
    margin: auto;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
div.tree-content .container > div {
    display: flex;
    flex-direction: column;
}
div.tree-content div.buttons {
    display: flex;
    flex-direction: row;
    gap: 1rem;
}
div.tree-content > header > h2 {
    display: flex;
    flex-direction: row;
    gap: 0.2rem;
    align-items: center;
    justify-content: start;
    border-bottom: dotted 0.1px silver;
}
div.tree-content > header > h2 > span.off, div.tree-content > header > h2 > span.draft {
    color: #737c81;
}
div.tree-content > header > h2 > span.content {
    font-size: 0.875rem;
}
div.tree-content > header > h2 > span.modified, div.tree-content > header > h2 > a {
    font-size: 0.7rem;
}
div.tree-content .editable:hover {
    cursor: pointer;
    color: #00aaff;
    text-decoration: underline;
}
div.tree-content div.object-info-container {
    margin-left: auto;
}
</style>
