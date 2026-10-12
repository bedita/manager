<template>
    <div class="mail-preview">
        <div class="preview-fields">
            <section
                class="template-fields"
                v-if="placeholders.length"
            >
                <h3>{{ msgTemplateValues }}</h3>
                <div class="variables-grid">
                    <div
                        v-for="placeholder in placeholders"
                        :key="placeholder"
                        class="input text"
                    >
                        <label :for="`mail-variable-${placeholder}`">{{ formatPlaceholder(placeholder) }}</label>
                        <input
                            :id="`mail-variable-${placeholder}`"
                            type="text"
                            :placeholder="placeholder"
                            v-model="variables[placeholder]"
                        >
                    </div>
                </div>
            </section>

            <section class="preview-section">
                <h3>{{ msgPreview }}</h3>
                <div class="text-preview">
                    <div v-html="previewText" />
                </div>
            </section>
        </div>

        <section class="delivery-section">
            <h3>{{ msgDelivery }}</h3>
            <div class="delivery-fields">
                <div class="input">
                    <label for="mail-preview-profile">{{ msgProfile }}</label>
                    <select
                        id="mail-preview-profile"
                        :disabled="!profiles.length"
                        v-model="profile"
                    >
                        <option
                            v-for="availableProfile in availableProfiles"
                            :key="availableProfile"
                            :value="availableProfile"
                        >{{ availableProfile }}</option>
                    </select>
                </div>
                <div class="input text">
                    <label for="mail-preview-destination">{{ msgRecipient }}</label>
                    <input
                        id="mail-preview-destination"
                        type="email"
                        autocomplete="email"
                        v-model="destination"
                    >
                </div>
                <button
                    class="button button-outlined"
                    :class="{ 'is-loading-spinner': loading }"
                    :disabled="!destination"
                    @click.prevent.stop="send"
                >
                    <app-icon icon="carbon:email" />
                    <span class="ml-05">{{ msgSend }}</span>
                </button>
            </div>
        </section>
    </div>
</template>
<script>
import { t } from 'ttag';
export default {
    name: 'MailPreview',
    props: {
        text: {
            type: String,
            required: true
        },
        profiles: {
            type: Array,
            required: true
        },
        uname: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            availableProfiles: [],
            destination: '',
            loading: false,
            msgSend: t`Send`,
            msgPreview: t`Preview`,
            msgTemplateValues: t`Template values`,
            msgDelivery: t`Delivery`,
            msgProfile: t`Profile`,
            msgRecipient: t`Recipient email`,
            placeholders: [],
            profile: this.profiles[0] || '',
            variables: {},
        }
    },
    computed: {
        previewText() {
            return this.text.replace(/{{(.*?)}}/g, (placeholder, name) => {
                const value = this.variables[name.trim().toLowerCase()];
                if (value === undefined || value === '') {
                    return placeholder;
                }
                return String(value).replace(/[&<>"']/g, character => ({
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                })[character]);
            });
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.placeholders = (this.text.match(/{{(.*?)}}/g) || []).map(placeholder => placeholder.replace(/{{|}}/g, '').trim().toLowerCase());
            this.placeholders = [...new Set(this.placeholders)];
            this.placeholders.forEach((placeholder) => {
                this.$set(this.variables, placeholder, '');
            });
            this.availableProfiles = [];
            for (const profile of this.profiles) {
                if (!this.availableProfiles.includes(profile)) {
                    this.availableProfiles.push(profile);
                }
            }
        });
    },
    methods: {
        formatPlaceholder(placeholder) {
            return placeholder.replace(/[_-]+/g, ' ').replace(/\b\w/g, character => character.toUpperCase());
        },
        async send() {
            try {
                this.loading = true;
                const response = await fetch(`${BEDITA.base}/sendmail`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-Token': BEDITA.csrfToken
                    },
                    body: JSON.stringify({
                        name: this.uname,
                        data: this.variables,
                        config: {
                            to: this.destination,
                            transport: this.profile,
                        }
                    })
                });
                const json = await response.json();
                if (json?.error) {
                    throw new Error(json.error);
                }
            } catch (error) {
                BEDITA.error(error);
            } finally {
                this.loading = false;
            }
        }
    },
}
</script>
<style>
div.mail-preview > section {
    margin-top: 1rem;
}
div.mail-preview .preview-fields {
    display: grid;
    grid-template-columns: minmax(0, 1fr);
    gap: 1rem;
    align-items: start;
}
div.mail-preview h3 {
    font-size: 1rem;
    margin: 0 0 0.5rem;
}
div.mail-preview .variables-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.75rem 1rem;
}
div.mail-preview .delivery-fields {
    display: flex;
    align-items: end;
    gap: 0.75rem;
}
div.mail-preview .delivery-fields > .input:first-child {
    flex: 1 1 12rem;
    max-width: 18rem;
}
div.mail-preview .delivery-fields > .input:first-child select {
    width: 100%;
}
div.mail-preview .delivery-fields > .input:nth-child(2) {
    flex: 0 1 18rem;
}
div.mail-preview .input label {
    display: block;
    margin-bottom: 0.25rem;
}
div.mail-preview .text-preview {
    max-height: 24rem;
    overflow: auto;
    border: 1px solid #ccc;
    color: #000;
    background-color: #FFF;
    border-radius: 5px;
    padding: 1.5rem;
    font-size: medium;
}
div.mail-preview > .text-preview > div {
    white-space: pre-line;
}
div.mail-preview > .text-preview > div > p {
    margin-top: 0.5rem;
}
div.mail-preview > .text-preview > div > p > a {
    color: #007bff;
}
@media (max-width: 600px) {
    div.mail-preview .preview-fields,
    div.mail-preview .variables-grid,
    div.mail-preview .delivery-fields {
        grid-template-columns: minmax(0, 1fr);
    }
    div.mail-preview .delivery-fields {
        align-items: stretch;
        flex-direction: column;
    }
}
</style>
