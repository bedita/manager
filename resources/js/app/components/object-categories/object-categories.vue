<template>
    <div class="object-categories">
        <details :open="forceOpen || countSelectedCommon() > 0" v-if="common?.length > 0">
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
                    <label>
                        <input
                            :id="`categories-${child.name}`"
                            type="checkbox"
                            name="categories[]"
                            :value="child.name"
                            :checked="child.name in selected"
                            v-model="selected"
                        >
                        {{ child.label || child.name }}
                    </label>
                </div>
            </div>
        </details>
        <details :open="forceOpen || countSelectedByParent(parent.id) > 0" v-for="parent in root" :key="parent.name">
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
                    <label>
                        <input
                            :id="`categories-${child.name}`"
                            type="checkbox"
                            name="categories[]"
                            :value="child.name"
                            :checked="child.name in selected"
                            v-model="selected"
                        >
                        {{ child.label || child.name }}
                    </label>
                </div>
            </div>
        </details>
    </div>
</template>
<script>
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
            selected: [],
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
    },
}
</script>
<style>
.object-categories details>summary {
  list-style-type: none;
  outline: none;
  cursor: pointer;
  border-bottom: 1px solid #eee;
  padding: 10px;
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

.object-categories details[open]>summary {
  margin-bottom: 0.5rem;
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
