<template>
    <section class="dashboard-section">
        <header>
            <h2>{{ msgSearch }}</h2>
        </header>

        <div role="search">
            <div role="textsearch">
                <input type="text" v-model="searchString" v-on:keydown.stop="captureKeys">
                <button ref="searchSubmit" :disabled="!searchString || searchString.length < 3" v-on:click="searchObjects">
                    <app-icon icon="carbon:search"></app-icon>
                    {{ msgSearch }}
                </button>
            </div>
            <div role="idsearch">
                <input type="text" :placeholder="msgIdOrUname" v-model="searchId">
                <button @click.prevent="goToID()" :disabled="!searchId">
                    {{ msgId }}
                </button>
            </div>
        </div>
    </section>
</template>
<script>
import { t } from 'ttag';

export default {
    name: 'DashboardSearch',

    props: {
        q: {
            type: String,
            default: '',
        },
    },

    data() {
        return {
            msgId: t`Go to ID`,
            msgIdOrUname: t`ID or uname`,
            msgSearch: t`Search`,
            searchId: '',
            searchString: '',
        };
    },

    created() {
        if (document.referrer.endsWith('/login') && window.top !== window) {
            window.top.postMessage('login', BEDITA.base);
        }

        this.searchString = this.q;
    },

    methods: {
        captureKeys(e) {
            let key = e.which || e.keyCode || 0;
            switch (key) {
                case 13:
                    this.searchObjects();
                    break;
                case 27:
                    this.popUpAction = '';
                    break;
            }
        },
        goToID() {
            if (!this.searchId) {
                return;
            }
            window.location.href = `${BEDITA.base}/view/${this.searchId}`;
        },
        searchObjects() {
            if (this.searchString) {
                this.$refs.searchSubmit.classList.add('is-loading-spinner');
                window.location.href = BEDITA.base + '/objects?q=' + this.searchString;
            }
        },
    }
}
</script>
