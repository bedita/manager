/**
 * Templates that uses this component (directly or indirectly):
 *  Template/Modules/view.twig
 *
 * <modules-view> component used for ModulesPage -> View
 *
 */

// import '../../config/config';


Vue.directive('richeditor', {

    // When the bound element is inserted into the DOM...
    inserted (element) {
        console.log(element);
        let configKey = element.getAttribute('ckconfig');
        CKEDITOR.replace(element);
    }
});

Vue.component('modules-view', {

    /**
     * component properties
     *
     * @returns {Object}
     */
    data() {
        return {

        };
    },

    // /**
    //  * triggered after virtual DOM is mounted
    //  *
    //  * @returns {void}
    //  */
    // mounted () {
    //     // description -> normal
    //     // body -> full
    //     // extro -> json editor

    //     let textareas = this.$el.getElementsByTagName('textarea');
    //     for (let i = 0; i < textareas.length; i++) {
    //         let ckConfig = ckeditorConfig.configNormal;
    //         let textarea = textareas[i];
    //         let configKey = textarea.getAttribute('ckconfig');
    //         if (configKey) {
    //             ckConfig = ckeditorConfig[configKey];
    //         }

    //         let toolbarOptions = [
    //             ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
    //             ['blockquote', 'code-block'],

    //             [{ 'header': 1 }, { 'header': 2 }],               // custom button values
    //             [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    //             [{ 'script': 'sub'}, { 'script': 'super' }],      // superscript/subscript
    //             [{ 'indent': '-1'}, { 'indent': '+1' }],          // outdent/indent
    //             [{ 'direction': 'rtl' }],                         // text direction

    //             [{ 'size': ['small', false, 'large', 'huge'] }],  // custom dropdown
    //             [{ 'header': [1, 2, 3, 4, 5, 6, false] }],

    //             [{ 'color': [] }, { 'background': [] }],          // dropdown with defaults from theme
    //             [{ 'font': [] }],
    //             [{ 'align': [] }],

    //             ['clean'],                                         // remove formatting button
    //             ['link', 'image', 'video'],
    //             ['showHtml']
    //         ];

    //         console.log(textarea)
    //         let quillArea = document.createElement('div');
    //         this.$el.insertBefore(quillArea, textarea);

    //         textarea._quill = new Quill(quillArea, {
    //             modules: {
    //                 toolbar: toolbarOptions
    //             },
    //             theme: 'snow'
    //         });


    //         // CKEDITOR.replace(textareas[i], ckConfig);

    //         // ClassicEditor.create(textareas[i],  {
    //         //     toolbar: [ 'headings', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote' ],
    //         //     heading: {
    //         //         options: [
    //         //             { modelElement: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
    //         //             { modelElement: 'heading1', viewElement: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
    //         //             { modelElement: 'heading2', viewElement: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' }
    //         //         ]
    //         //     }
    //         // });
    //     }
    // },

});


