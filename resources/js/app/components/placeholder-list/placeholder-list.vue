<template>
    <div
        class="placeholdersList input title text"
        v-if="items?.length > 0"
    >
        <div class="header">
            <span>{{ items?.length }} {{ msgPlaceholders }}</span>
        </div>
        <div
            class="placeholder-item"
            v-for="item in items"
            :key="itemKey(item)"
        >
            <app-icon v-if="item.obj?.type === 'audio'" icon="carbon:document-audio" height="24" />
            <app-icon v-if="item.obj?.type === 'documents'" icon="carbon:document" height="24" />
            <app-icon v-if="item.obj?.type === 'events'" icon="carbon:event" height="24" />
            <app-icon v-if="item.obj?.type === 'files'" icon="carbon:document-blank" height="24" />
            <app-icon v-if="item.obj?.type === 'images'" icon="carbon:image" height="24" />
            <app-icon v-if="item.obj?.type === 'links'" icon="carbon:link" height="24" />
            <app-icon v-if="item.obj?.type === 'locations'" icon="carbon:location" height="24" />
            <app-icon v-if="item.obj?.type === 'news'" icon="carbon:calendar" height="24" />
            <app-icon v-if="item.obj?.type === 'profiles'" icon="carbon:person" height="24" />
            <app-icon v-if="item.obj?.type === 'publications'" icon="carbon:wikis" height="24" />
            <app-icon v-if="item.obj?.type === 'videos'" icon="carbon:video" height="24" />

            <span>{{ item.obj?.attributes?.title }}</span>
            <placeholder-params
                :id="item.id"
                :field="field"
                :text="item.text"
                :type="item.obj?.type"
                :value="item.params_raw"
            />
        </div>
    </div>
</template>
<script>
import { EventBus } from 'app/components/event-bus';
import { t } from 'ttag';

export default {
    name: 'PlaceholderList',
    components: {
        PlaceholderParams: () => import('./placeholder-params.vue'),
    },
    props: {
        field: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            debounceRefreshHandle: null,
            items: [],
            richtextContent: '',
            msgPlaceholders: t`placeholders`,
        };
    },
    mounted() {
        this.$nextTick(async () => {
            this.richtextContent = this.value || document.getElementById(this.field).value || '';
            await this.extractPlaceholders();
            EventBus.listen('refresh-placeholders', this.debounceRefresh);
        });
    },
    methods: {
        async extractPlaceholders(content) {
            const regex = /BE-PLACEHOLDER\.(\d+)(?:\.([a-zA-Z0-9+/]+={0,2}))?/;
            const tmpDom = document.createElement('div');
            tmpDom.innerHTML = content || this.richtextContent;
            let matches = [];
            tmpDom.querySelectorAll('*[data-placeholder]').forEach(async (item) => {
                for (const subnode of item.childNodes) {
                    if (!subnode.textContent || subnode.nodeType !== Node.COMMENT_NODE) {
                        continue;
                    }
                    const m = subnode.textContent.match(regex);
                    if (!m) {
                        continue;
                    }
                    matches.push(m);
                }
            });
            for (const m of matches) {
                const o = await this.fetchObject(m[1]);
                this.items.push({
                    text: m[0],
                    id: m[1],
                    obj: o,
                    params_raw: m[2],
                    params: atob(m[2]),
                });
            }
        },
        async fetchObject(id) {
            const baseUrl = new URL(BEDITA.base).pathname;
            const response = await fetch(`${baseUrl}api/objects/${id}`, {
                credentials: 'same-origin',
                headers: {
                    accept: 'application/json',
                }
            });
            const responseJson = await response.json();

            return responseJson?.data || null;
        },
        itemKey(item) {
            return `${item.id}-${item.params_raw}`;
        },
        async debounceRefresh(payload) {
            if (!this.debounceRefreshHandle) {
                this.debounceRefreshHandle = this.$helpers.debounce((val) => {
                    this.refresh(val);
                }, 1000);
            }
            return this.debounceRefreshHandle(payload);
        },
        async refresh(payload) {
            const id = payload?.id;
            if (id !== this.field) {
                return;
            }
            if (payload?.content === this.richtextContent) {
                return;
            }
            this.items = [];
            await this.extractPlaceholders(payload.content);
            this.richtextContent = payload.content;
            this.$forceUpdate();
        },
    },
}
</script>
<style>
div.placeholdersList {
    padding: 8px;
    margin: 8px 0;
    border-radius: 8px;
    border: solid #d6d1d1 1px;
}
div.placeholdersList > div.header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 1.2em;
    color: white;
}
div.placeholdersList > div.placeholder-item {
    display: grid;
    grid-template-columns: 5% 60% 1fr;
    text-align: left;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
    border-top: dotted 1px #ccc;
}
</style>
