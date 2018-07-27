// Vue configs...

import Vue from 'vue';

export const VueConfig = {
    devtools: true,
}

export const VueOptions = {
    delimiters: ['<:', ':>'],
}

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
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'links', items: [ 'Link', 'Unlink'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        ],
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
