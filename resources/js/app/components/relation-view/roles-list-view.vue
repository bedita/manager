<template>
    <div class="roles-list-view">
        <div>
            <div v-for="groupName in Object.keys(objectsByGroups)">
                <h4 class="is-small has-font-weight-bold has-text-transform-upper">{{ groupName }}</h4>
                <div v-for="role in objectsByGroups[groupName]">
                    <input type="checkbox" :value="role" :disabled="userRolePriority > role.meta.priority" v-model="checkedRelations"/>
                    <span class="mx-05">{{ getRoleLabel(role.attributes.name, role.attributes.description) }}</span>
                </div>
            </div>
        </div>

        <div class="save-relations">
            <input type="hidden" :id="`${relationName}addRelated`" :name="`relations[${relationName}][addRelated]`" v-model="relationsData" />
        </div>
    </div>
</template>

<script>
import RelationshipsView from 'app/components/relation-view/relationships-view/relationships-view';

export default {
    name: 'roles-list-view',

    extends: RelationshipsView,

    props: {
        groups: {
            type: Object,
            default: {},
        },
        relatedObjects: {
            type: Array,
            default: () => [],
        },
        userRolePriority: {
            type: Number,
            default: () => 500,
        },
        userRoles: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            method: 'resources',
            objectsByGroups: {},
            removedRelations: [],
        }
    },

    async mounted() {
        await this.loadObjects();
        this.$nextTick(() => {
            let groups = this.groups;
            if (Object.keys(groups).length === 0) {
                for (let role of this.objects) {
                    if (!groups[this.getGroup(role.attributes.name)]) {
                        groups[this.getGroup(role.attributes.name)] = new Array();
                    }
                    groups[this.getGroup(role.attributes.name)].push(role.attributes.name);
                }
            }
            for (let groupName of Object.keys(groups)) {
                this.objectsByGroups[groupName] = new Array();
                for (let roleName of groups[groupName]) {
                    const role = this.objects.filter((v) => v.attributes.name === roleName)[0];
                    this.objectsByGroups[groupName].push(role);
                }
            }
            this.objectsByGroups = {...this.objectsByGroups};
        });
    },

    computed: {
        checkedRelations: {

            get: function() {
                return this.relatedObjects.concat(this.pendingRelations).filter(rel => !this.containsId(this.removedRelations, rel.id));
            },

            set: function(newValue) {
                // handles relations to be added
                let relationsToAdd = newValue.filter((rel) => !this.containsId(this.relatedObjects, rel.id));
                this.pendingRelations = relationsToAdd;

                // handles relations to be removed
                let relationsToRemove = this.relatedObjects.filter((rel) => !this.containsId(newValue, rel.id));
                this.removedRelations = relationsToRemove;

                // emit event to pass data to parent
                this.$emit('remove-relations', relationsToRemove);
            }
        },
    },

    methods: {
        getGroup(roleName) {
            const index = roleName.indexOf('__');
            if (index < 0) {
                return 'BEdita Manager';
            }

            return this.$helpers.humanize(roleName.substr(0, index));
        },
        getRoleLabel(roleName, roleDescription) {
            const index = roleName.indexOf('__');
            if (index < 0) {
                return roleName;
            }
            if (roleDescription) {
                return roleDescription;
            }

            return this.$helpers.humanize(roleName.substr(index + 2));
        },
    },
}
</script>
