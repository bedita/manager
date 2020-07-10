/**
* View component used for editing relations param
*
*
* @property {String} relationName
* @property {Object} object
* @property {Object} related
* @property {Object} schema
*
*/

import { PanelEvents } from 'app/components/panel-view';
import { t } from 'ttag';

export default {
    template: /*template*/
    `<div v-if="relationName" class="edit-relation">
        <section>
            <header class="mx-1 mt-1 tab unselectable">
                <h2><span><: t('Edit parameters of') :>: <: relationName | humanize :></span> &nbsp;</h2>
            </header>

            <div class="mx-1">
                <h2><: t('Object') :>: <: object.attributes.title :></h2>
                <div>
                    <span class="tag" :class="objectColorClass"><: object.type :></span>
                    <span class="tag"><: object.attributes.status :></span>
                </div>

                <h2 class="mt-2"><: t('Item related to') :>: <: relatedName :></h2>
                <div>
                    <span class="tag" :class="relatedColorClass"><: related.type :></span>
                    <span class="tag"><: relatedStatus :></span>
                </div>
            </div>

            <form class="mt-2 mx-1" ref="paramsForm" @change="checkParams()" @keyup="checkParams()">
                <div class="mb-1">
                    <p><: t('Priority') :></p>
                    <input class="input-narrow" type="number" step="1" v-model.number="priority" />
                </div>

                <div class="mb-1" v-for="(param, key) in schema">
                    <p :title="key"><: param.description || key :></p>

                    <!-- Boolean -> switch button true/false --->
                    <div v-if="param.type == 'boolean'">
                        <label class="switch">
                            <input type="checkbox" v-model="editingParams[key] ">
                            <span class="slider round"></span>
                        </label>
                    </div>

                    <div v-if="param.type == 'string'">
                        <!-- String, format: date-time -> datepicker --->
                        <input v-if="param.format == 'date-time'" v-datepicker time="true" v-model="editingParams[key]"></input>

                        <!-- String Enum -> select/option --->
                        <div v-else-if="param.enum !== undefined">
                            <select v-model="editingParams[key]">
                                <option v-for="item in param.enum" :value="item"><: item :></option>
                            </select>
                        </div>
                        <!-- String -> input text --->
                        <input v-else type="text" v-model="editingParams[key]">
                    </div>

                    <!-- Number -> input number --->
                    <div v-if="param.type == 'number'">
                        <label>
                            <input type="number" :name="key" step="any" v-model.number="editingParams[key]">
                        </label>
                    </div>

                    <!-- Integer -> input integer --->
                    <div v-if="param.type == 'integer'">
                        <label>
                            <input type="number" :name="key" v-model.number="editingParams[key]">
                        </label>
                    </div>
                </div>
            </form>

            <footer class="p-1">
                <button :disabled="!isModified"
                    class="has-background-info has-text-white"
                    @click.prevent="saveParams()"><: t('Save ') :></button>

                    <button class="mx-1" href="#" @click="closeParamsView()"><: t('Cancel') :></button>
            </footer>
        </section>
    </div>`,

    props: {
        relationName: {
            type: String,
            default: '',
        },
        object: {
            type: Object,
            default: () => {},
        },
        related: {
            type: Object,
            default: () => {},
        },
        schema: {
            type: Object,
            default: () => {},
        },
    },

    computed: {
        /**
         * get related object status
         *
         * @returns {String} status
         */
        relatedStatus() {
            return this.related.attributes.status;
        },

        /**
        * get related object type
        *
        * @returns {String} type
        */
        relatedType() {
            let type = '(not available)';
            if (this.related) {
                type = this.related.type;
            }

            return type;
        },

        /**
        * get related object title
        *
        * @returns {String} title
        */
        relatedName() {
            let name = '(empty)';
            if (this.related) {
                name = this.related.attributes.title;
            }

            return name;
        },

        /**
        * get main object color from type
        *
        * @returns {String} title
        */
        objectColorClass() {
            return `has-background-module-${this.object.type}`
        },

        /**
        * get related object color from type
        *
        * @returns {String} title
        */
        relatedColorClass() {
            return `has-background-module-${this.related.type}`
        },
    },

    data() {
        return {
            oldParams: {},
            editingParams: {},
            priority: null,
            isModified: false,
        }
    },

    watch: {
        related: {
            handler: function(object) {
                if (object) {
                    this.setInternalValues();
                }
            },
            deep: true,
            immediate: true,
        }
    },

    methods: {
        /**
         * set up for diff check
         *
         * @returns {void}
         */
        setInternalValues() {
            Object.assign(this.oldParams, this.related.meta.relation.params);
            Object.assign(this.editingParams, this.related.meta.relation.params);
            this.priority = this.related.meta.relation.priority;
        },

        /**
         * Save edited params
         *
         * @returns {void}
         */
        saveParams() {
            // internal prop are binded with the external
            if (Object.keys(this.editingParams).length) {
                this.related.meta.relation.params = this.editingParams;
            } else {
                delete this.related.meta.relation.params;
            }
            this.related.meta.relation.priority = this.priority;

            PanelEvents.sendBack('edit-params:save', this.related );
        },

        /**
         * Close Panel without saving
         *
         * @returns {void}
         */
        closeParamsView() {
            PanelEvents.closePanel();
        },

        /**
         * Editor diff check
         *
         * @returns {void}
         */
        checkParams() {
            this.isModified = !!Object.keys(this.editingParams).filter((index) => {
                return this.editingParams[index] !== '' && this.editingParams[index] !== this.oldParams[index];
            }).length || this.related.meta.relation.priority !== this.priority;
        },
    },
}
