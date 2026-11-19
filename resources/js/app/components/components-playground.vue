<template>
    <div class="components-playground">
        <div class="header">
            <h3 class="title">
                🧪 Vue Components
            </h3>
        </div>

        <div class="content">
            <!-- Ajax Login -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('ajaxLogin')"
                >
                    <h4 class="box-title">
                        Ajax Login
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.ajaxLogin ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.ajaxLogin"
                >
                    <p class="box-description">
                        Opens the login form in a modal iframe and listens for the successful login message.
                    </p>
                    <button class="playground-action-btn"
                            @click="openAjaxLogin"
                    >
                        Open Login Modal
                    </button>
                    <p class="box-description login-status"
                       v-if="ajaxLoginCompleted"
                    >
                        Login message received.
                    </p>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('ajaxLogin')"
                        >
                            {{ showSource.ajaxLogin ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.ajaxLogin"
                    ><code>import { AjaxLogin } from 'app/components/ajax-login/ajax-login.vue';

const loginModal = new AjaxLogin({
    propsData: {
        headerText: 'Login',
        message: 'Please log in to continue',
    },
});
loginModal.$on('login', onLogin);
loginModal.$mount();
document.body.appendChild(loginModal.$el);</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Login modal opens with the configured header and message</li>
                            <li>Close button and backdrop remove the modal cleanly</li>
                            <li>Successful login emits the <code>login</code> event</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Category Picker -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('categoryPicker')"
                >
                    <h4 class="box-title">
                        Category Picker
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.categoryPicker ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.categoryPicker"
                >
                    <p class="box-description">
                        Select one or more enabled categories and verify the emitted category objects.
                    </p>
                    <category-picker
                        id="playground-categories"
                        label="Categories"
                        form="components-playground"
                        :categories="categoryPickerOptions"
                        :initial-categories="categoryPickerInitialSelection"
                        @change="handleCategoryPickerChange"
                    />
                    <p class="box-description"
                       v-if="selectedCategories.length"
                    >
                        Selected: <code>{{ selectedCategoryLabels }}</code>
                    </p>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('categoryPicker')"
                        >
                            {{ showSource.categoryPicker ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.categoryPicker"
                    ><code>&lt;category-picker
    id="playground-categories"
    label="Categories"
    :categories="categoryPickerOptions"
    :initial-categories="categoryPickerInitialSelection"
    @change="handleCategoryPickerChange"
/&gt;</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Enabled categories are available for selection</li>
                            <li>Disabled categories are not shown</li>
                            <li>Initial category is selected</li>
                            <li>Change event reports the selected category objects</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Dashboard Search -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('dashboardSearch')"
                >
                    <h4 class="box-title">
                        Dashboard Search
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.dashboardSearch ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.dashboardSearch"
                >
                    <p class="box-description">
                        Search objects by text or open an object directly by ID.
                    </p>
                    <dashboard-search q="" />
                    <p class="box-description">
                        Search actions navigate to the matching application page.
                    </p>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('dashboardSearch')"
                        >
                            {{ showSource.dashboardSearch ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.dashboardSearch"
                    ><code>&lt;dashboard-search q="" /&gt;</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Text search requires at least three characters</li>
                            <li>Text search navigates to the objects page</li>
                            <li>ID search navigates directly to the object</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Dialog -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('dialog')"
                >
                    <h4 class="box-title">
                        Dialog
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.dialog ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.dialog"
                >
                    <p class="box-description">
                        Open a confirmation dialog and verify the result of each action.
                    </p>
                    <button class="playground-action-btn"
                            @click="openDialog"
                    >
                        Open Confirmation Dialog
                    </button>
                    <p class="box-description login-status"
                       v-if="dialogResult"
                    >
                        {{ dialogResult }}
                    </p>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('dialog')"
                        >
                            {{ showSource.dialog ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.dialog"
                    ><code>import { confirm } from 'app/components/dialog/dialog';

let dialog;
dialog = confirm(
    'Do you want to continue?',
    'Continue',
    () => {
        this.dialogResult = 'Confirmed';
        dialog.hide();
    },
);</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Confirmation dialog opens with the configured message</li>
                            <li>Confirm action reports the result and closes the dialog</li>
                            <li>Cancel and close actions remove the dialog cleanly</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Folder Picker -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('folderPicker')"
                >
                    <h4 class="box-title">
                        Folder Picker
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.folderPicker ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.folderPicker"
                >
                    <p class="box-description">
                        Select a folder and verify the selected folder object emitted by the component.
                    </p>
                    <folder-picker
                        id="playground-folder"
                        label="Folder"
                        form="components-playground"
                        @change="handleFolderPickerChange"
                    />
                    <p class="box-description login-status"
                       v-if="selectedFolder"
                    >
                        Selected: <code>{{ selectedFolder.label }}</code>
                    </p>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('folderPicker')"
                        >
                            {{ showSource.folderPicker ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.folderPicker"
                    ><code>&lt;folder-picker
    id="playground-folder"
    label="Folder"
    form="components-playground"
    @change="handleFolderPickerChange"
/&gt;</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Folder options load in the picker</li>
                            <li>Selecting a folder reports the selected folder object</li>
                            <li>Hidden form input tracks the selected folder</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- History Info -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('historyInfo')"
                >
                    <h4 class="box-title">
                        History Info
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.historyInfo ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.historyInfo"
                >
                    <p class="box-description">
                        Display a version history entry with its author, date, and changed fields.
                    </p>
                    <history-info
                        id="playground-history-id"
                        :meta="historyInfoMeta"
                        :cansave="historyInfoCanSave"
                    />

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('historyInfo')"
                        >
                            {{ showSource.historyInfo ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.historyInfo"
                    ><code>&lt;history-info
    id="playground-history-id"
    :meta="historyInfoMeta"
    :cansave="historyInfoCanSave"
/&gt;

data() {
    return {
        historyInfoMeta: {
            created: '2024-01-15T10:30:00Z',
            user_action: 'update',
            changed: {
                title: '&lt;strong&gt;Previous title&lt;/strong&gt; changed to Current title',
                description: 'Description was updated',
            },
            user: {
                attributes: {
                    name: 'Playground',
                    surname: 'User',
                    username: 'playground',
                },
            },
        },
    };
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Author and formatted date are displayed</li>
                            <li>Updated history action and changed fields are rendered</li>
                            <li>Restore action is available when saving is enabled</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KeyValueList -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('keyValueList')"
                >
                    <h4 class="box-title">
                        KeyValueList
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.keyValueList ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.keyValueList"
                >
                    <p class="box-description">
                        Expected: <code>"Custom Properties"</code> from "custom_properties"
                    </p>
                    <key-value-list
                        name="migration_test_keyvalue"
                        label="custom_properties"
                        :value="keyValueListValue"
                    />

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('keyValueList')"
                        >
                            {{ showSource.keyValueList ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.keyValueList"
                    ><code>&lt;key-value-list
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

            <!-- Index Cell -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('indexCell')"
                >
                    <h4 class="box-title">
                        Index Cell
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.indexCell ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.indexCell"
                >
                    <p class="box-description">
                        Render scalar values and related records in index table cells.
                    </p>
                    <div class="field-section">
                        <label class="field-label">Scalar value:</label>
                        <index-cell
                            prop="title"
                            :text="indexCellText"
                            :schema="indexCellSchema"
                            :settings="indexCellSettings"
                        />
                    </div>
                    <div class="field-section">
                        <label class="field-label">Related records:</label>
                        <index-cell
                            prop="related"
                            :related="indexCellRelated"
                            :related-fields="['title']"
                        />
                    </div>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('indexCell')"
                        >
                            {{ showSource.indexCell ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.indexCell"
                    ><code>&lt;index-cell
    prop="title"
    :text="indexCellText"
    :schema="indexCellSchema"
    :settings="indexCellSettings"
/&gt;

&lt;index-cell
    prop="related"
    :related="indexCellRelated"
    :related-fields="['title']"
/&gt;</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Scalar text is displayed in the cell</li>
                            <li>Copy icon appears when copy-to-clipboard is enabled</li>
                            <li>Related records display their configured fields</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>


            <!-- String List -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('stringList')"
                >
                    <h4 class="box-title">
                        String List
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.stringList ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.stringList"
                >
                    <p class="box-description">
                        Expected: <code>"My Test Field"</code> from "my_test_field"
                    </p>
                    <string-list
                        name="migration_test_string"
                        label="my_test_field"
                        :value="stringListValue"
                    />

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('stringList')"
                        >
                            {{ showSource.stringList ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.stringList"
                    ><code>&lt;string-list
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

            <!-- Login Password -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('loginPassword')"
                >
                    <h4 class="box-title">
                        Login Password
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.loginPassword ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.loginPassword"
                >
                    <p class="box-description">
                        Enter a password and toggle its visibility with the eye button.
                    </p>
                    <form class="login-password-demo"
                          action="/login"
                          @submit.prevent
                    >
                        <login-password />
                    </form>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('loginPassword')"
                        >
                            {{ showSource.loginPassword ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.loginPassword"
                    ><code>&lt;form action="/login"&gt;
    &lt;login-password /&gt;
&lt;/form&gt;</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Password input is rendered with the expected label</li>
                            <li>Visibility toggle is disabled until a password is entered</li>
                            <li>Visibility toggle switches between password and text input</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Object Properties (Event Bus Migration) -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('objectProperties')"
                >
                    <h4 class="box-title">
                        Object Properties (Event Bus → Props/Emits)
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.objectProperties ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.objectProperties"
                >
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

                    <input
                        id="addedProperties"
                        type="hidden"
                    >
                    <input
                        id="hidden"
                        type="hidden"
                    >
                    <input
                        id="translationRules"
                        type="hidden"
                    >

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('objectProperties')"
                        >
                            {{ showSource.objectProperties ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.objectProperties"
                    ><code>// Parent component (object-properties.vue)
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

            <!-- Object Types List -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('objectTypesList')"
                >
                    <h4 class="box-title">
                        Object Types List
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.objectTypesList ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.objectTypesList"
                >
                    <p class="box-description">
                        Select object types and inspect the component's hidden form values.
                    </p>
                    <object-types-list
                        side="playground"
                        :all="objectTypesListAll"
                        :selected="objectTypesListSelected"
                    />

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('objectTypesList')"
                        >
                            {{ showSource.objectTypesList ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.objectTypesList"
                    ><code>&lt;object-types-list
    side="playground"
    :all="objectTypesListAll"
    :selected="objectTypesListSelected"
/&gt;

// Hidden inputs generated by the component:
// current_playground and change_playground</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>All configured object types are displayed</li>
                            <li>Initially selected object types are checked</li>
                            <li>Checkbox changes update the selected values</li>
                            <li>Hidden current and change inputs are generated</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Placeholders (PlaceholderBus Migration) -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('placeholders')"
                >
                    <h4 class="box-title">
                        Placeholder System (EventBus → PlaceholderBus)
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.placeholders ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.placeholders"
                >
                    <p class="box-description">
                        Tests directive-component communication (migrated from EventBus to dedicated placeholderBus)
                    </p>

                    <div class="field-section">
                        <label class="field-label">Rich Text Editor (placeholder button disabled in playground):</label>
                        <textarea
                            id="richeditor-test"
                            name="richeditor_test"
                            v-model="richtextValue"
                            v-richeditor
                            @change="handleRichtextChange"
                        />
                        <p class="box-description"
                           style="margin-top: 8px;"
                        >
                            Note: To test placeholder detection, switch to "Source Code" view in the editor and add:<br>
                            <code>&lt;span data-placeholder="<strong>OBJECT_ID</strong>"&gt;&lt;!--BE-PLACEHOLDER.<strong>OBJECT_ID</strong>.--&gt;&lt;/span&gt;</code><br>
                            <strong>Replace OBJECT_ID with an existing image/media object ID from your database.</strong>
                        </p>
                    </div>

                    <div class="field-section">
                        <label class="field-label">Detected Placeholders:</label>
                        <placeholder-list
                            field="richeditor-test"
                            :value="richtextValue"
                            v-if="richtextValue"
                        />
                        <p class="box-description"
                           v-if="!hasPlaceholders"
                        >
                            No placeholders detected. Add the HTML structure shown above to test.
                        </p>
                    </div>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('placeholders')"
                        >
                            {{ showSource.placeholders ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.placeholders"
                    ><code>// placeholderBus.js - Simple event emitter for placeholder events
class PlaceholderBus {
    listen(event, callback) { /* ... */ }
    send(event, data) { /* ... */ }
}
export const placeholderBus = new PlaceholderBus();

// richeditor.js directive
import { placeholderBus } from 'app/components/placeholder-bus';

setup: (editor) => {
    editor.on('change', () => {
        placeholderBus.send('refresh-placeholders', {
            id: editor.id,
            content: editor.getContent()
        });
    });
}

placeholderBus.listen('replace-placeholder', (data) => {
    // Update placeholder in editor
});

// placeholder-list.vue component
import { placeholderBus } from 'app/components/placeholder-bus';

mounted() {
    placeholderBus.listen('refresh-placeholders', this.refresh);
}

// placeholder-params.vue component
import { placeholderBus } from 'app/components/placeholder-bus';

changeParams() {
    placeholderBus.send('replace-placeholder', {
        id, field, oldParams, newParams
    });
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Rich text editor loads correctly</li>
                            <li>PlaceholderBus imported (not EventBus)</li>
                            <li>Placeholder detection works when HTML contains comment syntax</li>
                            <li>No console errors</li>
                            <li>Note: Placeholder button disabled (requires real object/backend)</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Secret -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('secret')"
                >
                    <h4 class="box-title">
                        Secret
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.secret ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.secret"
                >
                    <p class="box-description">
                        Reveal, hide, and copy a masked secret value.
                    </p>
                    <secret :val="secretValue" />

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('secret')"
                        >
                            {{ showSource.secret ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.secret"
                    ><code>&lt;secret :val="secretValue" /&gt;

data() {
    return {
        secretValue: 'Playground secret value',
    };
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Secret starts masked</li>
                            <li>View and hide controls toggle the value</li>
                            <li>Copy control copies the secret value</li>
                            <li>No console errors</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Autosize Textarea -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('autosizeTextarea')"
                >
                    <h4 class="box-title">
                        Autosize Textarea
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.autosizeTextarea ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.autosizeTextarea"
                >
                    <p class="box-description">
                        Grow the textarea as content is entered.
                    </p>
                    <autosize-textarea v-model="autosizeTextareaValue" />
                    <p class="box-description">
                        Current value: <code>{{ autosizeTextareaValue }}</code>
                    </p>
                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('autosizeTextarea')"
                        >
                            {{ showSource.autosizeTextarea ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>
                    <pre class="source-code"
                         v-if="showSource.autosizeTextarea"
                    ><code>&lt;autosize-textarea v-model="autosizeTextareaValue" /&gt;</code></pre>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Textarea grows with multiline content</li>
                            <li>Input event updates the displayed value</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Date Input -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('dateInput')"
                >
                    <h4 class="box-title">
                        Date Input
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.dateInput ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.dateInput"
                >
                    <p class="box-description">
                        Activate the datepicker on a native input.
                    </p>
                    <input
                        id="playground-date"
                        type="text"
                        date="true"
                        value="2026-08-24"
                        v-datepicker
                    >
                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('dateInput')"
                        >
                            {{ showSource.dateInput ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>
                    <pre class="source-code"
                         v-if="showSource.dateInput"
                    ><code>&lt;input
    id="playground-date"
    type="text"
    date="true"
    value="2026-08-24"
    v-datepicker
&gt;</code></pre>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Datepicker opens from the input</li>
                            <li>Configured date is displayed correctly</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Email Input -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('emailInput')"
                >
                    <h4 class="box-title">
                        Email Input
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.emailInput ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.emailInput"
                >
                    <p class="box-description">
                        Validate an email address and use the mail action.
                    </p>
                    <input
                        id="playground-email"
                        class="email"
                        type="text"
                        value="editor@example.com"
                        v-email
                    >
                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('emailInput')"
                        >
                            {{ showSource.emailInput ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>
                    <pre class="source-code"
                         v-if="showSource.emailInput"
                    ><code>&lt;input
    id="playground-email"
    class="email"
    type="text"
    value="editor@example.com"
    v-email
&gt;</code></pre>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Email value is validated</li>
                            <li>Mail action appears for a valid address</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- JSON Editor Input -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('jsonEditorInput')"
                >
                    <h4 class="box-title">
                        JSON Editor Input
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.jsonEditorInput ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.jsonEditorInput"
                >
                    <p class="box-description">
                        Edit structured JSON through the jsoneditor directive.
                    </p>
                    <textarea
                        id="playground-json-editor"
                        v-jsoneditor
                    >{"title":"Example","tags":["one","two"]}</textarea>
                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('jsonEditorInput')"
                        >
                            {{ showSource.jsonEditorInput ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>
                    <pre class="source-code"
                         v-if="showSource.jsonEditorInput"
                    ><code>&lt;textarea
    id="playground-json-editor"
    v-jsoneditor
&gt;{"title":"Example","tags":["one","two"]}&lt;/textarea&gt;</code></pre>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>JSON editor loads with the fixture data</li>
                            <li>Valid JSON can be edited and formatted</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Staggered List -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('staggeredList')"
                >
                    <h4 class="box-title">
                        Staggered List
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.staggeredList ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.staggeredList"
                >
                    <p class="box-description">
                        Reveal slotted list items with a staggered transition.
                    </p>
                    <staggered-list :stagger="100">
                        <li
                            key="first"
                            data-index="0"
                        >
                            <div class="source-code-toggle">
                                <button class="source-toggle-btn"
                                        @click="toggleSource('staggeredList')"
                                >
                                    {{ showSource.staggeredList ? '▼' : '▶' }} View Source Code
                                </button>
                            </div>
                            <pre class="source-code"
                                 v-if="showSource.staggeredList"
                            ><code>&lt;staggered-list :stagger="100"&gt;
    &lt;li key="first" data-index="0"&gt;First item&lt;/li&gt;
    &lt;li key="second" data-index="1"&gt;Second item&lt;/li&gt;
    &lt;li key="third" data-index="2"&gt;Third item&lt;/li&gt;
&lt;/staggered-list&gt;</code></pre>
                            First item
                        </li>
                        <li
                            key="second"
                            data-index="1"
                        >
                            Second item
                        </li>
                        <li
                            key="third"
                            data-index="2"
                        >
                            Third item
                        </li>
                    </staggered-list>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>List items enter in sequence</li>
                            <li>Each item uses its data-index delay</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- URI Input -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('uriInput')"
                >
                    <h4 class="box-title">
                        URI Input
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.uriInput ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.uriInput"
                >
                    <p class="box-description">
                        Normalize and validate a URI on change.
                    </p>
                    <input
                        id="playground-uri"
                        type="text"
                        value="example.com/docs"
                        v-uri
                    >
                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('uriInput')"
                        >
                            {{ showSource.uriInput ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>
                    <pre class="source-code"
                         v-if="showSource.uriInput"
                    ><code>&lt;input
    id="playground-uri"
    type="text"
    value="example.com/docs"
    v-uri
&gt;</code></pre>
                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>URI value is validated</li>
                            <li>Missing HTTPS scheme is normalized on change</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Coordinates/Map (Provide/Inject EventBus Migration) -->
            <div class="box">
                <div class="box-header"
                     @click="toggleSection('coordinates')"
                >
                    <h4 class="box-title">
                        CoordinatesView / MapView (EventBus → Provide/Inject)
                    </h4>
                    <button class="section-toggle-btn">
                        {{ sections.coordinates ? '▼' : '▶' }}
                    </button>
                </div>
                <div class="box-content"
                     v-if="sections.coordinates"
                >
                    <p class="box-description">
                        Tests sibling component communication (migrated from EventBus using provide/inject)
                    </p>

                    <div>
                        <label class="field-label">Address Fields (for geocoding) + Coordinates (long,lat):</label>

                        <div class="address-grid">
                            <input id="address"
                                   type="text"
                                   placeholder="Address (e.g., Via del Corso)"
                                   class="address-input"
                            >
                            <input id="locality"
                                   type="text"
                                   placeholder="Locality (e.g., Roma)"
                                   class="address-input"
                            >
                            <input id="zipcode"
                                   type="text"
                                   placeholder="Zipcode (e.g., 00186)"
                                   class="address-input"
                            >
                            <input id="country"
                                   type="text"
                                   placeholder="Country (e.g., Italy)"
                                   class="address-input"
                            >
                            <input id="region"
                                   type="text"
                                   placeholder="Region (e.g., Lazio)"
                                   class="address-input"
                            >
                            <coordinates-view
                                :coordinates="currentCoordinates"
                                :options="mapOptions"
                            />
                        </div>
                    </div>

                    <div class="field-section"
                         v-if="mapboxToken"
                    >
                        <label class="field-label">Map View (drag marker to update coordinates):</label>
                        <map-view
                            lng="12.4964"
                            lat="41.9028"
                            popup-html="Rome, Italy"
                            :map-token="mapboxToken"
                        />
                    </div>

                    <div class="field-section"
                         v-else
                    >
                        <label class="field-label">Map View:</label>
                        <div class="map-placeholder">
                            <div class="map-placeholder-content">
                                <p class="map-icon">
                                    🗺️
                                </p>
                                <p class="map-message">
                                    Map requires Mapbox token
                                </p>
                                <p class="map-hint">
                                    Configure in app_local.php: Maps.mapbox.token
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="source-code-toggle">
                        <button class="source-toggle-btn"
                                @click="toggleSource('coordinates')"
                        >
                            {{ showSource.coordinates ? '▼' : '▶' }} View Source Code
                        </button>
                    </div>

                    <pre class="source-code"
                         v-if="showSource.coordinates"
                    ><code>// Parent provides coordination (components-playground.vue)
provide() {
    return {
        onCoordinatesUpdate: (coords) => {
            this.coordinatesListeners.forEach(fn => fn(coords));
        },
        registerCoordinatesListener: (callback) => {
            this.coordinatesListeners.push(callback);
        }
    };
}

// Map component injects callback (map-view.vue)
inject: ['onCoordinatesUpdate'],
marker.on('dragend', () => {
    if (this.onCoordinatesUpdate) {
        this.onCoordinatesUpdate(marker.getLngLat());
    }
});

// Coordinates component injects registration (coordinates-view.vue)
inject: ['registerCoordinatesListener'],
mounted() {
    if (this.registerCoordinatesListener) {
        this.registerCoordinatesListener((point) => {
            this.value = `${point.lng}, ${point.lat}`;
        });
    }
}</code></pre>

                    <div class="checklist">
                        <strong class="checklist-title">✓ Verification</strong>
                        <ul class="checklist-items">
                            <li>Coordinates input displays correctly</li>
                            <li>When map marker is dragged, coordinates input updates</li>
                            <li>No EventBus usage (check console for errors)</li>
                            <li>Provide/inject pattern works for sibling components</li>
                            <li>Both components have backward compatibility fallbacks</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { AjaxLogin } from 'app/components/ajax-login/ajax-login.vue';
import { confirm } from 'app/components/dialog/dialog';

export default {
    name: 'ComponentsPlayground',

    components: {
        AutosizeTextarea: () => import(/* webpackChunkName: "autosize-textarea" */ 'app/components/autosize-textarea'),
        CategoryPicker: () => import(/* webpackChunkName: "category-picker" */ 'app/components/category-picker/category-picker'),
        DashboardSearch: () => import(/* webpackChunkName: "dashboard-search" */ 'app/components/dashboard-search'),
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */ 'app/components/folder-picker/folder-picker'),
        HistoryInfo: () => import(/* webpackChunkName: "history-info" */ 'app/components/history/history-info'),
        IndexCell: () => import(/* webpackChunkName: "index-cell" */ 'app/components/index-cell/index-cell'),
        LoginPassword: () => import(/* webpackChunkName: "login-password" */ 'app/components/login-password/login-password'),
        Secret: () => import(/* webpackChunkName: "secret" */ 'app/components/secret/secret'),
        StaggeredList: () => import(/* webpackChunkName: "staggered-list" */ 'app/components/staggered-list'),
        KeyValueList: () => import(/* webpackChunkName: "key-value-list" */ 'app/components/json-fields/key-value-list'),
        StringList: () => import(/* webpackChunkName: "string-list" */ 'app/components/json-fields/string-list'),
        ObjectProperties: () => import(/* webpackChunkName: "object-properties" */ 'app/components/object-property/object-properties'),
        ObjectPropertyAdd: () => import(/* webpackChunkName: "object-property-add" */ 'app/components/object-property/object-property-add'),
        ObjectTypesList: () => import(/* webpackChunkName: "object-types-list" */ 'app/components/object-types-list/object-types-list'),
        CoordinatesView: () => import(/* webpackChunkName: "coordinates-view" */ 'app/components/coordinates-view'),
        MapView: () => import(/* webpackChunkName: "map-view" */ 'app/components/map-view'),
        PlaceholderList: () => import(/* webpackChunkName: "placeholder-list" */ 'app/components/placeholder-list/placeholder-list'),
    },

    provide() {
        return {
            onCoordinatesUpdate: (coords) => {
                this.coordinatesListeners.forEach(listener => listener(coords));
            },
            registerCoordinatesListener: (callback) => {
                this.coordinatesListeners.push(callback);
            },
        };
    },

    props: {
        googleMapsApiKey: {
            type: String,
            default: '',
        },
        mapboxToken: {
            type: String,
            default: '',
        },
        object: {
            type: Object,
            default: () => ({
                id: 999,
                type: 'documents',
                attributes: {
                    title: 'Playground Mock Document',
                    status: 'on'
                }
            }),
        },
    },

    data() {
        return {
            sections: {
                ajaxLogin: false,
                autosizeTextarea: false,
                categoryPicker: false,
                dashboardSearch: false,
                dateInput: false,
                dialog: false,
                emailInput: false,
                folderPicker: false,
                historyInfo: false,
                indexCell: false,
                jsonEditorInput: false,
                loginPassword: false,
                secret: false,
                staggeredList: false,
                stringList: false,
                uriInput: false,
                keyValueList: false,
                objectProperties: false,
                objectTypesList: false,
                placeholders: false,
                coordinates: false,
            },
            showSource: {
                ajaxLogin: false,
                autosizeTextarea: false,
                categoryPicker: false,
                dashboardSearch: false,
                dateInput: false,
                dialog: false,
                emailInput: false,
                folderPicker: false,
                historyInfo: false,
                indexCell: false,
                jsonEditorInput: false,
                loginPassword: false,
                secret: false,
                staggeredList: false,
                stringList: false,
                uriInput: false,
                keyValueList: false,
                objectProperties: false,
                objectTypesList: false,
                placeholders: false,
                coordinates: false,
            },
            stringListValue: '["test item 1", "test item 2"]',
            keyValueListValue: '{"key1": "value1", "key2": "value2"}',
            objectProperties: [],
            objectTypesListAll: ['documents', 'images', 'persons'],
            objectTypesListSelected: ['documents'],
            propTypesOptions: [
                { value: 'string', text: 'String' },
                { value: 'integer', text: 'Integer' },
                { value: 'boolean', text: 'Boolean' },
                { value: 'date', text: 'Date' },
            ],
            addedPropertiesJson: '[]',
            coordinatesListeners: [],
            currentCoordinates: 'POINT(12.4964 41.9028)',
            mapOptions: JSON.stringify({ key: this.googleMapsApiKey, url: 'https://maps.googleapis.com/maps/api/' }),
            richtextValue: '<p>Sample richtext content. Use the placeholder button to add placeholders.</p>',
            hasPlaceholders: false,
            ajaxLoginCompleted: false,
            dialogResult: '',
            autosizeTextareaValue: 'This textarea grows as you type.',
            categoryPickerOptions: [
                { id: 1, name: 'News', label: 'News', enabled: true },
                { id: 2, name: 'Events', label: 'Events', enabled: true },
                { id: 3, name: 'Archived', label: 'Archived', enabled: false },
            ],
            categoryPickerInitialSelection: [
                { name: 'News' },
            ],
            selectedCategories: [],
            selectedFolder: null,
            historyInfoCanSave: true,
            historyInfoMeta: {
                created: '2024-01-15T10:30:00Z',
                user_action: 'update',
                changed: {
                    title: '<strong>Previous title</strong> changed to Current title',
                    description: 'Description was updated',
                },
                user: {
                    attributes: {
                        name: 'Playground',
                        surname: 'User',
                        username: 'playground',
                    },
                },
            },
            indexCellText: 'Playground title',
            secretValue: 'Playground secret value',
            indexCellSchema: {
                type: 'string',
            },
            indexCellSettings: {
                copy2clipboard: true,
            },
            indexCellRelated: [
                {
                    id: 101,
                    type: 'documents',
                    attributes: {
                        title: 'Related document',
                    },
                },
                {
                    id: 102,
                    type: 'documents',
                    attributes: {
                        title: 'Another document',
                    },
                },
            ],
        };
    },

    computed: {
        selectedCategoryLabels() {
            return this.selectedCategories.map(category => category.label).join(', ');
        },
    },

    methods: {
        toggleSection(section) {
            this.sections[section] = !this.sections[section];
        },
        toggleSource(section) {
            this.showSource[section] = !this.showSource[section];
        },
        handleRichtextChange(event) {
            this.richtextValue = event.target.value;
            // Check if content has placeholder structure
            this.hasPlaceholders = this.richtextValue.includes('data-placeholder');
        },
        openAjaxLogin() {
            this.ajaxLoginCompleted = false;
            const loginModal = new AjaxLogin({
                propsData: {
                    headerText: 'Login',
                    message: 'Please log in to continue',
                },
            });
            loginModal.$on('login', () => {
                this.ajaxLoginCompleted = true;
            });
            loginModal.$mount();
            document.body.appendChild(loginModal.$el);
        },
        handleCategoryPickerChange(categories) {
            this.selectedCategories = categories;
        },
        handleFolderPickerChange(folder) {
            this.selectedFolder = folder;
        },
        openDialog() {
            this.dialogResult = '';
            let dialog;
            dialog = confirm(
                'Do you want to continue?',
                'Continue',
                () => {
                    this.dialogResult = 'Confirmed';
                    dialog.hide();
                },
            );
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
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #52565e;
}

.title {
    margin: 0;
    color: #ffffff;
    font-size: 1.1em;
    font-weight: 500;
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

.playground-action-btn {
    background: #4a90e2;
    border: 1px solid #6da8ed;
    border-radius: 4px;
    color: #ffffff;
    cursor: pointer;
    padding: 8px 12px;
}

.playground-action-btn:hover {
    background: #3478c5;
}

.login-password-demo {
    max-width: 420px;
}

.login-password-demo :deep(.is-flex) {
    align-items: stretch;
}

.login-password-demo :deep(.is-expanded) {
    flex: 1 1 auto;
    min-width: 0;
    width: auto;
}

.login-password-demo :deep(input) {
    width: 100%;
}

.login-password-demo :deep(button) {
    height: 100%;
}

.login-status {
    color: #8dd49a;
    margin-top: 10px;
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

.field-section {
    margin-bottom: 20px;
}

.field-label {
    display: block;
    margin-bottom: 8px;
    color: #b8bcc4;
    font-size: 0.9em;
}

.address-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}

.map-placeholder {
    height: 400px;
    background: #1e1e1e;
    border: 1px solid #52565e;
    border-radius: 4px;
    padding: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.map-placeholder-content {
    text-align: center;
    color: #b8bcc4;
}

.map-icon {
    margin: 0 0 10px 0;
}

.map-message {
    margin: 0;
    font-size: 0.9em;
}

.map-hint {
    margin: 5px 0 0 0;
    font-size: 0.85em;
    opacity: 0.7;
}
</style>
