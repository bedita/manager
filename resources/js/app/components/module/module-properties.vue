<template>
    <div class="module-properties">
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click="toggle"
        >
            <h2><span v-if="open">-</span><span v-else>+</span> <span>{{ msgProperties }}</span></h2>
        </header>
        <template v-if="open">
            <div
                class="input field"
                v-for="field in fields"
                :key="field"
            >
                <label :for="field">
                    {{ field }}
                    <span
                        class="sample"
                        v-html="samples[field]"
                    />
                </label>
                <div>
                    <div
                        :id="`container-${field}`"
                        class="jsoneditor-container"
                    />
                    <json-editor
                        :name="field"
                        :options="jsonEditorOptions"
                        :target="`container-${field}`"
                        :text="map[field] ? JSON.stringify(map[field], null, 2) : ''"
                        @change="change"
                    />
                </div>
            </div>
            <div>
                <template v-if="success">
                    <button @click.prevent="skip">
                        <app-icon
                            icon="carbon:checkmark"
                            color="green"
                        />
                        <span class="ml-05">
                            {{ msgSuccess }}
                        </span>
                    </button>
                </template>
                <template v-else>
                    <button
                        :class="{'is-loading-spinner': loading}"
                        :disabled="jsonVal === originalVal"
                        @click.prevent="save"
                    >
                        <app-icon icon="carbon:save" />
                        <span class="ml-05">
                            {{ msgSave }}
                        </span>
                    </button>
                </template>
            </div>
        </template>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ModuleProperties',
    components: {
        JsonEditor: () => import(/* webpackChunkName: "json-editor" */'app/components/json-editor/json-editor'),
    },
    inject: ['getCSFRToken'],
    props: {
        module: {
            type: Object,
            required: true,
        },
        objectType: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            fields: [
                'bulk',
                'fastCreate',
                'filter',
                'index',
                'options',
                'relations',
                'view',
            ],
            jsonEditorOptions: {
                mainMenuBar: true,
                mode: 'text',
                navigationBar: false,
                statusBar: false,
                readOnly: false,
            },
            loading: false,
            map: {
                bulk: '',
                fastCreate: '',
                filter: '',
                index: '',
                options: '',
                relations: '',
                view: '',
            },
            samples: {
                bulk: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#bulk" target="_new">wikidoc</a>',
                fastCreate: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#fastcreate" target="_new">wikidoc</a>',
                filter: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#filter" target="_new">wikidoc</a>',
                index: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#index" target="_new">wikidoc</a>',
                options: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#options" target="_new">wikidoc</a>',
                relations: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#relations" target="_new">wikidoc</a>',
                view: 'look at <a href="https://github.com/bedita/manager/wiki/Setup:-Properties-display#view" target="_new">wikidoc</a>',
            },
            msgProperties: t`Properties`,
            msgSave: t`Save`,
            msgSuccess: t`Done`,
            open: false,
            originalVal: null,
            success: false,
        };
    },
    computed: {
        jsonVal() {
            const val = {};
            const keys = Object.keys(this.map);
            keys.forEach((key) => {
                if (this.map[key] === '') {
                    val[key] = null;
                } else {
                    if (typeof this.map[key] === 'string') {
                        val[key] = JSON.parse(this.map[key]);
                    } else {
                        val[key] = JSON.parse(JSON.stringify(this.map[key]));
                    }
                }
            });

            return JSON.stringify(val);
        },
    },
    mounted() {
        this.$nextTick(() => {
            this.originalVal = JSON.stringify(this.module);
            this.fields.forEach((field) => {
                this.map[field] = this.module[field] || '';
            });
        });
    },
    methods: {
        change(field, val, prevVal, validation) {
            if (validation?.validationErrors?.length === 0) {
                this.map[field] = val?.text;
            }
        },
        async save() {
            try {
                this.loading = true;
                const payload = {
                    configurationKey: `Properties.${this.objectType}`,
                };
                const keys = Object.keys(this.map);
                keys.forEach((key) => {
                    if (this.map[key] === '') {
                        payload[key] = null;
                    } else {
                        if (typeof this.map[key] === 'string') {
                            payload[key] = JSON.parse(this.map[key]);
                        } else {
                            payload[key] = JSON.parse(JSON.stringify(this.map[key]));
                        }
                    }
                });
                const url = `/${this.objectType}/setup`;
                const body = JSON.stringify(payload);
                const options = {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': this.getCSFRToken(),
                    },
                    body,
                };
                const response = await fetch(url, options);
                const responseJson = await response.json();
                if (responseJson?.error) {
                    throw new Error(responseJson.error);
                }
                this.success = true;
                delete payload.configurationKey;
                this.originalVal = JSON.stringify(payload);
                setTimeout(() => {
                    this.$nextTick(() => {
                        this.success = false;
                    });
                }, 2000);
            } catch (e) {
                BEDITA.error(e);
            } finally {
                this.loading = false;
            }
        },
        skip() {
            // do nothing
        },
        toggle() {
            this.open = !this.open;
        },
    },
};
</script>
<style scoped>
div.module-properties {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    width: 800px;
    margin-top: 1rem;
    padding-top: 1rem;
}
div.module-properties .input {
    display: flex;
    flex-direction: column;
}
div.module-properties .sample {
    font-size: 0.8rem;
    color: #999;
}
div.module-properties .tab {
    cursor: pointer;
    border-bottom: solid #FFF 1px;
}
</style>
