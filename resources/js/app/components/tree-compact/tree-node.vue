<template>
    <div class="tree-node">
        <label>
            <span>
                <input
                    :type="inputType"
                    name="position"
                    :checked="parentFolderId === node.id"
                    :disabled="branchDisabled || referenceObject?.id === node?.id"
                    @click="setParent($event)"
                >
            </span>
            <span class="ml-05">
                {{ folders[node?.id]?.attributes?.title || node?.id }}
            </span>
        </label>
        <div
            class="tree-node-children"
            v-if="children?.length > 0"
        >
            <tree-node
                v-for="child in children"
                :branch-disabled="branchDisabled || referenceObject?.id === node?.id"
                :key="child.id"
                :folders="folders"
                :node="child"
                :object-type="objectType"
                :parent-folder-id="parentFolderId"
                :reference-object="referenceObject"
                @update-parent="updateParent"
            />
        </div>
    </div>
</template>
<script>

export default {
    name: 'TreeNode',
    components: {
        TreeNode: () => import('./tree-node.vue'),
    },
    props: {
        branchDisabled: {
            type: Boolean,
            default: false
        },
        folders: {
            type: Object,
            default: () => ({}),
        },
        node: {
            type: Object,
            required: true
        },
        objectType: {
            type: String,
            required: true
        },
        parentFolderId: {
            type: String,
            default: null
        },
        referenceObject: {
            type: Object,
            default: () => ({})
        }
    },
    data() {
        return {
            children: [],
            inputType: '',
            parentId: null,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.children = Object.keys(this.node?.subfolders || {}).map(key => this.node.subfolders[key]);
            this.inputType = this.objectType === 'folders' ? 'radio' : 'checkbox';
        });
    },
    methods: {
        setParent(ev) {
            if (ev?.target?.checked) {
                this.$emit('update-parent', this.node.id);
            }
        },
        updateParent(id) {
            this.parentId = id;
            this.$emit('update-parent', id);
        }
    },
};
</script>
<style scoped>
div.tree-node > label {
    display:flex;
    align-items:center;
    direction:column;
    cursor: pointer;
}
div.tree-node > label > span {
    display: flex;
    align-self: center;
    cursor: pointer;
}
div.tree-node, div.tree-node div.tree-node-children {
    margin-left: 1rem;
}
</style>
