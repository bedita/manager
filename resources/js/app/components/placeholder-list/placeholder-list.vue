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
            <div class="placeholder-item-name">
                <app-icon icon="carbon:document-audio"
                          height="24"
                          v-if="item.obj?.type === 'audio'"
                />
                <app-icon icon="carbon:document"
                          height="24"
                          v-if="item.obj?.type === 'documents'"
                />
                <app-icon icon="carbon:event"
                          height="24"
                          v-if="item.obj?.type === 'events'"
                />
                <app-icon icon="carbon:document-blank"
                          height="24"
                          v-if="item.obj?.type === 'files'"
                />
                <app-icon icon="carbon:image"
                          height="24"
                          v-if="item.obj?.type === 'images'"
                />
                <app-icon icon="carbon:link"
                          height="24"
                          v-if="item.obj?.type === 'links'"
                />
                <app-icon icon="carbon:location"
                          height="24"
                          v-if="item.obj?.type === 'locations'"
                />
                <app-icon icon="carbon:calendar"
                          height="24"
                          v-if="item.obj?.type === 'news'"
                />
                <app-icon icon="carbon:person"
                          height="24"
                          v-if="item.obj?.type === 'profiles'"
                />
                <app-icon icon="carbon:wikis"
                          height="24"
                          v-if="item.obj?.type === 'publications'"
                />
                <app-icon icon="carbon:video"
                          height="24"
                          v-if="item.obj?.type === 'videos'"
                />

                <span>{{ item.obj?.title }}</span>
            </div>
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
import { PlaceholderBus as placeholderBus } from 'app/components/placeholder-bus';
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
            cache: {},
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
            placeholderBus.listen('refresh-placeholders', this.debounceRefresh);
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
                    params_raw: m[2] || '',
                    params: this.$helpers.base64ToUtf8(m[2] || ''),
                });
            }
        },
        async fetchObject(id) {
            if (this.cache[id]) {
                return this.cache[id];
            }
            const baseUrl = new URL(BEDITA.base).pathname;
            const response = await fetch(`${baseUrl}resources/get/${id}`, {
                credentials: 'same-origin',
                headers: {
                    accept: 'application/json',
                }
            });
            const responseJson = await response.json();
            const data = responseJson?.data || {};
            this.cache[id] = {
                type: data?.type,
                title: data?.attributes?.title,
            };

            return this.cache[id];
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
    grid-template-columns: 35% 60% 1fr;
    text-align: left;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    margin: 4px 0;
    border-top: dotted 1px #ccc;
}
div.placeholdersList > div.placeholder-item > div.placeholder-item-name {
    display: flex;
    align-items: start;
    align-self: flex-start;
    gap: 8px;
    margin: 4px 0;
}
</style>
