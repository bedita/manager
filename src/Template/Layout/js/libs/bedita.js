
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

        Object.keys(plugins).forEach(element => {
            const BEPlugins = (window[element] || global[element]).default;

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

