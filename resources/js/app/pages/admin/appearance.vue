<template>
    <div class="module-index">
        <details v-for="property,propertyKey in configs" :key="propertyKey">
            <summary>{{ title(propertyKey) }}</summary>
            <div class="main-container">
                <div>
                    <div :id="`container-${propertyKey}`" class="jsoneditor-container"></div>
                    <json-editor :options="jsonEditorOptions" :target="`container-${propertyKey}`" :text="JSON.stringify(property, null, 2)" />
                </div>
                <div class="ml-05">
                    <button class="button button-primary button-primary-hover-module-admin is-width-auto" :class="loading && loadingKey === propertyKey ? 'is-loading-spinner' : ''" @click.prevent="save(propertyKey)">
                        <app-icon icon="carbon:save"></app-icon>
                        <span class="ml-05">{{ msgSave }}</span>
                    </button>
                    <a class="button button-outlined" :href="wikis[propertyKey]" target="_blank">
                        <app-icon icon="carbon:launch"></app-icon>
                        <span class="ml-05">{{ msgSeeWikiForHelp }}</span>
                    </a>
                </div>
            </div>
        </details>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {

    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },

    props: {
        configkey: {
            type: String,
            default: '',
        },
        configs: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
            config: {
                type: String,
            },
            jsonEditorOptions: {
                mainMenuBar: true,
                mode: 'text',
                navigationBar: false,
                statusBar: false,
                readOnly: false,
            },
            loading: false,
            loadingKey: '',
            msgConfigurationKey: t`Configuration key`,
            msgSave: t`Save`,
            msgSeeWikiForHelp: t`See wiki for help`,
            wikis: {
                access_control: 'https://github.com/bedita/manager/wiki/Setup:-Module-Access-by-role',
                alert_message: 'https://github.com/bedita/manager/wiki/Setup:-Alert-Message',
                export: 'https://github.com/bedita/manager/wiki/Setup:-Export',
                modules: 'https://github.com/bedita/manager/wiki/Setup:-Modules-configuration',
                pagination: 'https://github.com/bedita/manager/wiki/Setup:-Pagination',
                project: 'https://github.com/bedita/manager/wiki/Setup:-Project',
                properties: 'https://github.com/bedita/manager/wiki/Setup:-Properties-display',
            },
        };
    },

    mounted() {
        this.config = this.configkey || 'alert_message';
    },

    methods: {
        async save(propertyName) {
            try {
                this.loading = true;
                this.loadingKey = propertyName;
                const element = document.getElementById(`container-${propertyName}`);
                const val = element.jsonEditor.get();
                const propertyValue = val.text === '' ? null : JSON.parse(val.text);
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: JSON.stringify({
                        property_name: propertyName,
                        property_value: propertyValue,
                    })
                };
                const response = await fetch(`${BEDITA.base}/admin/appearance/save`, options);
                const responseJson = await response.json();
                if (responseJson.error) {
                    BEDITA.error(responseJson.error);
                }
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
                this.loadingKey = '';
            }
        },
        title(pKey) {
            const k = this.$helpers.humanize(pKey);

            return this.t(k);
        },
    },
};
</script>
<style>
details>summary {
  list-style-type: none;
  outline: none;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  padding: 10px;
  font-size: 1rem;
}

details>summary::-webkit-details-marker {
  display: none;
}

details>summary::before {
  content: '+ ';
  font-size: 1.2rem;
}

details[open]>summary::before {
  content: '- ';
  font-size: 1.2rem;
}

details[open]>summary {
  margin-bottom: 0.5rem;
}

.main-container {
  display: flex;
  justify-content: left;
  align-items: flex-start;
  margin-bottom: 1rem;
}
</style>