
import Vue from 'vue';

/**
 * BEdita Helper Object
 */
const BELoader = {

    /**
     * load Beplugins' Vue components (global)
     *
     * @return {void}
     */
    loadBeditaPlugins() {
        const plugins = BEDITA.plugins;

        plugins.forEach(element => {
            const vueComponent = window[element] || global[element];
            if (!vueComponent || !vueComponent.default) {
                return;
            }
            const BEPlugins = vueComponent.default;

            Object.keys(BEPlugins).forEach(componentName => {
                if (typeof BEPlugins[componentName] === 'object') {
                    Vue.component(componentName, BEPlugins[componentName]);

                    console.debug(
                        `%c[${componentName}]%c component succesfully registred from %c${element}%c Plugin`,
                        'color: blue',
                        'color: black',
                        'color: red',
                        'color: black'
                    );
                }
            });
        });
    }
}

export {
    BELoader,
};

