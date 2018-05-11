Vue.use(VueHotkey);
Vue.use(VeeValidate);

window._vueInstance = new Vue({
    el: 'main',

    data() {
        return {
            vueLoaded: false,
        }
    },

    created() {
        this.vueLoaded = true;
    },
});


