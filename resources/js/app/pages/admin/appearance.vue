<template>
    <div class="appearance module-index">
        <details v-for="property,propertyKey in configs" :key="propertyKey">
            <summary>{{ title(propertyKey) }}</summary>
            <div class="main-container">
                <div>
                    <div :id="`container-${propertyKey}`" class="jsoneditor-container"></div>
                    <json-editor :options="jsonEditorOptions" :target="`container-${propertyKey}`" :text="text(propertyKey, property)" />
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
    name: 'AdminAppearance',

    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },

    props: {
        configs: {
            type: Object,
            default: () => ({}),
        },
    },

    data() {
        return {
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
        text(propertyKey, property) {
            if (propertyKey === 'modules') {
                const ordered = {};
                for (const key of Object.keys(property)) {
                    const child = property[key];
                    ordered[key] = {};
                    for (const subkey of Object.keys(child).sort()) {
                        ordered[key][subkey] = child[subkey];
                    }
                }

                return JSON.stringify(ordered, null, 2);
            }
            if (!['access_control', 'export', 'pagination', 'project', 'properties'].includes(propertyKey)) {
                return JSON.stringify(property, null, 2);
            }
            let data = {};
            for (const key of Object.keys(property).sort()) {
                if (propertyKey === 'properties') {
                    const ordered = {};
                    for (const k of Object.keys(property[key]).sort()) {
                        ordered[k] = property[key][k];
                    }
                    data[key] = ordered;
                } else {
                    data[key] = property[key];
                }
            }

            return JSON.stringify(data, null, 2);
        },
        title(pKey) {
            const k = this.$helpers.humanize(pKey);

            return this.t(k);
        },
    },
};
</script>
<style>
div.appearance > details>summary {
  list-style-type: none;
  outline: none;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  padding: 10px;
  font-size: 1rem;
}

div.appearance > details>summary::-webkit-details-marker {
  display: none;
}

div.appearance > details>summary::before {
  content: '+ ';
  font-size: 1.2rem;
}

div.appearance > details[open]>summary::before {
  content: '- ';
  font-size: 1.2rem;
}

div.appearance > details[open]>summary {
  margin-bottom: 0.5rem;
}

div.appearance > details > .main-container {
  display: flex;
  justify-content: left;
  align-items: flex-start;
  margin-bottom: 1rem;
}
</style>
