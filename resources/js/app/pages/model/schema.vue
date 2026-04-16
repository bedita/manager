<template>
    <section class="model-schema">
        <div class="schema-toolbar">
            <div class="stats">
                <span class="pill">Object types: {{ nodes.length }}</span>
                <span class="pill">Relations: {{ relationCount }}</span>
                <span class="pill">Edges: {{ edges.length }}</span>
                <span
                    class="pill legend outgoing"
                    v-if="selectedNodeName"
                >
                    Out: {{ selectedOutgoingEdges.length }}
                </span>
                <span
                    class="pill legend incoming"
                    v-if="selectedNodeName"
                >
                    In: {{ selectedIncomingEdges.length }}
                </span>
            </div>
            <div class="actions">
                <div class="node-search">
                    <input
                        class="input node-search-input"
                        type="search"
                        list="schema-node-options"
                        placeholder="Find object type"
                        v-model.trim="nodeSearch"
                        @input="onNodeSearchInput"
                        @keydown.enter.prevent="selectSearchedNode"
                    >
                    <datalist id="schema-node-options">
                        <option
                            v-for="node in nodeSearchMatches.slice(0, 12)"
                            :key="`search-${node.name}`"
                            :value="node.name"
                        />
                    </datalist>
                    <button
                        class="button button-outlined node-search-button"
                        type="button"
                        :disabled="!nodeSearchMatches.length"
                        @click="selectSearchedNode"
                    >
                        Go
                    </button>
                    <button
                        class="button button-outlined node-search-button"
                        type="button"
                        @click="clearNodeSearch"
                        v-if="nodeSearch"
                    >
                        Clear
                    </button>
                </div>
                <label class="checkbox-row">
                    <input
                        type="checkbox"
                        v-model="onlyEnabled"
                    >
                    <span>Only enabled object types</span>
                </label>
                <a
                    class="button button-outlined export-button"
                    @click.prevent="exportProjectModelJson"
                >
                    <app-icon
                        class="export-icon"
                        icon="carbon:export"
                    />
                    <span class="ml-05">Export Json</span>
                </a>
                <a
                    class="button button-outlined export-button"
                    @click.prevent="exportProjectModelZip"
                >
                    <app-icon
                        class="export-icon"
                        icon="carbon:export"
                    />
                    <span class="ml-05">Export Zip</span>
                </a>
                <button
                    class="button button-outlined"
                    @click="fetchSchema"
                >
                    <app-icon
                        class="refresh-icon"
                        icon="carbon:reset"
                    />
                    <span class="ml-05">Reload Schema</span>
                </button>
            </div>
        </div>

        <div
            class="schema-notice"
            v-if="loading"
        >
            Loading schema...
        </div>
        <div
            class="schema-notice schema-error"
            v-else-if="error"
        >
            {{ error }}
        </div>

        <div
            class="schema-content"
            v-else
        >
            <div
                ref="schemaCanvas"
                class="schema-canvas"
            >
                <svg
                    :viewBox="`0 0 ${width} ${height}`"
                    role="img"
                    aria-label="Model schema graph"
                    @click="onCanvasClick"
                >
                    <g class="edges">
                        <g
                            v-for="edge in edges"
                            :key="edge.id"
                        >
                            <path
                                class="edge-loop"
                                :d="loopPath(edge)"
                                :class="edgeClass(edge)"
                                v-if="isSelfLoop(edge)"
                            />
                            <line
                                :x1="edge.from.x"
                                :y1="edge.from.y"
                                :x2="edge.to.x"
                                :y2="edge.to.y"
                                :class="edgeClass(edge)"
                                v-else
                            />
                            <path
                                class="edge-hit-area edge-hit-area-loop"
                                :d="loopPath(edge)"
                                @mouseenter="onEdgeEnter(edge, $event)"
                                @mousemove="onEdgeMove($event)"
                                @mouseleave="onEdgeLeave"
                                @click.stop="onEdgeClick(edge, $event)"
                                v-if="isSelfLoop(edge)"
                            />
                            <line
                                class="edge-hit-area"
                                :x1="edge.from.x"
                                :y1="edge.from.y"
                                :x2="edge.to.x"
                                :y2="edge.to.y"
                                @mouseenter="onEdgeEnter(edge, $event)"
                                @mousemove="onEdgeMove($event)"
                                @mouseleave="onEdgeLeave"
                                @click.stop="onEdgeClick(edge, $event)"
                                v-else
                            />
                        </g>
                    </g>
                    <g class="nodes">
                        <g
                            v-for="node in nodes"
                            :key="node.name"
                            :transform="`translate(${node.x}, ${node.y})`"
                            class="node"
                            :class="{
                                selected: selectedNodeName === node.name,
                                connected: connectedNodeNames.includes(node.name),
                                muted: isNodeMuted(node.name),
                                disabled: !node.enabled,
                            }"
                            @click.stop="selectNode(node.name)"
                        >
                            <circle :r="nodeRadius" />
                            <text
                                x="0"
                                :y="nodeRadius + 16"
                            >
                                {{ node.name }}
                            </text>
                        </g>
                    </g>
                </svg>

                <div
                    ref="edgeTooltip"
                    class="edge-tooltip"
                    :style="tooltipStyle"
                    v-if="tooltipEdge && tooltip.visible"
                >
                    <div class="edge-tooltip-line">
                        <strong>{{ relationText(tooltipEdge) }}</strong>
                        <span>{{ tooltipEdge.from.name }} -> {{ tooltipEdge.to.name }}</span>
                    </div>
                    <div class="edge-tooltip-line inverse">
                        <strong>{{ inverseRelationText(tooltipEdge) }}</strong>
                        <span>{{ tooltipEdge.to.name }} -> {{ tooltipEdge.from.name }}</span>
                    </div>
                    <div
                        class="edge-tooltip-hint"
                        v-if="pinnedEdgeId"
                    >
                        Pinned. Click empty canvas area to close.
                    </div>
                </div>
            </div>

            <div class="schema-side-panels">
                <aside
                    class="schema-info"
                    v-if="selectedNode"
                >
                    <h3 :class="`tag has-background-module-${selectedNode.name}`">
                        {{ selectedNode.name }}
                    </h3>
                    <div class="model-meta-grid">
                        <div class="meta-item">
                            <span class="meta-label">Enabled</span>
                            <span
                                class="meta-value"
                                :class="selectedNode.enabled ? 'is-yes' : 'is-no'"
                            >
                                {{ selectedNode.enabled ? 'yes' : 'no' }}
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Abstract</span>
                            <span
                                class="meta-value"
                                :class="selectedNode.is_abstract ? 'is-yes' : 'is-no'"
                            >
                                {{ selectedNode.is_abstract ? 'yes' : 'no' }}
                            </span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Parent</span>
                            <span class="meta-value text">{{ selectedNode.parent_name || '-' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Table</span>
                            <span class="meta-value text">{{ selectedNode.table || '-' }}</span>
                        </div>
                        <div class="meta-item wide">
                            <span class="meta-label">Associations</span>
                            <span class="meta-value text">{{ (selectedNode.associations || []).join(', ') || '-' }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Connections</span>
                            <span class="meta-value count">{{ selectedNodeEdges.length }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Outgoing</span>
                            <span class="meta-value count outgoing">{{ selectedOutgoingEdges.length }}</span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-label">Incoming</span>
                            <span class="meta-value count incoming">{{ selectedIncomingEdges.length }}</span>
                        </div>
                    </div>

                    <div
                        class="direction-list"
                        :class="section.key"
                        v-for="section in relationSections"
                        :key="section.key"
                    >
                        <h4>
                            <span>{{ section.title }}</span>
                            <span class="relation-count">{{ section.groups.length }}</span>
                        </h4>
                        <ul class="relations-list">
                            <li
                                class="relation-row"
                                v-for="group in section.groups"
                                :key="`${section.key}-${group.key}`"
                            >
                                <div class="relation-title">
                                    {{ relationText(group) }} / {{ inverseRelationText(group) }}
                                </div>
                                <div class="relation-targets">
                                    <span class="relation-arrow">{{ section.arrow }}</span>
                                    <span class="relation-types">{{ group.types.join(', ') }}</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </aside>
                <aside
                    class="schema-info model-properties-panel"
                    v-if="selectedNode"
                >
                    <h4>
                        <span>Model properties</span>
                        <span class="relation-count">{{ selectedModelProperties.length }}</span>
                    </h4>

                    <ul
                        class="property-list"
                        v-if="selectedModelProperties.length"
                    >
                        <li
                            class="property-row"
                            v-for="prop in selectedModelProperties"
                            :key="`prop-${prop.name}-${prop.property}`"
                        >
                            <div class="property-header">
                                <span class="property-name">{{ prop.name }}</span>
                                <span class="property-type">{{ prop.property }}</span>
                            </div>
                            <div
                                class="property-description"
                                v-if="prop.description"
                            >
                                {{ prop.description }}
                            </div>
                            <div class="property-meta">
                                <span class="property-chip">nullable: {{ prop.is_nullable ? 'yes' : 'no' }}</span>
                                <span class="property-chip">readonly: {{ prop.read_only ? 'yes' : 'no' }}</span>
                                <span
                                    class="property-chip"
                                    v-if="prop.default_value !== null && prop.default_value !== undefined"
                                >
                                    default: {{ prop.default_value }}
                                </span>
                            </div>
                        </li>
                    </ul>
                    <p
                        class="property-empty"
                        v-else
                    >
                        No custom model properties found for this object type.
                    </p>
                </aside>
                <aside
                    class="schema-info"
                    v-else
                >
                    <h3>Select a node</h3>
                    <p>Click an object type in the graph to inspect details.</p>
                </aside>
            </div>
        </div>
    </section>
</template>

<script>
import JSZip from 'jszip';

export default {
    name: 'ModelSchema',

    data() {
        return {
            loading: true,
            error: null,
            schema: null,
            onlyEnabled: true,
            selectedNodeName: null,
            hoveredEdgeId: null,
            pinnedEdgeId: null,
            tooltip: {
                visible: false,
                x: 0,
                y: 0,
            },
            width: 1200,
            height: 760,
            nodeRadius: 14,
            nodeSearch: '',
        };
    },

    computed: {
        objectTypes() {
            const values = this.schema?.object_types || [];
            return [...values].sort((a, b) => a.name.localeCompare(b.name));
        },

        relationCount() {
            return (this.schema?.relations || []).length;
        },

        nodeSearchMatches() {
            const query = this.normalizeNodeSearch(this.nodeSearch);
            if (!query) {
                return this.nodes;
            }

            return this.nodes.filter((node) => node.name.toLowerCase().includes(query));
        },

        nodes() {
            const source = this.onlyEnabled
                ? this.objectTypes.filter((item) => item.enabled !== false)
                : this.objectTypes;

            const count = source.length;
            if (!count) {
                return [];
            }

            const centerX = this.width / 2;
            const centerY = this.height / 2;
            const radius = Math.max(180, Math.min(this.width, this.height) * 0.43);

            return source.map((item, index) => {
                const angle = (2 * Math.PI * index) / count - Math.PI / 2;
                return {
                    ...item,
                    x: centerX + (count > 1 ? Math.cos(angle) * radius : 0),
                    y: centerY + (count > 1 ? Math.sin(angle) * radius : 0),
                };
            });
        },

        nodesByName() {
            return this.nodes.reduce((acc, node) => {
                acc[node.name] = node;
                return acc;
            }, {});
        },

        edges() {
            const relations = this.schema?.relations || [];
            const visible = this.nodesByName;
            const edges = [];
            const visited = new Set();

            relations.forEach((relation) => {
                const left = relation.left || [];
                const right = relation.right || [];

                left.forEach((leftType) => {
                    right.forEach((rightType) => {
                        const from = visible[leftType];
                        const to = visible[rightType];
                        if (!from || !to) {
                            return;
                        }

                        const id = `${relation.name}:${leftType}->${rightType}`;
                        if (visited.has(id)) {
                            return;
                        }

                        visited.add(id);
                        edges.push({
                            id,
                            relation: relation.name,
                            relationLabel: relation.label || null,
                            inverseRelation: relation.inverse_name || '-',
                            inverseRelationLabel: relation.inverse_label || null,
                            from,
                            to,
                        });
                    });
                });
            });

            return edges;
        },

        selectedNode() {
            if (!this.selectedNodeName) {
                return null;
            }

            return this.nodes.find((node) => node.name === this.selectedNodeName) || null;
        },

        selectedNodeEdges() {
            if (!this.selectedNodeName) {
                return [];
            }

            return this.edges.filter((edge) => {
                return edge.from.name === this.selectedNodeName || edge.to.name === this.selectedNodeName;
            });
        },

        connectedNodeNames() {
            if (!this.selectedNodeName) {
                return [];
            }

            return Array.from(new Set([
                this.selectedNodeName,
                ...this.selectedNodeEdges.map((edge) => edge.from.name),
                ...this.selectedNodeEdges.map((edge) => edge.to.name),
            ]));
        },

        selectedOutgoingEdges() {
            if (!this.selectedNodeName) {
                return [];
            }

            return this.selectedNodeEdges.filter((edge) => edge.from.name === this.selectedNodeName && edge.to.name !== this.selectedNodeName);
        },

        selectedIncomingEdges() {
            if (!this.selectedNodeName) {
                return [];
            }

            return this.selectedNodeEdges.filter((edge) => edge.to.name === this.selectedNodeName && edge.from.name !== this.selectedNodeName);
        },

        selectedLoopEdges() {
            if (!this.selectedNodeName) {
                return [];
            }

            return this.selectedNodeEdges.filter((edge) => edge.from.name === this.selectedNodeName && edge.to.name === this.selectedNodeName);
        },

        selectedModelProperties() {
            if (!this.selectedNodeName) {
                return [];
            }

            const allProperties = this.schema?.properties || [];
            return allProperties
                .filter((prop) => prop.object === this.selectedNodeName)
                .sort((a, b) => {
                    const byName = (a.name || '').localeCompare(b.name || '');
                    if (byName !== 0) {
                        return byName;
                    }

                    return (a.property || '').localeCompare(b.property || '');
                });
        },

        groupedOutgoingRelations() {
            return this.groupRelationsByType(this.selectedOutgoingEdges, 'to');
        },

        groupedIncomingRelations() {
            return this.groupRelationsByType(this.selectedIncomingEdges, 'from');
        },

        groupedLoopRelations() {
            return this.groupRelationsByType(this.selectedLoopEdges, 'to');
        },

        relationSections() {
            return [
                {
                    key: 'outgoing',
                    title: 'Outgoing relations',
                    arrow: '->',
                    groups: this.groupedOutgoingRelations,
                },
                {
                    key: 'incoming',
                    title: 'Incoming relations',
                    arrow: '<-',
                    groups: this.groupedIncomingRelations,
                },
                {
                    key: 'loop',
                    title: 'Self relations',
                    arrow: 'self',
                    groups: this.groupedLoopRelations,
                },
            ].filter((section) => section.groups.length);
        },

        hoveredEdge() {
            if (!this.hoveredEdgeId) {
                return null;
            }

            return this.edges.find((edge) => edge.id === this.hoveredEdgeId) || null;
        },

        pinnedEdge() {
            if (!this.pinnedEdgeId) {
                return null;
            }

            return this.edges.find((edge) => edge.id === this.pinnedEdgeId) || null;
        },

        tooltipEdge() {
            return this.pinnedEdge || this.hoveredEdge;
        },

        tooltipStyle() {
            return {
                left: `${this.tooltip.x}px`,
                top: `${this.tooltip.y}px`,
            };
        },
    },

    watch: {
        nodes(nodes) {
            if (!nodes.length) {
                this.selectedNodeName = null;
                return;
            }

            if (!this.selectedNodeName || !nodes.some((node) => node.name === this.selectedNodeName)) {
                const match = this.findNodeBySearch(this.nodeSearch, nodes);
                this.selectedNodeName = match?.name || nodes[0].name;
            }
        },

        edges(newEdges) {
            if (this.hoveredEdgeId && !newEdges.some((edge) => edge.id === this.hoveredEdgeId)) {
                this.hoveredEdgeId = null;
            }
            if (this.pinnedEdgeId && !newEdges.some((edge) => edge.id === this.pinnedEdgeId)) {
                this.pinnedEdgeId = null;
            }

            if (!this.hoveredEdgeId && !this.pinnedEdgeId) {
                this.tooltip.visible = false;
            }
        },
    },

    mounted() {
        this.fetchSchema();
    },

    methods: {
        makeRelationGroupKey(item) {
            return [
                item.relation,
                item.relationLabel || '',
                item.inverseRelation,
                item.inverseRelationLabel || '',
            ].join('|');
        },

        normalizeNodeSearch(value) {
            return (value || '').trim().toLowerCase();
        },

        findNodeBySearch(query, source = this.nodes) {
            const normalizedQuery = this.normalizeNodeSearch(query);
            if (!normalizedQuery) {
                return null;
            }

            return source.find((node) => node.name.toLowerCase() === normalizedQuery)
                || source.find((node) => node.name.toLowerCase().startsWith(normalizedQuery))
                || source.find((node) => node.name.toLowerCase().includes(normalizedQuery))
                || null;
        },

        onNodeSearchInput() {
            const match = this.findNodeBySearch(this.nodeSearch);
            if (match) {
                this.selectedNodeName = match.name;
            }
        },

        selectSearchedNode() {
            const match = this.findNodeBySearch(this.nodeSearch);
            if (!match) {
                return;
            }

            this.selectedNodeName = match.name;
            this.nodeSearch = match.name;
        },

        clearNodeSearch() {
            this.nodeSearch = '';
        },

        exportProjectModelJson() {
            const content = JSON.stringify(this.schema, null, 2);
            const url = URL.createObjectURL(new Blob([content], { type: 'application/json' }));
            const a = document.createElement('a');
            a.href = url;
            a.download = 'project-model.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        },

        exportProjectModelZip() {
            if (!this.schema) {
                return;
            }

            const zip = new JSZip();
            const parseMaybeJsonString = (value) => {
                if (typeof value !== 'string') {
                    return value;
                }

                try {
                    return JSON.parse(value);
                } catch {
                    return value;
                }
            };

            Object.keys(this.schema).forEach((key) => {
                const value = this.schema[key];
                const exportValue = key === 'config'
                    ? (() => {
                        if (Array.isArray(value)) {
                            return value.map((item) => {
                                if (!item || typeof item !== 'object') {
                                    return item;
                                }

                                return {
                                    ...item,
                                    content: parseMaybeJsonString(item.content),
                                };
                            });
                        }

                        return value;
                    })()
                    : value;
                const content = JSON.stringify(exportValue, null, 2);
                zip.file(`${key}.json`, content);
            });

            zip.generateAsync({ type: 'blob' })
                .then((content) => {
                    const url = URL.createObjectURL(content);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = 'project-model.zip';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    URL.revokeObjectURL(url);
                })
                .catch(() => {
                    BEDITA.error('Failed to export schema zip file.');
                });
        },
        groupRelationsByType(edges, counterpartSide) {
            const grouped = new Map();

            edges.forEach((edge) => {
                const key = this.makeRelationGroupKey(edge);

                if (!grouped.has(key)) {
                    grouped.set(key, {
                        key,
                        relation: edge.relation,
                        relationLabel: edge.relationLabel || null,
                        inverseRelation: edge.inverseRelation,
                        inverseRelationLabel: edge.inverseRelationLabel || null,
                        types: [],
                    });
                }

                const row = grouped.get(key);
                const typeName = edge[counterpartSide]?.name;
                if (typeName && !row.types.includes(typeName)) {
                    row.types.push(typeName);
                }
            });

            return Array.from(grouped.values())
                .map((row) => ({
                    ...row,
                    types: [...row.types].sort((a, b) => a.localeCompare(b)),
                }))
                .sort((a, b) => {
                    const byRel = a.relation.localeCompare(b.relation);
                    if (byRel !== 0) {
                        return byRel;
                    }

                    return (a.types[0] || '').localeCompare(b.types[0] || '');
                });
        },

        relationText(edge) {
            if (!edge?.relationLabel) {
                return edge?.relation || '-';
            }

            return `${edge.relation} (${edge.relationLabel})`;
        },

        inverseRelationText(edge) {
            if (!edge?.inverseRelationLabel) {
                return edge?.inverseRelation || '-';
            }

            return `${edge.inverseRelation} (${edge.inverseRelationLabel})`;
        },

        isSelfLoop(edge) {
            return edge?.from?.name === edge?.to?.name;
        },

        loopPath(edge) {
            const x = edge.from.x;
            const y = edge.from.y;
            const spreadX = this.nodeRadius * 2.4;
            const spreadY = this.nodeRadius * 2.1;

            return `M ${x} ${y - this.nodeRadius} C ${x + spreadX} ${y - spreadY}, ${x + spreadX} ${y + spreadY}, ${x} ${y + this.nodeRadius}`;
        },

        positionTooltipAtPoint(baseX, baseY) {
            const canvas = this.$refs.schemaCanvas;
            if (!canvas) {
                return;
            }

            const offset = 14;
            const tooltipEl = this.$refs.edgeTooltip;
            const tooltipWidth = tooltipEl?.offsetWidth || 300;
            const tooltipHeight = tooltipEl?.offsetHeight || 74;

            const minX = canvas.scrollLeft + 8;
            const maxX = canvas.scrollLeft + canvas.clientWidth - tooltipWidth - 8;
            const minY = canvas.scrollTop + 8;
            const maxY = canvas.scrollTop + canvas.clientHeight - tooltipHeight - 8;

            let nextX = baseX + offset;
            let nextY = baseY + offset;

            if (nextX > maxX) {
                nextX = baseX - tooltipWidth - offset;
            }
            if (nextY > maxY) {
                nextY = baseY - tooltipHeight - offset;
            }

            this.tooltip.x = Math.max(minX, Math.min(nextX, maxX));
            this.tooltip.y = Math.max(minY, Math.min(nextY, maxY));
        },

        onEdgeEnter(edge, event) {
            if (this.pinnedEdgeId) {
                return;
            }

            this.hoveredEdgeId = edge.id;
            this.updateTooltipPosition(event);
            this.tooltip.visible = true;
        },

        onEdgeMove(event) {
            if (!this.hoveredEdgeId || this.pinnedEdgeId) {
                return;
            }

            this.updateTooltipPosition(event);
        },

        onEdgeLeave() {
            if (this.pinnedEdgeId) {
                return;
            }

            this.hoveredEdgeId = null;
            this.tooltip.visible = false;
        },

        onEdgeClick(edge, event) {
            if (this.pinnedEdgeId === edge.id) {
                this.pinnedEdgeId = null;
                this.tooltip.visible = !!this.hoveredEdgeId;
                if (this.hoveredEdgeId) {
                    this.updateTooltipPosition(event);
                }

                return;
            }

            this.pinnedEdgeId = edge.id;
            this.hoveredEdgeId = edge.id;
            this.updateTooltipPosition(event);
            this.tooltip.visible = true;
        },

        onCanvasClick() {
            if (this.pinnedEdgeId) {
                this.pinnedEdgeId = null;
                this.hoveredEdgeId = null;
                this.tooltip.visible = false;
            }

            // Clicking empty canvas clears current node selection.
            this.selectedNodeName = null;
            this.nodeSearch = '';
        },

        updateTooltipPosition(event) {
            const canvas = this.$refs.schemaCanvas;
            if (!canvas || !event) {
                return;
            }

            const rect = canvas.getBoundingClientRect();
            const baseX = event.clientX - rect.left + canvas.scrollLeft;
            const baseY = event.clientY - rect.top + canvas.scrollTop;
            this.positionTooltipAtPoint(baseX, baseY);
        },

        edgeClass(edge) {
            const isHovered = this.hoveredEdgeId === edge.id;
            const isPinned = this.pinnedEdgeId === edge.id;
            if (!this.selectedNodeName) {
                return {
                    hovered: isHovered,
                    pinned: isPinned,
                };
            }

            const isOutgoing = edge.from.name === this.selectedNodeName && edge.to.name !== this.selectedNodeName;
            const isIncoming = edge.to.name === this.selectedNodeName && edge.from.name !== this.selectedNodeName;
            const isLoop = edge.from.name === this.selectedNodeName && edge.to.name === this.selectedNodeName;

            return {
                selected: isOutgoing || isIncoming || isLoop,
                outgoing: isOutgoing,
                incoming: isIncoming,
                loop: isLoop,
                muted: !isOutgoing && !isIncoming && !isLoop,
                hovered: isHovered,
                pinned: isPinned,
            };
        },

        isNodeMuted(nodeName) {
            return !!this.selectedNodeName && !this.connectedNodeNames.includes(nodeName);
        },

        selectNode(name) {
            const nextSelectedNodeName = this.selectedNodeName === name ? null : name;
            this.selectedNodeName = nextSelectedNodeName;
            this.nodeSearch = nextSelectedNodeName || '';
        },

        fetchSchema() {
            this.loading = true;
            this.error = null;

            const options = {
                method: 'GET',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                },
            };

            const basePath = `${new URL(BEDITA.base, window.location.origin).pathname}`.replace(/\/$/, '');
            const requestUrl = `${basePath}/model/export`;

            fetch(requestUrl, options)
                .then((res) => {
                    if (!res.ok) {
                        throw new Error(`HTTP ${res.status}`);
                    }
                    return res.json();
                })
                .then((json) => {
                    this.schema = json || {};
                })
                .catch(() => {
                    this.error = 'Unable to load model schema JSON.';
                    this.schema = null;
                })
                .finally(() => {
                    this.loading = false;
                });
        },
    },
};
</script>

<style scoped lang="scss">
.model-schema {
    min-height: 520px;
}

.schema-toolbar {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    flex-wrap: wrap;
}

.stats {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.pill {
    display: inline-flex;
    align-items: center;
    border: 1px solid #d2d2d2;
    border-radius: 9999px;
    padding: 0.15rem 0.6rem;
    font-size: 0.85rem;
}

.pill.legend {
    font-weight: 700;
}

.pill.legend.outgoing {
    border-color: #0d5cab;
    color: #0d5cab;
}

.pill.legend.incoming {
    border-color: #af5a00;
    color: #af5a00;
}

.actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.node-search {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}

.node-search-input {
    min-width: 14rem;
}

.node-search-button {
    white-space: nowrap;
}

.export-button {
    display: inline-flex;
    align-items: center;
}

.export-icon, .refresh-icon {
    width: 1rem;
    height: 1rem;
    font-size: 1rem;
    line-height: 1;
    flex: 0 0 1rem;
}

.checkbox-row {
    display: inline-flex;
    gap: 0.4rem;
    align-items: center;
}

.schema-notice {
    border: 1px solid #dedede;
    background: #f8f8f8;
    border-radius: 6px;
    padding: 0.75rem;
}

.schema-error {
    border-color: #cc3333;
    color: #7b1515;
    background: #fff5f5;
}

.schema-content {
    display: grid;
    grid-template-columns: minmax(0, 3fr) minmax(280px, 2fr);
    gap: 1rem;
}

.schema-side-panels {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
    gap: 0.8rem;
    align-content: start;
}

.schema-canvas {
    position: relative;
    border: 1px solid #dedede;
    border-radius: 6px;
    background: #fff;
    overflow: auto;
}

svg {
    width: 100%;
    aspect-ratio: 1200 / 760;
}

.edges line,
.edges path.edge-loop {
    stroke: #b9c0c9;
    stroke-width: 1;
    opacity: 0.65;
    transition: opacity 0.18s ease, stroke-width 0.18s ease, stroke 0.18s ease;
    fill: none;
}

.edges .edge-hit-area {
    stroke: transparent;
    stroke-width: 12;
    opacity: 0;
    pointer-events: stroke;
}

.edges .edge-hit-area-loop {
    fill: none;
}

.edges line.selected,
.edges path.edge-loop.selected {
    stroke-width: 2;
    opacity: 1;
}

.edges line.muted,
.edges path.edge-loop.muted {
    opacity: 0.08;
}

.edges line.hovered,
.edges path.edge-loop.hovered {
    stroke-width: 3;
    opacity: 1;
}

.edges line.pinned,
.edges path.edge-loop.pinned {
    stroke-width: 4;
    opacity: 1;
}

.edges line.outgoing,
.edges path.edge-loop.outgoing {
    stroke: #0d5cab;
}

.edges line.incoming,
.edges path.edge-loop.incoming {
    stroke: #af5a00;
}

.edges line.loop,
.edges path.edge-loop.loop {
    stroke: #7f2aa8;
}


.edge-tooltip {
    position: absolute;
    z-index: 40;
    max-width: 360px;
    border: 1px solid #d7dde7;
    background: #fff;
    border-radius: 8px;
    padding: 0.55rem 0.65rem;
    box-shadow: 0 12px 20px rgba(20, 30, 50, 0.2);
    pointer-events: none;
}

.edge-tooltip-line {
    display: flex;
    gap: 0.45rem;
    align-items: baseline;
    color: #0d5cab;
    font-size: 0.82rem;
    line-height: 1.25;
}

.edge-tooltip-line.inverse {
    margin-top: 0.25rem;
    color: #af5a00;
}

.edge-tooltip-hint {
    margin-top: 0.4rem;
    padding-top: 0.3rem;
    border-top: 1px solid #e8edf4;
    font-size: 0.74rem;
    color: #556270;
}

.node {
    cursor: pointer;
    transition: opacity 0.18s ease;
}

.node circle {
    fill: #ffffff;
    stroke: #223;
    stroke-width: 1.5;
}

.node text {
    font-size: 11px;
    fill: #222;
    text-anchor: middle;
    paint-order: stroke;
    stroke: #fff;
    stroke-width: 3px;
    stroke-linecap: round;
    stroke-linejoin: round;
}

.node.disabled circle {
    stroke: #777;
    fill: #efefef;
}

.node.selected circle {
    stroke: #0d5cab;
    stroke-width: 3;
    fill: #f2f8ff;
}

.node.connected:not(.selected) circle {
    stroke-width: 2;
    fill: #fafcff;
}

.node.muted {
    opacity: 0.22;
}

.schema-info {
    border: 1px solid #dedede;
    border-radius: 6px;
    padding: 0.8rem;
    background: #fff;
    color: #1f2a37;
}

.schema-info h3 {
    margin-top: 0;
    color: #111827;
    font-weight: 500;
}

.schema-info h4 {
    color: #1f2937;
}

.schema-info p {
    color: #334155;
}

.model-meta-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.4rem;
    margin-top: 0.35rem;
}

.meta-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.45rem;
    border: 1px solid #e6edf5;
    border-radius: 6px;
    background: #f8fbff;
    padding: 0.35rem 0.45rem;
}

.meta-item.wide {
    grid-column: 1 / -1;
}

.meta-label {
    font-size: 0.75rem;
    color: #475569;
}

.meta-value {
    font-size: 0.76rem;
    font-weight: 600;
    color: #1f2937;
    border-radius: 999px;
    padding: 0.02rem 0.42rem;
    border: 1px solid #d7e1ed;
    background: #fff;
    line-height: 1.2;
}

.meta-value.text {
    border: 0;
    background: transparent;
    border-radius: 0;
    padding: 0;
    font-weight: 500;
    text-align: right;
    word-break: break-word;
}

.meta-value.count {
    min-width: 1.45rem;
    text-align: center;
}

.meta-value.is-yes {
    color: #0f5132;
    border-color: #b9e6cc;
    background: #ecfff4;
}

.meta-value.is-no {
    color: #7a1f1f;
    border-color: #f2cbcb;
    background: #fff3f3;
}

.meta-value.outgoing {
    color: #0d5cab;
    border-color: #c9def4;
    background: #f2f8ff;
}

.meta-value.incoming {
    color: #af5a00;
    border-color: #f0d8bf;
    background: #fff8f0;
}

.direction-list,
.model-properties-panel {
    margin-top: 1rem;
    border: 1px solid #e5eaf0;
    border-radius: 8px;
    padding: 0.6rem;
    background: #f9fbfd;
}

.direction-list h4,
.model-properties-panel h4 {
    margin: 0 0 0.5rem;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.5rem;
}

.direction-list.outgoing {
    border-left: 4px solid #0d5cab;
}

.direction-list.incoming {
    border-left: 4px solid #af5a00;
}

.direction-list.loop {
    border-left: 4px solid #7f2aa8;
}

.relation-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 1.4rem;
    height: 1.4rem;
    padding: 0 0.35rem;
    border-radius: 999px;
    border: 1px solid #d2dbe7;
    background: #fff;
    color: #334155;
    font-size: 0.76rem;
}

.relations-list,
.property-list {
    margin: 0;
    padding: 0;
    list-style: none;
}

.relation-row,
.property-row {
    margin: 0;
    padding: 0.45rem 0.5rem;
    border: 1px solid #e6edf5;
    border-radius: 6px;
    background: #fff;
}

.relation-row + .relation-row,
.property-row + .property-row {
    margin-top: 0.35rem;
}

.relation-title {
    color: #27364a;
    font-size: 0.86rem;
    line-height: 1.25;
    margin-bottom: 0.25rem;
    font-weight: 500;
    letter-spacing: 0.01em;
    white-space: normal;
    word-break: break-word;
}

.relation-targets {
    display: flex;
    gap: 0.35rem;
    align-items: flex-start;
    white-space: normal;
}

.relation-arrow {
    color: #64748b;
    font-weight: 700;
    line-height: 1.2;
}

.relation-types {
    color: #1f2937;
    font-size: 0.84rem;
    line-height: 1.25;
    word-break: break-word;
    white-space: normal;
}

.model-properties-panel {
    margin-top: 0;
    border-left: 4px solid #4b5563;
}

.property-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    gap: 0.5rem;
}

.property-name {
    font-size: 0.86rem;
    font-weight: 500;
    color: #1f2937;
}

.property-type {
    font-size: 0.75rem;
    color: #475569;
    border: 1px solid #d2dbe7;
    border-radius: 999px;
    padding: 0.04rem 0.35rem;
    background: #f8fafc;
}

.property-description {
    margin-top: 0.28rem;
    color: #334155;
    font-size: 0.82rem;
    line-height: 1.25;
}

.property-meta {
    margin-top: 0.32rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.28rem;
}

.property-chip {
    display: inline-flex;
    border: 1px solid #dbe4ef;
    border-radius: 999px;
    padding: 0.02rem 0.35rem;
    font-size: 0.72rem;
    color: #526173;
    background: #f9fbff;
}

.property-empty {
    margin: 0;
    font-size: 0.82rem;
    color: #64748b;
}

@media (max-width: 1024px) {
    .schema-content {
        grid-template-columns: 1fr;
    }

    .schema-side-panels {
        grid-template-columns: 1fr;
    }
}
</style>
