import { CkeditorConfig } from 'config/config';

export default {
    template: /* template */`
    <div>
        <slot></slot>
    </div>
    `,

    props: {
        el: {
            type: HTMLTextAreaElement,
        },
    },

    async mounted() {
        // wild workaround to dynamic import CKEDITOR from cdn
        if (typeof CKEDITOR === 'undefined') {
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = 'https://cdn.ckeditor.com/4.11.3/standard/ckeditor.js';

            document.getElementsByTagName('head')[0].appendChild(script);
        }

        const element = this.el;
        const configKey = element.getAttribute('ckconfig');

        let loadedConfig = {};
        if (CkeditorConfig) {
            loadedConfig = CkeditorConfig[configKey];
        }

        this.setupCKEDITOR(element, loadedConfig);
    },

    methods: {
        /**
         * wait for CKEDITOR to be loaded then setup instance for element
         *
         * @param {HTMLElement} element
         * @param {Object} loadedConfig
         *
         * @returns {void}
         */
        setupCKEDITOR(element, loadedConfig) {
            setTimeout(() => {
                if (typeof CKEDITOR === 'undefined') {
                    this.setupCKEDITOR(element, loadedConfig);
                } else {
                    let editor = CKEDITOR.replace(element, loadedConfig);
                    element.dataset.originalValue = element.value;
                    editor.on('change', () => {
                        element.value = editor.getData();
                        let isChanged = element.value !== element.dataset.originalValue;
                        element.dispatchEvent(new CustomEvent('change', {
                            bubbles: true,
                            detail: {
                                id: element.id,
                                isChanged,
                            }
                        }));
                    });
                }
            }, 25);
        }
    }
};
