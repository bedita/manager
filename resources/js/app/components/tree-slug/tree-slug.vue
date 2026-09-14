<template>
    <div class="tree-slug">
        <template v-if="mode === 'view'">
            {{ slugPathCompact }}
        </template>
        <template v-if="mode === 'edit'">
            <template v-if="!editing && v === slugOriginalContent">
                <div :title="`${slugPathCompact}/${v}`"
                     class="slug-path"
                >
                    <span class="slug-path-parents">{{ slugPathCompact }}</span>
                    <span class="slug-path-leaf">/{{ v }}</span>
                </div>
                <div class="button-wrapper">
                    <button
                        class="button button-outlined"
                        @click.stop.prevent="editing = true"
                    >
                        <app-icon icon="carbon:edit" />
                    </button>
                </div>
            </template>
            <template v-if="!editing && v !== slugOriginalContent">
                <div :title="`${slugPathCompact}/${v}`"
                     class="slug-path"
                >
                    <span class="slug-path-parents">{{ slugPathCompact }}</span>
                    <span class="slug-path-leaf">/{{ v }}</span>
                </div>
                <div class="button-wrapper">
                    <button
                        class="button button-outlined"
                        @click.stop.prevent="save"
                    >
                        <app-icon
                            :class="saving?'saving':''"
                            :icon="saving?'carbon:cics-transaction-server-zos':'carbon:save'"
                        />
                    </button>
                    <template v-if="!saving">
                        <button
                            class="button button-outlined"
                            @click.stop.prevent="v = slugOriginalContent; editing = false"
                        >
                            <app-icon icon="carbon:reset" />
                        </button>
                    </template>
                </div>
            </template>
            <template v-if="editing">
                <div class="slug-path slug-path-expanded">
                    <span class="slug-path-parents">{{ slugPathCompact }}</span>
                    <span class="slug-path-leaf">/<input
                        type="text"
                        v-model="v"
                    ></span>
                </div>
                <div class="button-wrapper">
                    <button
                        class="button button-outlined"
                        @click.stop.prevent="editing = false"
                    >
                        <app-icon icon="carbon:checkmark" />
                    </button>
                    <button
                        class="button button-outlined"
                        @click.stop.prevent="v = slugOriginalContent; editing = false"
                    >
                        <app-icon icon="carbon:close" />
                    </button>
                </div>
            </template>
        </template>
    </div>
</template>
<script>
const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-CSRF-Token': BEDITA.csrfToken,
    },
};
export default {
    name: 'TreeSlug',
    props: {
        mode: {
            type: String,
            required: true
        },
        objectId: {
            type: String,
            required: true
        },
        objectType: {
            type: String,
            required: true
        },
        objectUname: {
            type: String,
            required: true
        },
        parentId: {
            type: String,
            required: true
        },
        slugContent: {
            type: String,
            default: null
        },
        slugPath: {
            type: Array,
            default: () => []
        },
        slugPathCompact: {
            type: String,
            default: null
        },
    },
    data() {
        return {
            editing: false,
            saving: false,
            slugOriginalContent: this.slugContent || (this.objectUname + '-' + this.objectId),
            v: this.slugContent || (this.objectUname + '-' + this.objectId),
        }
    },
    methods: {
        async save() {
            this.saving = true;
            // Getting the right parent for this slug
            let parentId = this.parentId;
            API_OPTIONS.method = 'POST';
            API_OPTIONS.body = JSON.stringify({type: this.objectType, parent: parentId, id: this.objectId, slug: this.v})
            const response = await fetch(`${API_URL}tree/slug`, API_OPTIONS);
            if(response.status == '200'){
                this.slugOriginalContent = this.v;
            }
            this.editing = false;
            setTimeout(() => this.saving = false, 1500);
        },
    },
}
</script>
<style scoped>
.tree-slug {
    margin: 0.25rem;
    margin-left: 1.25rem;
    font-size: 0.875rem;
    font-family: Arial, sans-serif;
    width: 100%;
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
}
.tree-slug .slug-path {
    display: flex;
    align-items: baseline;
    width: fit-content;
    max-width: 100%;
    border-bottom: 1px dotted #ccc;
}
.tree-slug .slug-path-parents {
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.tree-slug .slug-path-leaf {
    flex: none;
    white-space: nowrap;
}
.tree-slug .slug-path-expanded {
    display: block;
}
.tree-slug .slug-path-expanded .slug-path-parents,
.tree-slug .slug-path-expanded .slug-path-leaf {
    overflow: visible;
    text-overflow: clip;
    white-space: normal;
    overflow-wrap: anywhere;
}
.tree-slug input {
    border: 1px solid #ccc;
    background-color: #cccc;
    border-radius: 4px;
    height: 1rem;
    margin-left: 0.25rem;
    width: fit-content !important;
    field-sizing: content;
}
.tree-slug button {
    cursor: pointer;
    padding-top: 0.1rem;
    padding-bottom: 0.1rem;
    height: 1rem;
}
.button-wrapper {
    display: flex;
    flex-direction: row;
    gap: 4px;
    margin-top: 0.5rem;
}
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.saving {
    animation: spin 0.5s linear infinite;
}
</style>
