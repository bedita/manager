<template>
    <div class="input textarea text">
        <label :for="name">{{ label|humanize }}</label>
        <div :id="name">
            <div class="key-value-item mb-1" v-for="(item, index) in items">
                <div>
                    <input type="text" v-model="item.value" @change="onChanged()" :readonly="readonly"/>
                </div>
                <div class="mb-2" v-if="!readonly">
                    <button @click.prevent="remove(index)">{{  t('Remove') }}</button>
                </div>
            </div>
        </div>
        <button @click.prevent="add" v-if="!readonly">{{  t('Add') }}</button>

        <input type="hidden" :name="name" v-model="result" />
    </div>
</template>

<script>

/**
 * <string-list> component to handle simple JSON array of strings
 */
export default {
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
    },
}
</script>

