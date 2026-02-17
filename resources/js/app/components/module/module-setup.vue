
<template>
    <div class="module-setup">
        <header
            class="tab"
            :class="{'open': open}"
            :open="open"
            @click="toggle"
        >
            <h2><span v-if="open">-</span><span v-else>+</span> <span>{{ msgModule }}</span></h2>
        </header>
        <template v-if="open">
            <div class="grid">
                <div
                    v-for="field in otherFields"
                    :key="field"
                >
                    <label :for="field">
                        {{ field }}
                        <span
                            class="sample"
                            v-html="samples[field]"
                        />
                    </label>
                    <input
                        :id="field"
                        :name="field"
                        type="text"
                        v-model="map[field]"
                    >
                </div>
            </div>
            <div
                class="input field"
                v-for="field in jsonFields"
                :key="field"
            >
                <label :for="field">
                    {{ field }}
                    <span
                        class="sample"
                        v-html="samples[field]"
                    />
                </label>
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
            <div
                class="input checkbox"
                v-for="field in boolFields"
                :key="field"
            >
                <input
                    type="checkbox"
                    :name="field"
                    v-model="map[field]"
                >
                <label :for="field">
                    {{ field }}
                </label>
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
    name: 'ModuleSetup',
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
                'color',
                'icon',
                'shortLabel',
                'sort',
                'route',
                'sidebar',
                'annotations',
            ],
            jsonEditorOptions: {
                mainMenuBar: true,
                mode: 'text',
                navigationBar: false,
                statusBar: false,
                readOnly: false,
            },
            boolFields: ['annotations'],
            jsonFields: ['route', 'sidebar'],
            loading: false,
            otherFields: ['color', 'icon', 'shortLabel', 'sort'],
            map: {
                color: '',
                shortLabel: '',
                sort: '',
                icon: '',
                sidebar: '',
                route: '',
                annotations: false,
            },
            samples: {
                color: 'i.e. #FF0000',
                shortLabel: 'i.e. Usr',
                sort: 'i.e. -modified',
                icon: 'look at <a href="https://icon-sets.iconify.design" targer="_new">icon sets</a>',
                route: 'i.e. {"_name": "users:organizer"}',
                sidebar: 'i.e. {"index":[{"label":"Organizer","url":{"_name":"users:organizer"},"icon":"carbon:add"}]}',
            },
            msgModule: t`Module`,
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
                if (this.map[key] !== '') {
                    val[key] = this.map[key];
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
                    configurationKey: `Modules.${this.objectType}`,
                };
                const keys = Object.keys(this.map);
                keys.forEach((key) => {
                    if (this.map[key] === '') {
                        payload[key] = null;
                    } else if (this.jsonFields.includes(key)) {
                        if (typeof this.map[key] === 'string') {
                            payload[key] = JSON.parse(this.map[key]);
                        } else {
                            payload[key] = JSON.parse(JSON.stringify(this.map[key]));
                        }
                    } else {
                        payload[key] = this.map[key];
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
div.module-setup {
    display: flex;
    flex-direction: column;
    width: 800px;
}
div.module-setup .grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-gap: 1rem;
}
div.module-setup > div {
    margin-top: 1rem;
}
div.module-setup .input {
    display: flex;
    flex-direction: column;
}
div.module-setup .sample {
    font-size: 0.8rem;
    color: #999;
}
div.module-setup input {
    width: 150px;
}
div.module-setup .tab {
    cursor: pointer;
    border-bottom: solid #FFF 1px;
}
div.module-setup .checkbox {
    flex-direction: row;
    align-items: center;
}
div.module-setup .checkbox > label {
    margin-left: 0.5rem;
}
div.module-setup .checkbox > input {
    width: auto;
}
</style>
