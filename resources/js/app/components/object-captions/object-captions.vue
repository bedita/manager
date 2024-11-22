<template>
    <div class="object-captions">
        <div
            class="captions-none"
            v-if="captions.length === 0"
        >
            {{ msgNoCaptions }}
        </div>
        <div
            class="captions-container"
            v-if="captions.length > 0"
        >
            <div class="column">
                {{ msgCaption }}
            </div>
            <div class="column">
                {{ msgTitle }}
            </div>
            <div class="column">
                {{ msgLang }}
            </div>
            <div class="column">
                {{ msgStatus }}
            </div>
            <div class="column" />
            <div class="column" />
            <div class="column" />
            <div class="column" />
        </div>
        <div
            v-for="(item, index) in captions"
            :key="index"
            :class="{'captions-container': item.mode == 'view', dragging: draggingIndex === index}"
            draggable="true"
            @dragstart="handleDragStart(index)"
            @dragend="draggingIndex = null"
            @dragover.prevent
            @dragenter="handleDragEnter(index)"
            @dragleave.prevent=""
            @drop="handleDrop(index)"
        >
            <template v-if="item.mode == 'view'">
                <div><span>{{ truncate(item.caption_text, 100) }}</span></div>
                <div><span>{{ item.label }}</span></div>
                <div><span>{{ languages[item.lang] }}</span></div>
                <div><span>{{ item.status }}</span></div>
                <div class="sort">
                    <app-icon icon="carbon:chevron-sort" />
                </div>
                <div>
                    <button
                        class="button button-outlined"
                        @click.prevent="item.mode = 'edit'"
                        v-if="!readonly && item.mode == 'view'"
                    >
                        <app-icon icon="carbon:edit" />
                        <span class="ml-05">{{ msgEdit }}</span>
                    </button>
                </div>
                <div>
                    <button
                        class="button button-outlined"
                        @click.prevent="remove(item)"
                        v-if="!readonly"
                    >
                        <app-icon icon="carbon:trash-can" />
                        <span class="ml-05">{{ msgRemove }}</span>
                    </button>
                </div>
                <div>
                    <span
                        class="ribbon has-background-module-media"
                        v-if="item.nuid"
                    >
                        {{ msgNew }}
                    </span>
                </div>
            </template>
            <template v-if="item.mode == 'edit'">
                <section class="fieldset caption-form-container mt-15">
                    <div class="tab-container">
                        <div class="input text">
                            <label for="captionTitle">{{ msgTitle }}</label>
                            <input
                                id="captionTitle"
                                type="text"
                                v-model="item.label"
                            >
                        </div>
                        <div class="input select">
                            <label for="captionLang">{{ msgLang }}</label>
                            <select
                                id="captionLang"
                                v-model="item.lang"
                            >
                                <option
                                    v-for="lang in langs"
                                    :key="lang.value"
                                    :value="lang.value"
                                >
                                    {{ lang.label }}
                                </option>
                            </select>
                        </div>
                        <div class="input select">
                            <label for="captionStatus">{{ msgStatus }}</label>
                            <select
                                id="captionStatus"
                                v-model="item.status"
                            >
                                <option
                                    v-for="status in statuses"
                                    :key="status.value"
                                    :value="status.value"
                                >
                                    {{ status.label }}
                                </option>
                            </select>
                        </div>
                        <div class="input select">
                            <label for="captionFormat">{{ msgFormat }}</label>
                            <select
                                id="captionFormat"
                                v-model="item.format"
                            >
                                <option
                                    v-for="format in formats"
                                    :key="format"
                                    :value="format"
                                >
                                    {{ format }}
                                </option>
                            </select>
                        </div>
                        <div class="input textarea">
                            <label for="captionText">{{ msgText }}</label>
                            <textarea
                                id="captionText"
                                rows="10"
                                v-model="item.caption_text"
                                @input="validate(item)"
                            />
                            <div
                                class="mt-05"
                                v-if="item?.errors"
                            >
                                <div v-if="item?.errors.length === 0">
                                    <app-icon
                                        icon="carbon:checkmark"
                                        color="green"
                                    />
                                    <span class="ml-05">{{ msgValid }}</span>
                                </div>
                                <div v-if="item?.errors.length > 0">
                                    <app-icon
                                        icon="carbon:error"
                                        color="red"
                                    />
                                    <span class="ml-05">{{ msgNotValid }}</span>
                                </div>
                                <ol class="validate-results">
                                    <li
                                        v-for="error in item.errors"
                                        :key="error.line"
                                    >
                                        Line {{ error.line }}: {{ error.message }}
                                    </li>
                                </ol>
                            </div>
                        </div>
                        <div>
                            <button
                                class="button button-secondary is-expanded"
                                :disabled="captionSaveDisabled(item)"
                                @click.prevent="item.mode = 'view'"
                                v-if="!readonly && item.mode == 'edit'"
                            >
                                <app-icon icon="carbon:checkmark" />
                                <span class="ml-05">{{ msgOk }}</span>
                            </button>
                        </div>
                        <div>
                            <button
                                class="button button-secondary is-expanded"
                                @click.prevent="undoChanges(item)"
                            >
                                <app-icon icon="carbon:reset" />
                                <span class="ml-05">{{ msgCancel }}</span>
                            </button>
                        </div>
                    </div>
                </section>
            </template>
        </div>
        <template v-if="viewMode == 'view'">
            <div
                class="mt-05"
                v-if="!readonly"
            >
                <div>
                    <button
                        class="button button-outlined"
                        @click.prevent="viewMode='new'"
                    >
                        <app-icon icon="carbon:edit" />
                        <span class="ml-05">{{ msgNewCaption }}</span>
                    </button>
                </div>
            </div>
        </template>
        <template v-if="viewMode == 'new'">
            <section class="fieldset caption-form-container mt-15">
                <div class="tab-container">
                    <div class="input text">
                        <label for="newCaptionTitle">{{ msgTitle }}</label>
                        <input
                            id="newCaptionTitle"
                            type="text"
                            v-model="newItem.label"
                        >
                    </div>
                    <div class="input select">
                        <label for="newCaptionLang">{{ msgLang }}</label>
                        <select
                            id="newCaptionLang"
                            v-model="newItem.lang"
                        >
                            <option
                                v-for="lang in langs"
                                :key="lang.value"
                                :value="lang.value"
                            >
                                {{ lang.label }}
                            </option>
                        </select>
                    </div>
                    <div class="input select">
                        <label for="newCaptionStatus">{{ msgStatus }}</label>
                        <select
                            id="newCaptionStatus"
                            v-model="newItem.status"
                        >
                            <option
                                v-for="status in statuses"
                                :key="status.value"
                                :value="status.value"
                            >
                                {{ status.label }}
                            </option>
                        </select>
                    </div>
                    <div class="input select">
                        <label for="newCaptionFormat">{{ msgFormat }}</label>
                        <select
                            id="newCaptionFormat"
                            v-model="newItem.format"
                        >
                            <option
                                v-for="format in formats"
                                :key="format"
                                :value="format"
                            >
                                {{ format }}
                            </option>
                        </select>
                    </div>
                    <div class="input textarea">
                        <label for="newCaptionText">{{ msgCaption }}</label>
                        <textarea
                            id="newCaptionText"
                            rows="10"
                            v-model="newItem.caption_text"
                            @input="validate(newItem)"
                        />
                        <div
                            class="mt-05"
                            v-if="newItem?.errors"
                        >
                            <div v-if="newItem?.errors.length === 0">
                                <app-icon
                                    icon="carbon:checkmark"
                                    color="green"
                                />
                                <span class="ml-05">{{ msgValid }}</span>
                            </div>
                            <div v-if="newItem?.errors.length > 0">
                                <app-icon
                                    icon="carbon:error"
                                    color="red"
                                />
                                <span class="ml-05">{{ msgNotValid }}</span>
                            </div>
                            <ol class="validate-results">
                                <li
                                    v-for="error in newItem.errors"
                                    :key="error.line"
                                >
                                    Line {{ error.line }}: {{ error.message }}
                                </li>
                            </ol>
                        </div>
                    </div>
                    <div>
                        <button
                            class="button button-secondary is-expanded"
                            :disabled="captionSaveDisabled(newItem)"
                            @click.prevent="add"
                        >
                            <app-icon icon="carbon:add" />
                            <span class="ml-05">{{ msgAdd }}</span>
                        </button>
                    </div>
                    <div>
                        <button
                            class="button button-secondary is-expanded"
                            @click.prevent="viewMode='view'"
                        >
                            <app-icon icon="carbon:reset" />
                            <span class="ml-05">{{ msgCancel }}</span>
                        </button>
                    </div>
                </div>
            </section>
        </template>

        <input
            type="hidden"
            name="captions"
            :value="captionsValue"
        >

        <div
            class="mt-1"
            v-if="!readonly"
        >
            <button
                class="button button-primary"
                :class="{'is-loading-spinner': loading}"
                :disabled="captionsValueStart === captionsValue && noErrors"
                @click.prevent="save"
            >
                <app-icon icon="carbon:save" />
                <span class="ml-05">{{ msgSave }}</span>
            </button>

            <button
                class="button button-primary"
                :disabled="captionsValueStart === captionsValue"
                @click.prevent="undoAllChanges()"
            >
                <app-icon icon="carbon:reset" />
                <span class="ml-05">{{ msgCancel }}</span>
            </button>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';
import { WebVTTParser } from 'webvtt-parser';

export default {
    name: 'ObjectCaptions',

    inject: ['getCSFRToken'],

    props: {
        objectId: {
            type: String,
            required: true,
        },
        objectType: {
            type: String,
            required: true,
        },
        config: {
            type: Object,
            default: () => {},
        },
        items: {
            type: Array,
            default: () => [],
        },
        languages: {
            type: Object,
            default: () => {},
        },
        readonly: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            loading: false,
            newItem: {},
            captions: [],
            captionsValueStart: '',
            draggingIndex: null,
            formats: [],
            isDraggable: false,
            langs: [],
            msgAdd: t`Add`,
            msgCancel: t`Cancel`,
            msgCaption: t`Caption`,
            msgEdit: t`Edit`,
            msgFormat: t`Format`,
            msgLang: t`Language`,
            msgNewCaption: t`New caption`,
            msgNew: t`New`,
            msgNoCaptions: t`No captions`,
            msgNotValid: t`Not valid`,
            msgOk: t`Ok`,
            msgParams: t`Parameters`,
            msgRemove: t`Remove`,
            msgSave: t`Save`,
            msgStatus: t`Status`,
            msgTitle: t`Title`,
            msgValid: t`Valid`,
            originalCaptions: [],
            statuses: [
                { value: 'on', label: 'on' },
                { value: 'draft', label: 'draft' },
                { value: 'off', label: 'off' },
            ],
            viewMode: 'view',
            webvttParser: null,
        };
    },

    computed: {
        captionsValue() {
            let val = [];
            for (const item of this.captions) {
                val.push({
                    status: item.status,
                    label: item.label,
                    format: item.format,
                    lang: item.lang,
                    caption_text: item.caption_text,
                    params: item.params,
                });
            }

            return JSON.stringify(val);
        },
        noErrors() {
            for (const item of this.captions) {
                if (item.errors && item.errors.length > 0) {
                    return false;
                }
            }
            return true;
        },
    },

    mounted() {
        this.$nextTick(() => {
            this.formats = this.config.formats.allowed || ['webvtt'];
            this.langs = Object.keys(this.languages).map((key) => {
                return {
                    value: key,
                    label: this.languages[key],
                };
            });
            for (let item of this.items) {
                const caption = {
                    uid: this.uid(),
                    status: item.status,
                    label: item.label,
                    format: item.format,
                    lang: item.lang,
                    caption_text: item.caption_text,
                    params: item.params,
                    mode: 'view',
                };
                this.captions.push(caption);
                this.originalCaptions.push({ ...caption });
            }
            this.captionsValueStart = this.captionsValue;
            this.newItem = {
                nuid: this.uid(),
                status: 'draft',
                label: null,
                format: this.config?.formats?.default || 'webvtt',
                lang: 'it',
                caption_text: null,
                params: null,
                mode: 'view',
            };
            this.webvttParser = new WebVTTParser();
        });
    },

    methods: {
        add() {
            const item = {...this.newItem};
            item.mode = 'view';
            item.uid = item.nuid;
            this.captions.push(item);
            this.newItem = {
                nuid: this.uid(),
                status: 'draft',
                label: null,
                format: this.config?.formats?.default || 'webvtt',
                lang: 'it',
                caption_text: null,
                params: null,
                mode: 'view',
            };
            this.viewMode = 'view';
        },
        captionSaveDisabled(item) {
            if (item?.caption_text === null || item?.caption_text?.length === 0) {
                return true;
            }

            return item.errors && item.errors.length > 0;
        },
        handleDragStart(index) {
            setTimeout(() => {
                this.draggingIndex = index;
            }, 0)
        },
        handleDrop(index) {
            if (this.draggingIndex === null || this.draggingIndex > this.captions.length) return;
            const draggedItem = this.captions[this.draggingIndex];
            this.captions.splice(this.draggingIndex,1);
            this.captions.splice(index,0,draggedItem);
        },
        handleDragEnter(index) {
            if (this.draggingIndex === null || this.draggingIndex === index) return;
            this.swapItems(this.draggingIndex, index);
            this.draggingIndex = index;
        },
        remove(item) {
            this.captions.splice(this.captions.indexOf(item), 1);
        },
        async save() {
            try {
                this.loading = true;
                const payload = {
                    data: {
                        id: this.objectId,
                        type: this.objectType,
                        attributes: {
                            captions: this.captions.map((item) => {
                                return {
                                    status: item.status,
                                    label: item.label,
                                    format: item.format,
                                    lang: item.lang,
                                    caption_text: item.caption_text,
                                    params: item.params,
                                };
                            }),
                        },
                    },
                };
                const options = {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken,
                    },
                    body: JSON.stringify(payload)
                };
                const response = await fetch(`${BEDITA.base}/api/${this.objectType}/${this.objectId}`, options);
                const responseJson = await response.json();
                if (responseJson.error) {
                    BEDITA.error(responseJson.error);
                }
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
            }
        },
        swapItems(fromIndex, toIndex) {
            if(fromIndex > this.captions.length || toIndex > this.captions.length) return;
            const temp = this.captions[fromIndex];
            this.$set(this.captions, fromIndex, this.captions[toIndex]);
            this.$set(this.captions, toIndex, temp);
        },
        truncate(str, len) {
            return this.$helpers.truncate(str, len);
        },
        validate(item) {
            if (item.format !== 'webvtt') {
                return;
            }
            const content = item.caption_text;
            const tree = this.webvttParser.parse(content, 'metadata');
            if (item.uid) {
                this.captions.find((caption) => caption.uid === item.uid).errors = tree.errors || [];
            } else {
                this.newItem.errors = tree.errors || [];
            }
        },
        uid() {
            return Math.random().toString(16).slice(2);
        },
        undoAllChanges() {
            this.captions = [];
            for (let item of this.originalCaptions) {
                this.captions.push({ ...item });
            }
        },
        undoChanges(item) {
            const originalData = this.originalCaptions.find((caption) => caption.uid === item.uid) || {};
            this.captions.find((caption) => caption.uid === item.uid).status = originalData.status;
            this.captions.find((caption) => caption.uid === item.uid).label = originalData.label;
            this.captions.find((caption) => caption.uid === item.uid).format = originalData.format;
            this.captions.find((caption) => caption.uid === item.uid).lang = originalData.lang;
            this.captions.find((caption) => caption.uid === item.uid).caption_text = originalData.caption_text;
            this.captions.find((caption) => caption.uid === item.uid).params = originalData.params;
            this.captions.find((caption) => caption.uid === item.uid).mode = 'view';
        },
    },
}
</script>
<style>
.object-captions > div.captions-none {
    border-bottom: 1px dashed white;
    padding-bottom: 1rem;
}
.object-captions > div.captions-container {
    display: grid;
    grid-template-columns: 30% 10% 8% 6% 40px 120px 120px 100px;
    border-bottom: 1px dashed white;
}

.object-captions > div.captions-container > div.column {
    font-weight: bold;
    font-size: 0.9rem;
}

.object-captions > div.captions-container > div {
    padding: 14px;
    font-size: 0.9rem;
}

.object-captions > div > div.sort {
    background: transparent;
    color: white;
    align-self: center;
}

.object-captions > div > div.sort:hover {
    cursor: move;
}

.object-captions > div.dragging {
    opacity: 0;
}

.object-captions > div.captions-container > div > span.ribbon {
    z-index: 2;
    color: #fff;
    font-size: 0.75rem;
    text-transform: uppercase;
    padding: 4px 16px 4px 16px;
    border-radius: 4px;
}

.object-captions > div.captions-container > div > section.caption-form-container {
    border: dotted 1px white;
    background-color: #383642;
    color: white;
    padding: 1rem !important;
    border-radius: 15px;
}

.object-captions ol.validate-results {
    padding: 0;
    margin: 0;
    list-style-type: none;
}

.object-captions ol.validate-results > li {
    color: red;
}
</style>
