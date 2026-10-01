<template>
    <div class="show-hide">
        <button
            class="button button-outlined is-expanded"
            @click.prevent.stop="show"
            v-if="!visible"
        >
            <app-icon icon="carbon:add" />
            <span class="ml-05">{{ msgShow }}</span>
        </button>
        <button
            class="button button-outlined is-expanded"
            @click.prevent.stop="hide"
            v-if="visible"
        >
            <app-icon icon="carbon:subtract" />
            <span class="ml-05">{{ msgHide }}</span>
        </button>
    </div>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'ShowHide',

    props: {
        field: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            visible: false,
            msgHide: t`Hide`,
            msgShow: t`Show`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            document.getElementById(this.field).style.display = 'none';
            this.visible = false;
        });
    },

    methods: {
        hide() {
            this.visible = false;
            document.getElementById(this.field).style.display = 'none';
        },
        show() {
            this.visible = true;
            document.getElementById(this.field).style.display = 'block';
        },
    },
}
</script>
<style>
div.show-hide {
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: center;
    width: 150px;
}
div.show-hide > button {
    margin-right: 0.5rem;
    border: none;
}
</style>
