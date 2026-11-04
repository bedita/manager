<template>
    <section
        class="dashboard-section recent-activity"
        :aria-busy="loading"
    >
        <header>
            <h2>{{ msgRecentActivity }}</h2>
        </header>

        <div v-if="pagination">
            <nav class="pagination has-text-size-smallest">
                <div class="count-activities">
                    <span class="has-font-weight-bold">{{ pagination.count }}</span>
                    <span>{{ msgActivities }}</span>
                </div>
                <div class="page-size">
                    <span>{{ msgSize }}</span>
                    <select
                        class="page-size-selector has-background-gray-700 has-border-gray-700 has-font-weight-light has-text-gray-200 has-text-size-smallest"
                        v-model="pageSize"
                        @change="changePage"
                    >
                        <option
                            v-for="pSize in pageSizes"
                            :key="pSize"
                        >
                            {{ pSize }}
                        </option>
                    </select>
                </div>
                <div class="pagination-buttons">
                    <div>
                        <!-- first page -->
                        <button
                            :class="pageButtonClass"
                            @click.prevent="changePage(1)"
                            v-if="pagination.page > 1"
                        >
                            {{ 1 }}
                        </button>
                        <!-- delimiter -->
                        <span
                            class="pages-delimiter"
                            v-if="pagination.page > 3"
                        />
                        <!-- prev page -->
                        <button
                            :class="pageButtonClass"
                            @click.prevent="changePage(pagination.page - 1)"
                            v-if="pagination.page > 2"
                        >
                            {{ pagination.page - 1 }}
                        </button>
                        <!-- current page -->
                        <input
                            size="2"
                            class="ml-05"
                            :class="pagination.page === 1 ? 'mr-05' : 'ml-05'"
                            v-model="currentPage"
                        >
                        <!-- next page -->
                        <button
                            :class="pageButtonClass"
                            @click.prevent="changePage(pagination.page + 1)"
                            v-if="pagination.page < pagination.page_count - 1"
                        >
                            {{ pagination.page + 1 }}
                        </button>
                        <!-- delimiter -->
                        <span
                            class="pages-delimiter"
                            v-if="pagination.page < pagination.page_count - 2"
                        />
                        <!-- last page -->
                        <button
                            :class="pageButtonClass"
                            @click.prevent="changePage(pagination.page_count)"
                            v-if="pagination.page < pagination.page_count"
                        >
                            {{ pagination.page_count }}
                        </button>
                    </div>
                </div>
            </nav>
        </div>
        <div
            class="is-loading-spinner mt-05"
            role="status"
            :aria-label="msgLoading"
            v-if="loading"
        />
        <div
            class="activity-list"
            role="table"
            :aria-label="msgRecentActivity"
            v-if="activities.length > 0 && !loading"
        >
            <div
                class="activity-row activity-heading"
                role="row"
            >
                <div role="columnheader">
                    {{ msgTitle }}
                </div>
                <div role="columnheader">
                    {{ msgType }}
                </div>
                <div role="columnheader">
                    {{ msgAction }}
                </div>
                <div role="columnheader">
                    {{ msgChanged }}
                </div>
                <div role="columnheader">
                    {{ msgDate }}
                </div>
            </div>
            <template v-for="item in activities">
                <a
                    :key="item.id + '-object'"
                    :href="url(item)"
                    class="activity-row"
                    :class="[`object-status-${item.object_status}`, actionClass(item)]"
                    role="row"
                    v-if="item.object_type"
                >
                    <div
                        class="activity-title"
                        role="cell"
                    >
                        {{ title(item) }}
                    </div>
                    <div
                        class="activity-type"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgType }}</span>
                        <span :class="`tag has-background-module-${item.object_type}`">{{ t(item.object_type || '?') }}</span>
                    </div>
                    <div
                        class="activity-secondary activity-action"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgAction }}</span>
                        <span class="activity-action-value">{{ item.meta.user_action }}</span>
                    </div>
                    <div
                        class="activity-secondary activity-changed"
                        :title="changes(item, false)"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgChanged }}</span>
                        <span class="activity-changes">{{ changes(item, false) }}</span>
                    </div>
                    <div
                        class="activity-secondary activity-date"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgDate }}</span>
                        {{ $helpers.formatDate(item.meta.created) }}
                    </div>
                </a>
                <div
                    class="activity-row object-status-deleted activity-action-deleted activity-missing-object"
                    :key="item.id + '-deleted'"
                    role="row"
                    v-else
                >
                    <div
                        class="activity-title"
                        role="cell"
                    >
                        <span class="activity-title-label">{{ title(item) }}</span>
                        <span class="activity-deleted">{{ msgDeleted }}</span>
                    </div>
                    <div
                        class="activity-type"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgType }}</span>
                        <span :class="`tag`">?</span>
                    </div>
                    <div
                        class="activity-secondary activity-action"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgAction }}</span>
                        <span class="activity-action-value">{{ item.meta.user_action }}</span>
                    </div>
                    <div
                        class="activity-secondary activity-changed"
                        :title="changes(item, false)"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgChanged }}</span>
                        <span class="activity-changes">{{ changes(item, false) }}</span>
                    </div>
                    <div
                        class="activity-secondary activity-date"
                        role="cell"
                    >
                        <span class="activity-field-label">{{ msgDate }}</span>
                        {{ $helpers.formatDate(item.meta.created) }}
                    </div>
                </div>
            </template>
        </div>
        <div
            class="activity-state activity-empty"
            role="status"
            aria-live="polite"
            v-if="!loading && !error && !activities.length"
        >
            <h3>{{ msgNoActivityFound }}</h3>
        </div>
        <div
            class="activity-state activity-error"
            role="alert"
            v-if="!loading && error && !activities.length"
        >
            {{ error }}
        </div>
    </section>
</template>
<script>
import { t } from 'ttag';

const API_URL = new URL(BEDITA.base).pathname;
const API_OPTIONS = {
    credentials: 'same-origin',
    headers: {
        'accept': 'application/json',
    }
};

export default {
    name: 'RecentActivity',

    props: {
        pageSizes: {
            type: Array,
            default: () => [10, 20, 50, 100, 500],
        },
        userId: {
            type: Number,
            default: 0,
        },
    },

    data() {
        return {
            activities: [],
            currentPage: 1,
            error: '',
            loading: false,
            pageSize: null,
            pagination: {},
            msgAction: t`Action`,
            msgActivities: t`activities`,
            msgActivity: t`Activity`,
            msgChanged: t`Changed`,
            msgDate: t`Date`,
            msgDeleted: t`Deleted`,
            msgLoading: t`Loading`,
            msgNoActivityFound: t`No activity found`,
            msgRecentActivity: t`Your recent activity`,
            msgSize: t`Size`,
            msgTitle: t`Title or #id uname`,
            msgType: t`Type`,
        };
    },

    mounted() {
        this.$nextTick(() => {
            this.pageSize = this.pageSizes[0];
            this.changePage();
        });
    },

    methods: {
        async changePage(page = 1) {
            await this.fetchObjects(this.userId, page, this.pageSize);
        },
        changes(item, truncate = true) {
            if (truncate) {
                return this.$helpers.truncate(Object.keys(item.meta.changed).join(', '), 50);
            }

            return Object.keys(item.meta.changed).join(', ');
        },
        async fetchObjects(userId, page, pageSize) {
            try {
                this.loading = true;
                this.error = '';
                const response = await fetch(`${API_URL}history/get?filter[user_id]=${userId}&page=${page}&page_size=${pageSize}&sort=-created`, API_OPTIONS);
                const json = await response.json();
                this.pagination = json.meta.pagination;
                this.currentPage = json.meta.pagination.page;
                this.activities = [...(json.data || [])];
                const ids = this.activities.filter(item => item.meta.resource_id).map(item => item.meta.resource_id).filter((v, i, a) => a.indexOf(v) === i).map(i=>Number(i));
                const objectResponse = await fetch(`${API_URL}history/objects?filter[id]=${ids.join(',')}&page_size=${pageSize}`, API_OPTIONS);
                const objectJson = await objectResponse.json();
                for (const item of this.activities) {
                    const object = objectJson.data.find(obj => obj.id === item.meta.resource_id);
                    if (!object) {
                        continue;
                    }
                    item.object_title = object.attributes.title;
                    item.object_uname = object.attributes.uname;
                    item.object_status = object.attributes.status;
                    item.object_type = object.type;
                }
            } catch (e) {
                this.error = e?.message || e;
            } finally {
                this.loading = false;
            }
        },
        getTextFromHtml(s) {
            const e = document.createElement('div');
            e.innerHTML = s;

            return e.textContent || e.innerText || '';
        },
        actionClass(item) {
            const action = String(item.meta.user_action || '').toLowerCase();
            if (
                action.includes('delet') ||
                action.includes('remov') ||
                action.includes('elimin') ||
                action.includes('rimos')
            ) {
                return 'activity-action-deleted';
            }
            if (
                action.includes('creat') ||
                action.includes('add') ||
                action.includes('aggiun')
            ) {
                return 'activity-action-created';
            }
            if (
                action.includes('updat') ||
                action.includes('edit') ||
                action.includes('modif') ||
                action.includes('aggiorn')
            ) {
                return 'activity-action-updated';
            }

            return '';
        },
        pageButtonClass() {
            return 'has-text-size-smallest button is-width-auto button-outlined';
        },
        title(item) {
            if (item.object_title) {
                return this.getTextFromHtml(item.object_title);
            }
            const id = `#${item.meta.resource_id}`;
            const uname = item.object_uname || t`(deleted)`;

            return `${id} ${uname}`;
        },
        url(item) {
            return `${item.object_type}/view/${item.meta.resource_id}`;
        },
    },
}
</script>
<style scoped>
.recent-activity {
    container-type: inline-size;
    width: 100%;
    max-width: 68rem;
    min-width: 0;
    margin-top: 1rem;
    padding: 1rem;
    border: 1px solid #495057;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.025);
}

.recent-activity.dashboard-section > header > h2 {
    padding-top: 0;
}

.recent-activity .activity-list {
    width: 100%;
}

.recent-activity .activity-row {
    display: grid;
    grid-template-columns: minmax(0, 38fr) minmax(0, 12fr) minmax(0, 12fr) minmax(0, 23fr) minmax(0, 15fr);
    column-gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #495057;
    align-items: start;
}

.recent-activity .activity-row > div {
    min-width: 0;
    white-space: normal;
    overflow-wrap: anywhere;
    line-height: 1.6;
}

.recent-activity a.activity-row:hover {
    background: rgba(255, 255, 255, 0.025);
}

.recent-activity .object-status-off,
.recent-activity .object-status-draft,
.recent-activity .object-status-deleted {
    color: #8e959e;
}

.recent-activity .activity-type .tag {
    max-width: 100%;
    height: auto;
    min-height: 2em;
    white-space: normal;
    overflow-wrap: anywhere;
}

.recent-activity .activity-heading > div {
    color: #8e959e;
    font-size: 0.75rem;
    font-weight: 500;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.recent-activity .activity-title {
    font-weight: 500;
}

.recent-activity .activity-secondary {
    color: #8e959e;
    font-size: 0.75rem;
}

.recent-activity .activity-field-label {
    display: none;
}

.recent-activity .activity-changes {
    display: inline-block;
    max-width: 100%;
    padding: 0.2em 0.6em;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.08);
    color: #ced4da;
    vertical-align: middle;
}

.recent-activity .activity-action-value {
    display: inline-flex;
    align-items: baseline;
    gap: 0.25em;
}

.recent-activity .activity-action-value::before {
    display: inline-block;
    flex: 0 0 auto;
    width: 1.2em;
    font-weight: 500;
    text-align: center;
}

.recent-activity .activity-action-created .activity-action-value::before {
    color: #58c99b;
    content: "+";
}

.recent-activity .activity-action-updated .activity-action-value::before {
    color: #68b7e8;
    content: "↻";
}

.recent-activity .activity-action-deleted .activity-action-value::before {
    color: #f08080;
    content: "−";
}

.recent-activity .activity-deleted {
    display: inline-block;
    margin-left: 0.5em;
    padding: 0.15em 0.55em;
    border-radius: 999px;
    background: rgba(240, 128, 128, 0.16);
    color: #f3a0a0;
    font-size: 0.7rem;
    font-weight: 500;
    text-decoration: none;
    vertical-align: middle;
}

.recent-activity .activity-missing-object {
    text-decoration: none;
}

.recent-activity .activity-title-label {
    text-decoration: line-through;
}

.recent-activity .activity-state {
    margin-top: 0.75rem;
    padding: 1rem;
    border: 1px solid #495057;
    border-radius: 4px;
    background: rgba(255, 255, 255, 0.025);
}

.recent-activity .activity-list > .activity-row:last-child {
    margin-bottom: 0;
}

.recent-activity .activity-empty {
    border-left: 3px solid #68b7e8;
    color: #ced4da;
}

.recent-activity .activity-empty h3 {
    margin: 0;
    font-size: 1rem;
    font-weight: 500;
}

.recent-activity .activity-error {
    border-left: 3px solid #f08080;
    color: #f3a0a0;
}

.recent-activity a.activity-row:focus-visible,
.recent-activity button:focus-visible,
.recent-activity input:focus-visible,
.recent-activity select:focus-visible {
    outline: 2px solid #68b7e8;
    outline-offset: 2px;
}

@container (max-width: 600px) {
    .recent-activity .activity-heading {
        display: none;
    }

    .recent-activity .activity-row:not(.activity-heading) {
        grid-template-columns: minmax(0, 1fr);
        gap: 1rem;
        margin-bottom: 0.75rem;
        padding: 0.75rem;
        border: 1px solid #495057;
        border-radius: 4px;
        background: #343a40;
    }

    .recent-activity .activity-row > div {
        display: block;
        min-width: 0;
        width: auto;
        height: auto;
        line-height: 1.6;
        padding: 0;
        border: 0;
        white-space: normal;
    }

    .recent-activity .activity-row > .activity-title {
        grid-column: 1 / -1;
        display: block;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #495057;
        overflow-wrap: anywhere;
    }

    .recent-activity .activity-field-label {
        display: block;
        margin-bottom: 0.35rem;
        color: #8e959e;
        font-size: 0.65rem;
        font-weight: 500;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .recent-activity .activity-row > .activity-changed {
        grid-column: 1 / -1;
        min-width: 0;
    }

    .recent-activity .activity-row .activity-changes {
        min-width: 0;
        max-width: 100%;
        overflow: visible;
        white-space: normal;
        overflow-wrap: anywhere;
        text-overflow: clip;
    }

    .recent-activity .activity-row > .activity-date {
        grid-column: 1 / -1;
    }

    .recent-activity nav.pagination {
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .recent-activity nav.pagination > div:not(:first-child) {
        margin-left: 0;
    }
}
</style>
