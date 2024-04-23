<template>
    <div class="object-types-picker">
        <Treeselect
            :placeholder="msgObjectTypes"
            :options="types"
            :async="false"
            :clearable="false"
            :disable-branch-nodes="true"
            :multiple="true"
            value-format="object"
            v-model="selectedTypes"
            @input="changeTypes"
        />
    </div>
</template>
<script>
import { Treeselect } from '@riophae/vue-treeselect'
import { t } from 'ttag';
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

export default {
    name: 'ObjectTypesPicker',

    components: {
        Treeselect,
    },

    props: {
        initialSelected: {
            type: Array,
            default: () => [],
        },
        initialTypes: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            msgObjectTypes: t`Object types`,
            selectedTypes: [],
            types: [],
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.types = this.initialTypes?.map((t) => ({ id: t, label: t }));
            this.types = this.types.sort((a, b) => a.label.localeCompare(b.label));
            this.selectedTypes = this.initialSelected ? this.initialSelected.sort().map((t) => ({ id: t, label: t })) : [];
        });
    },

    methods: {
        changeTypes() {
            this.$emit('updatequerytypes', this.selectedTypes.map((item) => item.id));
        },
    },
}
</script>
<style>
div.object-types-picker {
    min-width: 150px;
}
</style>
