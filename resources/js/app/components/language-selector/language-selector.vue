<template>
    <div class="languageSelector">
        <div class="input select">
            <label for="lang">{{ title }}</label>
            <Treeselect
                v-if="readonly === 0"
                placeholder
                :form="form"
                :options="languagesOptions"
                :disable-branch-nodes="true"
                :multiple="false"
                v-model="lang"
                @input="onChange"
            />
            <select v-else><option>{{ languages[lang] }}</option></select>
            <input v-if="reference" type="hidden" :id="reference" :value="lang" />
            <input v-if="reference" type="hidden" :id="`${reference}Label`" :value="languages[lang]" />
            <input v-if="reference != 'translateFrom'" type="hidden" name="lang" v-model="lang" />
        </div>
    </div>
</template>
<script>
import { Treeselect } from '@riophae/vue-treeselect'
import '@riophae/vue-treeselect/dist/vue-treeselect.css'

import { t } from 'ttag';
export default {
    name: 'LanguageSelector',
    components: {
        Treeselect,
    },
    props: {
        excludeLang: {
            type: String,
            default: '',
        },
        language: {
            type: String,
            default: '',
        },
        languageLabel: {
            type: String,
            default: '',
        },
        languages: {
            type: Object,
            default: () => {},
        },
        readonly: {
            type: Number,
            default: 0,
        },
        reference: {
            type: String,
            default: '',
        },
    },
    data() {
        return {
            form: 'langSelector',
            lang: '',
            languagesOptions: [],
            title: '',
            msgMainLanguage: t`The main language is`,
        };
    },
    mounted() {
        this.$nextTick(() => {
            this.lang = this.language == '' ? null : this.language;
            this.title = this.languageLabel || this.msgMainLanguage;
            this.languages['null'] = '';
            if (this.excludeLang) {
                delete this.languages[this.excludeLang];
            }
            this.languagesOptions = Object.keys(this.languages).map((key) => {
                return {
                    id: key,
                    label: this.languages[key],
                };
            });
        });
    },
    methods: {
        onChange() {
            this.$emit('change', this.lang);
        }
    },
};
</script>
<style>
.languageSelector {
    max-width: 150px;
}
.languageSelector span.main {
    font-weight: bold;
}
</style>
