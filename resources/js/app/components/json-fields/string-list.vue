<template>
    <div class="input textarea text">
        <label :for="`container-${name}`">{{ label|humanize }}</label>
        <div :id="`container-${name}`">
            <div class="key-value-item mb-1 is-flex" v-for="(item, index) in items">
                <div class="is-expanded">
                    <input type="text" v-model="item.value" @change="onChanged()" :readonly="readonly"/>
                </div>
                <div v-if="!readonly">
                    <button @click.prevent="remove(index)" class="button button-primary" style="min-width: 32px; border-top-left-radius: 0; border-bottom-left-radius: 0;">
                        <Icon icon="carbon:trash-can"></Icon>
                        <span class="ml-05">{{ t('Remove') }}</span>
                    </button>
                </div>
            </div>
        </div>
        <button @click.prevent="add" v-if="!readonly">
            <Icon icon="carbon:add"></Icon>
            <span class="ml-05">{{ t('Add') }}</span>
        </button>

        <input type="hidden" :id="getId(name)" :name="name" v-model="result" @change="updateList($event)" />
    </div>
</template>

<script>
import { Icon } from '@iconify/vue2';

/**
 * <string-list> component to handle simple JSON array of strings
 */
export default {

    components: {
        Icon,
    },

    props: {
        value: String,
        name: String,
        label: String,
        readonly: Boolean,
    },

    data() {
        return {
            items: [],
            result: {},
        }
    },

    created() {
        this.result = this.value;
        if (!this.value) {
            this.result = null;
        }
        const v = JSON.parse(this.result) || [];
        v.forEach((k) => {
            this.items.push({
                value: k,
            })
        });

        if (!this.items.length) {
            this.add();
        }
    },

    methods: {
        /**
         * Update input hidden form value.
         *
         * @returns {void}
         */
        onChanged() {
            const data = this.items.map((i) => i?.value || null).filter((v) => v !== null);
            this.result = JSON.stringify(data);
        },

        /**
         * Add an empty item to list.
         *
         * @returns {void}
         */
        add() {
            this.items.push({value: ''});
        },

        /**
         * Remove item from list.
         *
         * @param {Integer} index The index
         * @returns {void}
         */
        remove(index) {
            this.items.splice(index, 1);
            this.onChanged();
        },

        getId(name) {
            return name.replaceAll('_', '-').replaceAll('[', '-').replaceAll(']', '');
        },

        updateList(event) {
            this.items = [];
            this.result = event?.target?.value || '';
            const items = this.result.split(',') || [];
            for (let item of items) {
                this.items.push({value: item});
            }
        },
    },
}
</script>
