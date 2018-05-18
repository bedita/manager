/**
 *
 * v-richeditor directive to activate ckeditor on element
 *
 */
Vue.directive('richeditor', {

    /**
     * When the bound element is inserted into the init CKeditor
     *
     * @param {Object} element DOM object
     */
    inserted (element) {
        const configKey = element.getAttribute('ckconfig');
        let loadedConfig = null;
        if (ckeditorConfig) {
            loadedConfig = ckeditorConfig[configKey];
        }

        CKEDITOR.replace(element, loadedConfig);
    },
});
