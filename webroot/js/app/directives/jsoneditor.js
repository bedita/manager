/**
 *
 * v-jsoneditor directive to activate jsoneditor on element
 *
 */

const jsonEditorOptions = {
    "mode": "code",
    "modes": ["tree", "code"],
    "history": true,
    "search": true,
    onChange: function () {
        if (element) {
            const json = element.jsonEditor.get();

            try {
                element.value = JSON.stringify(json);
            } catch(e) {
                console.error(e);
            }
        }
    },
};

Vue.directive('jsoneditor', {
    element: null,
    /**
     * When the bound element is inserted into the init CKeditor
     *
     * @param {Object} element DOM object
     */
    inserted (element) {
        this.element = element;
        const content = element.value;
        try {
            const json = JSON.parse(content) || {};

            if (json) {
                element.style.display = "none";
                let jsonEditor = document.createElement('div');
                jsonEditor.className = "jsoneditor-container";
                element.parentElement.insertBefore(jsonEditor, element);
                element.jsonEditor = new JSONEditor(jsonEditor, jsonEditorOptions);
                element.jsonEditor.set(json);
            }
        } catch (err) {
            console.error(err);
        }
    },
});
