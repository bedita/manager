<template>
    <div class="placeholdersList input title text" v-if="items?.length > 0">
        <div>
            <app-icon icon="carbon:image" height="24"></app-icon>
            <span>{{ items?.length }} placeholders</span>
        </div>
        <template v-for="item in items">
            <div class="placeholder-item">
                <span>{{ item.obj?.attributes?.title }}</span>
                <placeholder-params
                    :field="field"
                    :id="item.id"
                    :text="item.text"
                    :value="item.params_raw"
                />
            </div>
        </template>
    </div>
</template>
<script>
import { EventBus } from 'app/components/event-bus';

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
            richtextContent: '',
            items: [],
        };
    },
    mounted() {
        this.$nextTick(async () => {
            this.richtextContent = this.value || document.getElementById(this.field).value || '';
            await this.extractPlaceholders();
            EventBus.listen('refresh-placeholders', this.refresh);
        });
    },
    methods: {
        async extractPlaceholders() {
            const regex = /BE-PLACEHOLDER\.(\d+)(?:\.([-A-Za-z0-9+=]{1,100}|=[^=]|={3,}))?/;
            const tmpDom = document.createElement('div');
            tmpDom.innerHTML = this.richtextContent;
            tmpDom.querySelectorAll('*[data-placeholder]').forEach(async (item) => {
                for (const subnode of item.childNodes) {
                    if (!subnode.textContent || subnode.nodeType !== Node.COMMENT_NODE) {
                        continue;
                    }
                    const m = subnode.textContent.match(regex);
                    if (!m) {
                        continue;
                    }
                    const o = await this.fetchObject(m[1]);
                    this.items.push({
                        text: m[0],
                        id: m[1],
                        obj: o,
                        params_raw: m[2],
                        params: atob(m[2]),
                    });
                }
            });
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
        async refresh(payload) {
            const id = payload?.id;
            if (id !== this.field) {
                return;
            }
            if (payload?.content === this.richtextContent) {
                return;
            }
            this.richtextContent = payload.content;
            this.items = [];
            await this.extractPlaceholders();
            this.$forceUpdate();
        },
    },
}
</script>
<style>
div.placeholdersList > div.placeholder-item {
    display: grid;
    grid-template-columns: 50% 1fr;
    text-align: left;
    align-items: center;
    gap: 8px;
    margin: 4px 0;
    border-bottom: dotted 1px #ccc;
}
</style>
