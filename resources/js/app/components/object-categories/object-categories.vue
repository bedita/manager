<template>
    <div class="object-categories">
        <div class="is-flex">
            <input
                type="text"
                :placeholder="msgSearch"
                @input="onSearchCategory"
                ref="searchCat"
            >
            <button
                type="button"
                class="button button-outlined ml-1"
                :disabled="searchInCategories.length === 0"
                @click.stop="$refs.searchCat.value = ''; searchInCategories = ''">
                <app-icon icon="carbon:filter-reset"></app-icon>
                <span class="ml-05">{{ msgEmpty }}</span>
            </button>
        </div>
        <details :open="forceOpen || countSelectedCommon() > 0 || foundInCommon()" v-if="common?.length > 0">
            <summary>
                GLOBAL
                <span
                    class="tag is-smallest is-black mx-05"
                    :class="countSelectedCommon() === 0 ? 'empty' : ''"
                >
                    {{ countSelectedCommon() }} / {{ common.length }}
                </span>
            </summary>
            <div class="categories-children">
                <div
                    class="checkbox"
                    v-for="child in common"
                    :key="child.name"
                >
                    <label v-show="searchInCategories === '' || foundIn(child)">
                        <input
                            :id="`categories-${child.name}`"
                            type="checkbox"
                            name="categories[]"
                            :value="child.name"
                            :checked="child.name in selected"
                            v-model="selected"
                        >
                        <span v-html="title(child)"></span>
                    </label>
                </div>
            </div>
        </details>
        <details :open="forceOpen || countSelectedByParent(parent.id) > 0 || foundInParent(parent)" v-for="parent in root" :key="parent.name">
            <summary>
                {{ parent.label || parent.name }}
                <span
                    class="tag is-smallest is-black mx-05"
                    :class="countSelectedByParent(parent.id) === 0 ? 'empty' : ''"
                >
                    {{ countSelectedByParent(parent.id) }} / {{ children[parent.id].length }}
                </span>
            </summary>
            <div class="categories-children">
                <div
                    class="checkbox"
                    v-for="child in children[parent.id]"
                    :key="child.name"
                >
                    <label v-show="searchInCategories === '' || foundIn(child)">
                        <input
                            :id="`categories-${child.name}`"
                            type="checkbox"
                            name="categories[]"
                            :value="child.name"
                            :checked="child.name in selected"
                            v-model="selected"
                        >
                        <span v-html="title(child)"></span>
                    </label>
                </div>
            </div>
        </details>
    </div>
</template>
<script>
import { t } from 'ttag';

let debouncedSearchCategory = null;

export default {
    name: 'ObjectCategories',
    props: {
        modelCategories: {
            type: Array,
            required: true
        },
        value: {
            type: Array,
            default: () => []
        },
    },
    data() {
        return {
            children: {},
            common: [],
            forceOpen: false,
            root: [],
            searchInCategories: '',
            selected: [],
            msgEmpty: t`Empty`,
            msgSearch: t`Search on categories`,
        }
    },
    mounted() {
        this.$nextTick(() => {
            this.selected = this.value;
            this.root = this.modelCategories.filter(category => category.parent_id === null);
            this.root.sort((a, b) => {
                if (a.label && b.label) {
                    return a.label.localeCompare(b.label);
                }
                return a.name.localeCompare(b.name);
            });
            this.children = this.modelCategories.reduce((acc, category) => {
                if (!acc[category.parent_id]) {
                    acc[category.parent_id] = [];
                }
                acc[category.parent_id].push(category);
                return acc;
            }, {});
            this.root = this.root.filter(category => this.children[category.id]);
            this.common = this.modelCategories.filter(category => category.parent_id === null && !this.children[category.id]);
            for (const key in this.children) {
                this.children[key].sort((a, b) => {
                    if (a.label && b.label) {
                        return a.label.localeCompare(b.label);
                    }
                    return a.name.localeCompare(b.name);
                });
            }
            this.common.sort((a, b) => {
                if (a.label && b.label) {
                    return a.label.localeCompare(b.label);
                }
                return a.name.localeCompare(b.name);
            });
            this.selected = this.value.map(category => category.name);
            let groupsNumber = Object.keys(this.children).length;
            groupsNumber += this.common.length > 0 ? 1 : 0;
            this.forceOpen = groupsNumber <= 3 || this.modelCategories.length <= 20;
        });
    },
    methods: {
        countSelectedCommon() {
            return this.selected.filter(category => {
                const arr = this.common || [];

                return arr.find(child => child.name === category);
            }).length;
        },
        countSelectedByParent(parentId) {
            return this.selected.filter(category => {
                const cc = this.children || {};
                const arr = cc?.[parentId] || [];

                return arr.find(child => child.name === category);
            }).length;
        },
        foundIn(cat) {
            return (cat?.label && cat?.label?.toLowerCase().indexOf(this.searchInCategories.toLowerCase()) !== -1);
        },
        foundInCommon() {
            if (!this.searchInCategories) {
                return false;
            }
            return this.common.find(cat => this.foundIn(cat));
        },
        foundInParent(parent) {
            if (!this.searchInCategories) {
                return false;
            }
            return this.children[parent.id].find(cat => this.foundIn(cat));
        },
        onSearchCategory(e) {
            if (!debouncedSearchCategory) {
                debouncedSearchCategory = this.$helpers.debounce((val) => {
                    this.searchInCategories = val.length < 3 ? '' : val;
                }, 300);
            }

            return debouncedSearchCategory(e.target.value);
        },
        title(cat) {
            let title = cat.label || cat.name;
            const div = document.createElement('div');
            div.innerHTML = title;
            title = div.innerText;
            if (!this.searchInCategories) {
                return title;
            }

            return title.replace(new RegExp(`(${this.searchInCategories})`, 'i'), '<span class="has-text-decoration-underline">$1</span>');
        },
    },
}
</script>
<style>
.object-categories details {
    padding: .5rem 0;
}

.object-categories details:not(:first-child) {
    border-top: 1px dashed #555;
}

.object-categories details>summary {
  list-style-type: none;
  outline: none;
  cursor: pointer;
  font-size: 1rem;
  text-transform: uppercase;
}

.object-categories details>summary::-webkit-details-marker {
  display: none;
}

.object-categories details>summary::before {
  content: '+ ';
  font-size: 1.2rem;
}

.object-categories details[open]>summary::before {
  content: '- ';
  font-size: 1.2rem;
}

.object-categories details:not([open]) > summary {
    opacity: 0.7;
}

.object-categories .categories-children {
  columns: 2 auto;
}

.object-categories .checkbox > label {
  display: block;
}

.object-categories .checkbox > label {
    cursor: pointer;
}
</style>
