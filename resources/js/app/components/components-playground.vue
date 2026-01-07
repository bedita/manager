<template>
    <div class="components-playground">
        <div class="header">
            <h3 class="title">🧪 Vue Components</h3>
        </div>

        <div class="content">
            <!-- KeyValueList -->
            <div class="box">
                <div class="box-header" @click="toggleSection('keyValueList')">
                    <h4 class="box-title">KeyValueList</h4>
                    <button class="section-toggle-btn">
                        {{ sections.keyValueList ? '▼' : '▶' }}
                    </button>
                </div>
                <div v-if="sections.keyValueList" class="box-content">
                    <p class="box-description">
                        Expected: <code>"Custom Properties"</code> from "custom_properties"
                    </p>
                    <key-value-list
                        name="migration_test_keyvalue"
                        label="custom_properties"
                        :value="keyValueListValue"
                    />

                    <div class="source-code-toggle">
                        <button @click="toggleSource('keyValueList')" class="source-toggle-btn">
                            {{ showSource.keyValueList ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre v-if="showSource.keyValueList" class="source-code"><code>&lt;key-value-list
    name="migration_test_keyvalue"
    label="custom_properties"
    :value="keyValueListValue"
/&gt;

data() {
    return {
        keyValueListValue: '{"key1": "value1", "key2": "value2"}'
    };
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Label is humanized (underscores → spaces, Title Case)</li>
                            <li>Add/Remove buttons work</li>
                            <li>No console errors (F12 → Console)</li>
                            <li>No "unknown filter" warnings</li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- String List -->
            <div class="box">
                <div class="box-header" @click="toggleSection('stringList')">
                    <h4 class="box-title">String List</h4>
                    <button class="section-toggle-btn">
                        {{ sections.stringList ? '▼' : '▶' }}
                    </button>
                </div>
                <div v-if="sections.stringList" class="box-content">
                    <p class="box-description">
                        Expected: <code>"My Test Field"</code> from "my_test_field"
                    </p>
                    <string-list
                        name="migration_test_string"
                        label="my_test_field"
                        :value="stringListValue"
                    />

                    <div class="source-code-toggle">
                        <button @click="toggleSource('stringList')" class="source-toggle-btn">
                            {{ showSource.stringList ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre v-if="showSource.stringList" class="source-code"><code>&lt;string-list
    name="migration_test_string"
    label="my_test_field"
    :value="stringListValue"
/&gt;

data() {
    return {
        stringListValue: '["test item 1", "test item 2"]'
    };
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Label is humanized (underscores → spaces, Title Case)</li>
                            <li>Add button works</li>
                            <li>No console errors (F12 → Console)</li>
                            <li>No "unknown filter" warnings</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Object Properties (Event Bus Migration) -->
            <div class="box">
                <div class="box-header" @click="toggleSection('objectProperties')">
                    <h4 class="box-title">Object Properties (Event Bus → Props/Emits)</h4>
                    <button class="section-toggle-btn">
                        {{ sections.objectProperties ? '▼' : '▶' }}
                    </button>
                </div>
                <div v-if="sections.objectProperties" class="box-content">
                    <p class="box-description">
                        Tests parent-child event communication (migrated from EventBus)
                    </p>

                    <object-properties
                        :init-properties="objectProperties"
                        type="custom"
                        :hidden="[]"
                        :translatable="[]"
                        :translation-rules="[]"
                    >
                        <template #default="{ onPropAdded }">
                            <object-property-add
                                :prop-types="propTypesOptions"
                                @prop-added="onPropAdded"
                            />
                        </template>
                    </object-properties>

                    <input id="addedProperties" type="hidden">
                    <input id="hidden" type="hidden">
                    <input id="translationRules" type="hidden">

                    <div class="source-code-toggle">
                        <button @click="toggleSource('objectProperties')" class="source-toggle-btn">
                            {{ showSource.objectProperties ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre v-if="showSource.objectProperties" class="source-code"><code>// Parent component (object-properties.vue)
&lt;template&gt;
    &lt;slot @prop-added="onPropAdded"&gt;&lt;/slot&gt;
&lt;/template&gt;

// Usage - child automatically emits to parent via slot
&lt;object-properties type="custom"&gt;
    &lt;object-property-add :prop-types="types" /&gt;
&lt;/object-properties&gt;

// Child component (object-property-add.vue)
emits: ['prop-added'],
methods: {
    add() {
        this.$emit('prop-added', { name, property_type_name, description });
    }
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Add property form appears</li>
                            <li>New properties appear in list after clicking Add</li>
                            <li>No EventBus usage (check console for errors)</li>
                            <li>Props/emits pattern works correctly</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: 'ComponentsPlayground',

    components: {
        KeyValueList: () => import(/* webpackChunkName: "key-value-list" */ 'app/components/json-fields/key-value-list'),
        StringList: () => import(/* webpackChunkName: "string-list" */ 'app/components/json-fields/string-list'),
        ObjectProperties: () => import(/* webpackChunkName: "object-properties" */ 'app/components/object-property/object-properties'),
        ObjectPropertyAdd: () => import(/* webpackChunkName: "object-property-add" */ 'app/components/object-property/object-property-add'),
    },

    data() {
        return {
            sections: {
                stringList: false,
                keyValueList: false,
                objectProperties: false,
            },
            showSource: {
                stringList: false,
                keyValueList: false,
                objectProperties: false,
            },
            stringListValue: '["test item 1", "test item 2"]',
            keyValueListValue: '{"key1": "value1", "key2": "value2"}',
            objectProperties: [],
            propTypesOptions: [
                { value: 'string', text: 'String' },
                { value: 'integer', text: 'Integer' },
                { value: 'boolean', text: 'Boolean' },
                { value: 'date', text: 'Date' },
            ],
            addedPropertiesJson: '[]',
        };
    },

    methods: {
        toggleSection(section) {
            this.sections[section] = !this.sections[section];
        },
        toggleSource(section) {
            this.showSource[section] = !this.showSource[section];
        },
    }
};
</script>

<style scoped>
.components-playground {
    background: #3c3f45;
    border: 1px solid #52565e;
    border-radius: 6px;
    padding: 20px;
    margin: 20px 0;
    color: #ffffff;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #52565e;
    padding-bottom: 15px;
}

.title {
    margin: 0;
    color: #ffffff;
    font-size: 1.1em;
    font-weight: 500;
}

.content {
    margin-top: 0;
}

.box {
    background: #2e3138;
    margin: 10px 0;
    border-radius: 4px;
    border: 1px solid #52565e;
    color: #ffffff;
}

.box-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    cursor: pointer;
    user-select: none;
}

.box-header:hover {
    background: #33363d;
}

.box-title {
    margin: 0;
    color: #ffffff;
    font-size: 1em;
    font-weight: 500;
}

.section-toggle-btn {
    background: transparent;
    border: none;
    color: #ffffff;
    font-size: 0.9em;
    cursor: pointer;
    padding: 0 8px;
}

.box-content {
    padding: 0 15px 15px 15px;
}

.box-description {
    font-size: 0.85em;
    color: #b8bcc4;
    margin: 0 0 10px 0;
}

.box-description code {
    background: #52565e;
    padding: 2px 6px;
    border-radius: 3px;
    color: #ffffff;
}

/* Ensure all nested content has proper text colors */
.box ::v-deep input,
.box ::v-deep textarea,
.box ::v-deep label,
.box ::v-deep div,
.box ::v-deep span,
.box ::v-deep button {
    color: inherit;
}

.box ::v-deep input,
.box ::v-deep textarea {
    color: #212529 !important;
    background: #f8f9fa;
    border: 1px solid #ced4da;
}

.box ::v-deep button {
    color: #212529 !important;
}

.source-code-toggle {
    margin: 15px 0 10px 0;
}

.source-toggle-btn {
    background: #3c3f45;
    border: 1px solid #52565e;
    color: #b8bcc4;
    padding: 6px 12px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85em;
}

.source-toggle-btn:hover {
    background: #52565e;
    color: #ffffff;
}

.source-code {
    background: #1e1e1e;
    border: 1px solid #52565e;
    border-radius: 4px;
    padding: 12px;
    margin: 10px 0;
    overflow-x: auto;
    font-size: 0.85em;
    line-height: 1.5;
}

.source-code code {
    color: #d4d4d4;
    font-family: 'Courier New', Courier, monospace;
}

.checklist {
    background: #2e3e4e;
    padding: 12px;
    margin: 15px 0 0 0;
    border-radius: 4px;
    border-left: 3px solid #4a90e2;
}

.checklist-title {
    color: #ffffff;
    font-size: 0.9em;
}

.checklist-items {
    margin: 8px 0 0 0;
    padding-left: 20px;
    color: #e0e0e0;
    font-size: 0.85em;
    line-height: 1.6;
}
</style>
