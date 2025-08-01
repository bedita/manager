<template>
    <div class="system-info">
        <p
            v-for="item,key of infos"
            :key="key"
        >
            <template v-if="jsonKeys.includes(key)">
                <details>
                    <summary>{{ key }}</summary>
                    <div
                        :id="`container-${hash}-${key}`"
                        class="jsoneditor-container"
                    />
                    <json-editor
                        :name="`${hash}-${key}`"
                        :options="jsonEditorOptions"
                        :target="`container-${hash}-${key}`"
                        :text="JSON.stringify(item, null, 2)"
                    />
                </details>
            </template>
            <template v-else>
                <span class="key">{{ key }}</span>:
                <span class="version">{{ item }}</span>
            </template>
        </p>
    </div>
</template>
<script>
import Vue from 'vue';

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
                readOnly: false,
            },
            jsonKeys: [
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
<style>
span.key {
    font-style: italic;
    color: yellow;
}
span.version {
    font-family: 'Courier New', Courier, monospace;
}
</style>
