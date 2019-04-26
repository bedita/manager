// Vue configs...

import Vue from 'vue';
import Locale from 'app/locales';
import { t } from 'ttag';

export const VueConfig = {
    devtools: true,
}

export const VueOptions = {
    delimiters: ['<:', ':>'],
}

// Polyfill
import 'abortcontroller-polyfill/dist/abortcontroller-polyfill-only';

// merge vue options and configs
for (let property in VueConfig) {
    if (VueConfig.hasOwnProperty(property)) {
        Vue.config[property] = VueConfig[property];
    }
}

for (let property in VueOptions) {
    if (VueOptions.hasOwnProperty(property)) {
        Vue.options[property] = VueOptions[property];
    }
}

Locale(BEDITA.locale);

// General Configs

// TO-DO to be a dynamic array
export const ACCEPTABLE_MEDIA = ['media', 'image', 'video', 'audio', 'files'];

// Global mixins

Vue.mixin({
    methods: {
        /**
         * ttag helper method for string literal template
         *
         * @param {string} value string to translate
         *
         * @return {string} translated value
          */
        t: (value) => {
            // call ttag t method
            return t([value]);
        },
    }
});

// CKeditor configs...

export const CkeditorConfig = {
        configDefault: {
        toolbar: [
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike'] },
            { name: 'paragraph', groups: ['list', 'blocks', 'align'], items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
            { name: 'styles', groups: 'styles', items: ['Format'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['PasteText', 'PasteFromWord'] },
            { name: 'document', groups: ['mode'], items: ['RemoveFormat', '-', 'Source'] },
        ],
        format_tags: 'title;paragraph',
        format_title: { name: 'Section title', element: 'h2' },
        format_paragraph: { name: 'Normal paragraph', element: 'p' },
        allowedContent: true,
        language: BEDITA.currLang2,
        entities: false,
        fillEmptyBlocks: false,
        forcePasteAsPlainText: true,
        startupOutlineBlocks: true,
        height: 224, // 14rem (16px)
    }
};
