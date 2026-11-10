<template>
    <div class="user-filter">
        <!-- Autocomplete reads default-value only when mounted. -->
        <autocomplete
            :key="selectedLabel"
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
            :form="form"
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
        form: {
            type: String,
            default: '',
        },
        value: {
            type: String,
            default: null,
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
            selectedId: this.value ?? this.initialId,
            selectedLabel: this.initialLabel,
        };
    },

    computed: {
        userId() {
            return this.value ?? this.initialId;
        },
    },

    watch: {
        userId: {
            immediate: true,
            async handler(id) {
                if (id === this.selectedId && this.selectedLabel) {
                    return;
                }
                this.selectedId = id;
                this.selectedLabel = '';
                if (!id) {
                    return;
                }
                try {
                    const response = await fetch(`${API_URL}users/list?filter[id]=${encodeURIComponent(id)}&page_size=1`, API_OPTIONS);
                    if (!response.ok) {
                        throw new Error(`Unable to load user ${id}: ${response.status}`);
                    }
                    const json = await response.json();
                    const user = json.data.find(user => user.id === id);
                    if (!user) {
                        throw new Error(`User ${id} not found`);
                    }
                    if (this.selectedId === id) {
                        this.selectedLabel = this.formatLabel(user.attributes);
                    }
                } catch (error) {
                    console.error(error);
                }
            },
        },
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
            this.$emit('input', this.selectedId);
        },

        onInput(event) {
            if (!event.target.value) {
                this.selectedId = '';
                this.selectedLabel = '';
                this.$emit('input', '');
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
