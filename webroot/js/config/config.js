// Vue configs...

Vue.config.devtools = true;

Vue.options.delimiters = ['@(', ')'];

// CKeditor configs...

var ckeditorConfig = {
    configFull: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            '/',
            { name: 'links', items: [ 'Link', 'Unlink', 'Anchor' ] },
            { name: 'editAttributes', items: [ 'Attr' ] },
            { name: 'editing', groups: [ 'find'], items: [ 'Find', 'Replace' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
            { name: 'tools', items: [ /*'Maximize', */'ShowBlocks', 'AutoCorrect' ] },
            '/',
            { name: 'styles', items: [ 'Format' , 'Styles'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
        ],
        allowedContent: true,
        resize_enabled: true,
        autocorrect_enabled: true,
        extraPlugins: 'attributes,autocorrect,beButtons,codemirror,onchange,webkit-span-fix',
        language: BEDITA.currLang2,
        codemirror: { theme: 'lesser-dark' },
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
        height:660
    },

    configNormal: {
        toolbar: [
            { name: 'document', groups: [ 'mode' ], items: [ 'Source'] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ], items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat' ] },
            { name: 'links', items: [ 'Link', 'Unlink'/*, 'Anchor' */] },
            { name: 'paragraph', groups: [ 'list','blocks', 'align' ], items: [ 'NumberedList', 'BulletedList', '-', 'Blockquote', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            '/',
            /*{ name: 'customTools', items: [ 'x\y', 'Dfn', 'Glo' ] },*/
            { name: 'editAttributes', items: [ 'Attr' ] },
            { name: 'editing', groups: [ 'find'], items: [ 'Find'/*, 'Replace'*/ ] },
            { name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar', 'Formula' ] },
            { name: 'styles', items: [ 'Format' , 'Styles'] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ], items: [ /*'Paste', */'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'tools', items: [/* 'Maximize',*/ 'ShowBlocks', 'AutoCorrect' ] },
        ],
        allowedContent: true,
        resize_enabled: true,
        autocorrect_enabled: true,
        extraPlugins: 'codemirror,attributes,beButtons,onchange,webkit-span-fix,autocorrect',
        language: BEDITA.currLang2,
        codemirror: { theme: 'lesser-dark' },
        entities:false,
        fillEmptyBlocks:false,
        forcePasteAsPlainText:true,
        startupOutlineBlocks: true,
        height:660
    },
};
