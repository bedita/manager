
import Vue from 'vue';

const BELoader = {
    loadBeditaPlugins() {
        const plugins = BEDITA.plugins;
        Object.keys(plugins).forEach(element => {
            if (element !== 'DebugKit') {
                const BEPlugin = (window[element] || global[element]).default;

                Object.keys(BEPlugin).forEach(componentName => {
                    console.debug(
                        `%c[${componentName}]%c component succesfully registred from %c${element}%c Plugin`,
                        'color: blue',
                        'color: black',
                        'color: red',
                        'color: black'
                    );
                    Vue.component(componentName, BEPlugin[componentName]);
                });
            }
        });
    }
}

export {
    BELoader,
};

