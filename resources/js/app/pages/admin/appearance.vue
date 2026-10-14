<template>
    <div class="appearance module-index">
        <details
            v-for="property,propertyKey in configs"
            :key="propertyKey"
            class="fieldset"
        >
            <summary class="tab admin-section-header unselectable">
                <h2>{{ title(propertyKey) }}</h2>
            </summary>
            <div class="main-container">
                <div class="appearance-editor">
                    <div
                        :id="`container-${propertyKey}`"
                        class="jsoneditor-container jse-theme-dark"
                    />
                    <json-editor
                        :options="jsonEditorOptions"
                        :target="`container-${propertyKey}`"
                        :text="text(propertyKey, property)"
                    />
                </div>
                <div class="appearance-actions">
                    <button
                        class="button button-primary button-primary-hover-module-admin is-width-auto"
                        :class="loading && loadingKey === propertyKey ? 'is-loading-spinner' : ''"
                        @click.prevent="save(propertyKey)"
                    >
                        <app-icon icon="carbon:save" />
                        <span class="ml-05">{{ msgSave }}</span>
                    </button>
                    <a
                        class="button button-outlined"
                        :href="wikis[propertyKey]"
                        target="_blank"
                    >
                        <app-icon icon="carbon:launch" />
                        <span class="ml-05">{{ msgSeeWikiForHelp }}</span>
                    </a>
                </div>
            </div>
        </details>
    </div>
</template>
<script>
import { t } from 'ttag';
import 'vanilla-jsoneditor/themes/jse-theme-dark.css';

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
            if (!property) {
                return 'null';
            }
            if (propertyKey === 'modules') {
                const ordered = {};
                for (const key of Object.keys(property)) {
                    ordered[key] = {};
                    for (const subkey of Object.keys(property[key]).sort()) {
                        ordered[key][subkey] = property[key][subkey];
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
<style lang="scss">
@use "../../../../styles/variables";

.appearance {
    display: grid;
    width: 100%;
    max-width: 44rem;
    min-width: 0;

    > details {
        min-width: 0;
    }

    .main-container {
        display: grid;
        gap: variables.$gutter;
        min-width: 0;
        padding: variables.$gutter;
    }

    .appearance-editor,
    .jsoneditor-container {
        min-width: 0;
        max-width: 100%;
    }

    .appearance-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: variables.$gutter * .5;
        padding-top: variables.$gutter;
        border-top: 1px solid variables.$gray-700;
    }
}
</style>
