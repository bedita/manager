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
export const ACCEPTABLE_MEDIA = ['media', 'images', 'videos', 'audio', 'files'];

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
    configFull: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'editAttributes', items: [ 'Attr' ] },
            { name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
            { name: 'tools', items: [ 'ShowBlocks', 'AutoCorrect' ] },
            { name: 'styles', items: [ 'Format' , 'Styles'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        ],
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
        height: 200, // 12.5em (16px)
    },

    configNormal: {
        toolbar: [
            { name: 'basicstyles', groups: ['basicstyles', 'cleanup'], items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
            { name: 'paragraph', groups: ['list', 'blocks', 'align'], items: ['NumberedList', 'BulletedList'] },
            { name: 'styles', items: ['Format'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'clipboard', groups: ['clipboard', 'undo'], items: ['PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'document', groups: ['mode'], items: ['Source'] },
        ],
        format_tags: 'p;h2',
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
        height: 200, // 12.5em (16px)
    },

    configSimple: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'links', items: [ 'Link', 'Unlink'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Undo', 'Redo' ] },
            { name: 'tools', items: [ 'ShowBlocks' ] },
        ],
        allowedContent: true,
        language: BEDITA.currLang2,
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
        height: 200, // 12.5em (16px)
    },
};
