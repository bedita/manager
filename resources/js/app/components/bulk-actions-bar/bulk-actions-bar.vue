<template>
    <div class="bulk-actions-bar">
        <div class="select-cell narrow">
            <input
                ref="checkAllCB"
                type="checkbox"
                v-model="multi"
                @click="toggle"
            >
        </div>
        <div>
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgUpdateToOn"
                @click="setStatus('on', $event)"
            >
                <app-icon
                    icon="carbon:toggle-on-fill"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
        </div>
        <div>
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgUpdateToDraft"
                @click="setStatus('draft', $event)"
            >
                <app-icon
                    icon="carbon:toggle-off-fill"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
        </div>
        <div>
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgUpdateToOff"
                @click="setStatus('off', $event)"
            >
                <app-icon
                    icon="carbon:toggle-off"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
        </div>
        <div
            class="copy-folder-action"
            v-if="canSaveFolders"
        >
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgCopyToFolder"
                @click="toggleCopyFolder"
            >
                <app-icon
                    icon="carbon:folder-parent"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
            <div
                class="folder-picker-popover"
                v-if="copyToFolderShow"
            >
                <folder-picker
                    :label="msgCopyToFolder"
                    @change="copyFolder = $event"
                />
                <button
                    class="button button-outlined ml-1"
                    type="button"
                    :disabled="disabled || !copyFolder"
                    @click="applyPosition('copy', copyFolder)"
                >
                    {{ msgCopy }}
                </button>
            </div>
        </div>
        <div
            class="move-folder-action"
            v-if="canSaveFolders"
        >
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgMoveToFolder"
                @click="toggleMoveFolder"
            >
                <app-icon
                    icon="carbon:folder-move-to"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
            <div
                class="folder-picker-popover"
                v-if="moveToFolderShow"
            >
                <folder-picker
                    :label="msgMoveToFolder"
                    @change="moveFolder = $event"
                />
                <button
                    class="button button-outlined ml-1"
                    type="button"
                    :disabled="disabled || !moveFolder"
                    @click="applyPosition('move', moveFolder)"
                >
                    {{ msgMove }}
                </button>
            </div>
        </div>
        <div>
            <a
                class="button button-outlined"
                :class="{ 'is-disabled': disabled }"
                :aria-disabled="disabled"
                v-title="msgMoveToTrash"
                @click="remove"
                v-if="canDelete === 1"
            >
                <app-icon
                    icon="carbon:trash-can"
                    :style="{ width: '20px', height: '20px' }"
                />
            </a>
        </div>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'BulkActionsBar',
    components: {
        FolderPicker: () => import(/* webpackChunkName: "folder-picker" */'app/components/folder-picker/folder-picker'),
    },
    props: {
        canDelete: {
            type: [Number, String],
            default: 0,
        },
        canSaveFolders: {
            type: Number,
            default: 0,
        },
        selectedRows: {
            type: Array,
            default: () => [],
        },
    },
    emits: ['position', 'remove', 'set-status', 'toggle'],
    data() {
        return {
            copyToFolderShow: false,
            copyFolder: null,
            moveToFolderShow: false,
            moveFolder: null,
            multi: false,
            msgCopy: t`Copy`,
            msgCopyToFolder: t`Copy to folder`,
            msgMove: t`Move`,
            msgMoveToFolder: t`Move to folder`,
            msgMoveToTrash: t`Move to trash`,
            msgUpdateToDraft: t`Set status "draft"`,
            msgUpdateToOff: t`Set status "off"`,
            msgUpdateToOn: t`Set status "on"`,
        };
    },
    computed: {
        disabled() {
            return this.selectedRows.length === 0;
        },
    },
    methods: {
        remove() {
            if (this.disabled) {
                return;
            }
            this.$emit('remove', true);
        },
        setStatus(status, event) {
            if (this.disabled) {
                return;
            }
            this.reset();
            this.$emit('set-status', { status, event });
        },
        reset() {
            this.copyToFolderShow = false;
            this.copyFolder = null;
            this.moveToFolderShow = false;
            this.moveFolder = null;
        },
        toggleCopyFolder() {
            if (this.disabled) {
                return;
            }
            const show = !this.copyToFolderShow;
            this.reset();
            this.copyToFolderShow = show;
        },
        toggleMoveFolder() {
            if (this.disabled) {
                return;
            }
            const show = !this.moveToFolderShow;
            this.reset();
            this.moveToFolderShow = show;
        },
        applyPosition(action, folder) {
            if (this.disabled || !folder?.id) {
                return;
            }
            this.$emit('position', { action, folderId: folder.id });
            this.reset();
        },
        toggle() {
            this.$emit('toggle', this.multi);
        },
    },
}
</script>
<style scoped>
div.bulk-actions-bar {
    display: grid;
    grid-template-columns: 3rem 3.5rem 3.5rem 3.5rem 3.5rem 3.5rem 3.5rem 3.5rem;
    align-items: center;
}
div.bulk-actions-bar > div {
    display: flex;
    align-items: center;
    justify-content: center;
}
div.bulk-actions-bar > .select-cell {
    justify-content: flex-start;
}
div.bulk-actions-bar > .select-cell input[type="checkbox"] {
    position: relative;
    left: 5px;
}
.copy-folder-action, .move-folder-action {
    position: relative;
}
.copy-folder-action .folder-picker-popover, .move-folder-action .folder-picker-popover {
    position: absolute;
    top: calc(100% + 0.5rem);
    left: calc(50% - 1.35rem);
    z-index: 10;
    display: flex;
    align-items: flex-end;
    gap: 0.5rem;
    width: 23rem;
    max-width: calc(100vw - 2rem);
    padding: 0.75rem;
    background: #fff;
    border: 1px solid #adb5bd;
    border-radius: 4px;
    box-shadow: 0 0.25rem 0.75rem rgb(0 0 0 / 20%);
    box-sizing: border-box;
}
.folder-picker-popover .folder-picker {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.folder-picker-popover :deep(.folder-picker > label) {
    display: block;
    margin: 0 0 0.25rem;
    color: #212529;
    font-weight: 600;
    line-height: 1.25;
}
.folder-picker-popover :deep(.vue-treeselect) {
    width: 100%;
}
.folder-picker-popover :deep(.vue-treeselect__control) {
    display: table !important;
    width: 100% !important;
    height: 36px !important;
    max-width: none;
    min-height: 36px;
    padding: 0 5px !important;
    background: #fff;
    border-bottom: 1px dotted #7d8790 !important;
    border-radius: 4px !important;
    box-sizing: border-box;
}
.folder-picker-popover button.button-outlined {
    height: 36px;
    min-height: 36px;
    padding: 0 0.75rem;
    background: #f1f3f5;
    border-color: #68727d;
    color: #212529;
    box-sizing: border-box;
}
.folder-picker-popover button.button-outlined:hover,
.folder-picker-popover button.button-outlined:focus {
    background: #dfe3e7;
    border-color: #495057;
    color: #111;
}
.folder-picker-popover button.button-outlined:disabled {
    background: #e9ecef;
    border-color: #ced4da;
    color: #6c757d;
}
a.button {
    height: 2.7rem;
    min-width: 2.7rem;
    width: 2.7rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    margin: 0;
    line-height: 1;
    justify-self: center;
    color: white;
    border-color: transparent;
    border-radius: 50%;
    transition: background-color 0.3s ease;
}

a.button :deep(svg) {
    display: block;
}

a.button:hover {
    cursor: pointer;
}
.is-disabled {
    opacity: 0.35;
    pointer-events: none;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    div.bulk-actions-bar {
        grid-template-columns: repeat(auto-fill, 3.5rem);
        grid-auto-rows: 3.5rem;
    }
}
</style>
