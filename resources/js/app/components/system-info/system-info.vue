<template>
    <div>
        <p v-for="item,key of infos">
            <span class="key">{{ key }}</span>:
            <span class="version">{{ item }}</span>
        </p>
    </div>
</template>
<script>
import Vue from 'vue';

export default {
    name: 'SystemInfo',
    props: {
        data: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            infos: {},
        }
    },

    mounted() {
        this.$nextTick(() => {
            this.infos = JSON.parse(this.data) || {};
            if (this.infos['Vuejs'] !== undefined) {
                this.infos['Vuejs'] = Vue.version;
            }
        });
    },
}
</script>
<style>
span.key {
    font-style: italic;
    color: yellow;
}
span.version {
    font-family: 'Courier New', Courier, monospace;
}
</style>
