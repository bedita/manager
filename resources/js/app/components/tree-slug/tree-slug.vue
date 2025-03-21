<template>
    <div class="tree-slug">
        <template v-if="mode === 'view'">
            {{ slugPathCompact }}
        </template>
        <template v-if="mode === 'edit'">
            <template v-if="!editing && v === slugOriginalContent">
                <span class="slugPath">{{ slugPathCompact }}/{{ v }}</span>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="editing = true"
                >
                    <app-icon icon="carbon:edit" />
                </button>
            </template>
            <template v-if="!editing && v !== slugOriginalContent">
                <span class="slugPath">{{ slugPathCompact }}/{{ v }}</span>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="save"
                >
                    <app-icon 
                        :class="saving?'saving':''" 
                        :icon="saving?'carbon:cics-transaction-server-zos':'carbon:save'"
                    />
                </button>
                <template v-if="!saving">
                    <button
                        class="button button-outlined ml-05"
                        @click.stop.prevent="v = slugOriginalContent; editing = false"
                    >
                        <app-icon icon="carbon:reset" />
                    </button>
                </template>
            </template>
            <template v-if="editing">
                <span class="slugPath">{{ slugPathCompact }}/</span>
                <input
                    type="text"
                    v-model="v"
                >
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="editing = false"
                >
                    <app-icon icon="carbon:checkmark" />
                </button>
                <button
                    class="button button-outlined ml-05"
                    @click.stop.prevent="v = slugOriginalContent; editing = false"
                >
                    <app-icon icon="carbon:close" />
                </button>
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
            slugOriginalContent: this.slugContent,
            v: this.slugContent,
        }
    },
    methods: {
        async save() {
            this.saving = true;
            // Getting the right parent for this slug
            API_OPTIONS.method = 'GET';
            let parentsResp = await fetch(`${API_URL}tree/parents/${this.objectType}/${this.objectId}`, API_OPTIONS);
            let parentsJson = (await parentsResp.json())
            let parentId = parentsJson.parents.filter(
                p => p.meta.slug_path_compact === this.slugPathCompact
            )[0].id;
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
    flex-direction: row;
}
.tree-slug .slugPath {
    white-space: nowrap;
    border-bottom: 1px dotted #ccc;
}
.tree-slug input {
    border: 1px solid #ccc;
    background-color: #cccc;
    margin-left: 0.25em;
    border-radius: 4px;
    height: 1rem;
    width: fit-content !important;
    field-sizing: content;
}
.tree-slug button {
    cursor: pointer;
    padding-top: 0.1rem;
    padding-bottom: 0.1rem;
    height: 1rem;
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
