<template>
    <div class="mail-preview">
        <div class="text-preview mt-05">
            <div :value="text"
                 v-html="text"
            />
        </div>

        <div class="input text mt-05"
             v-for="placeholder in placeholders"
             :key="placeholder"
        >
            <input type="text"
                   :placeholder="placeholder"
                   v-model="variables[placeholder]"
            >
        </div>

        <div class="mt-05 send">
            <input type="text"
                   placeholder="gustavo@bedita.net"
                   v-model="destination"
            >
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
        uname: {
            type: String,
            required: true
        }
    },
    data() {
        return {
            destination: '',
            loading: false,
            msgSend: t`Send`,
            placeholders: [],
            variables: {},
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.placeholders = this.text.match(/{{(.*?)}}/g).map(placeholder => placeholder.replace(/{{|}}/g, '').trim().toLowerCase());
            this.placeholders.sort();
            this.placeholders = [...new Set(this.placeholders)];
            this.placeholders.forEach((placeholder, k) => {
                this.$set(this.variables, placeholder, '');
            });
        });
    },
    methods: {
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
div.mail-preview > .send {
    display: grid;
    grid-template-columns: 1fr 100px;
}
div.mail-preview > .text-preview {
    border: 1px dotted #ccc;
    color: #000;
    background-color: #FFF;
    border-radius: 5px;
    padding: 2rem 2rem;
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
</style>
