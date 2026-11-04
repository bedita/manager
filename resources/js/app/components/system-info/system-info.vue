<template>
    <div class="system-info">
        <div
            v-for="item,key of infos"
            :key="key"
            class="system-info-entry"
        >
            <template v-if="jsonKeys.includes(key)">
                <details class="fieldset">
                    <summary class="tab admin-section-header unselectable">
                        <h2>{{ key }}</h2>
                    </summary>
                    <div
                        :id="`container-${hash}-${key}`"
                        class="jsoneditor-container jse-theme-dark"
                    />
                    <json-editor
                        :name="`${hash}-${key}`"
                        :options="jsonEditorOptions"
                        :target="`container-${hash}-${key}`"
                        :text="JSON.stringify(item, null, 2)"
                    />
                </details>
            </template>
            <dl
                class="system-info-row"
                v-else
            >
                <dt>{{ key }}</dt>
                <dd>{{ item }}</dd>
            </dl>
        </div>
    </div>
</template>
<script>
import Vue from 'vue';
import 'vanilla-jsoneditor/themes/jse-theme-dark.css';

export default {
    name: 'SystemInfo',
    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },
    props: {
        data: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            hash: '',
            infos: {},
            jsonEditorOptions: {
                mainMenuBar: true,
                mode: 'text',
                navigationBar: false,
                statusBar: false,
                readOnly: true,
            },
            jsonKeys: [
                'Database',
                'Extensions',
                'Extensions info',
                'GET /home',
            ],
        }
    },

    mounted() {
        this.$nextTick(() => {
            this.infos = JSON.parse(this.data) || {};
            if (this.infos['Vuejs'] !== undefined) {
                this.infos['Vuejs'] = Vue.version;
            }
            this.hash = Math.random().toString(36).substring(2, 15);
        });
    },
}
</script>
<style scoped lang="scss">
@use "../../../../styles/variables";

.system-info {
    min-width: 0;

    .system-info-row {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        gap: variables.$gutter;
        margin: 0;
        padding: variables.$gutter;
        border-bottom: 1px solid variables.$gray-700;

        &:hover {
            background: rgba(variables.$white, .025);
        }

        dt {
            color: variables.$gray-300;
        }

        dd {
            min-width: 0;
            margin: 0;
            font-family: variables.$font-family-monospace;
            font-weight: variables.$font-weight-normal;
            overflow-wrap: anywhere;
        }
    }

    .fieldset {
        margin: variables.$gutter 0;
    }

    .jsoneditor-container {
        min-width: 0;
        max-width: 100%;
        margin-top: variables.$gutter;
    }

    @media (max-width: 567px) {
        .system-info-row {
            grid-template-columns: minmax(0, 1fr);
            gap: variables.$gutter * .5;
        }
    }
}
</style>
