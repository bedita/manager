<template>
    <div class="user-filter">
        <autocomplete
            base-class="user-filter-autocomplete"
            size="100"
            :search="search"
            :get-result-value="getResultValue"
            :default-value="selectedLabel"
            :placeholder="placeholder"
            :debounce-time="300"
            @submit="onSubmit"
            @input="onInput"
        />
        <input
            type="hidden"
            :name="name"
            :value="selectedId"
        >
    </div>
</template>

<script>
const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    },
};

export default {
    props: {
        name: {
            type: String,
            required: true,
        },
        initialId: {
            type: String,
            default: '',
        },
        initialLabel: {
            type: String,
            default: '',
        },
        placeholder: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            selectedId: this.initialId,
            selectedLabel: this.initialLabel,
        };
    },

    methods: {
        async search(input) {
            if (!input || input.trim().length < 2) {
                return [];
            }
            const response = await fetch(`${API_URL}users/list?q=${encodeURIComponent(input.trim())}&page_size=10`, API_OPTIONS);
            const json = await response.json();

            return (json.data || []).map((user) => ({
                id: user.id,
                label: this.formatLabel(user.attributes),
            }));
        },

        formatLabel(attributes) {
            const name = [attributes.name, attributes.surname].filter(Boolean).join(' ');

            return name ? `${name} (${attributes.username})` : attributes.username;
        },

        getResultValue(result) {
            return result.label;
        },

        onSubmit(result) {
            this.selectedId = result.id;
            this.selectedLabel = result.label;
        },

        onInput(input) {
            if (!input) {
                this.selectedId = '';
                this.selectedLabel = '';
            }
        },
    },
};
</script>
<style scoped>
.user-filter {
    position: relative;
    display: inline-block;
}
</style>
