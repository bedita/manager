# Vue 2 to Vue 3 Upgrade Roadmap

## Current State Analysis
- **Last verified**: 2026-08-28
- **Vue version**: 3.5.x with `@vue/compat` enabled (MODE: 2)
- **Build tool**: Webpack 5 (good, already modern)
- **Vue components**: ~95 .vue files
- **Key dependencies**: FullCalendar, Chart.js, vue-chartjs, Treeselect, Autocomplete
- **Build status**: `yarn webpack --mode production` passes (last run: 2026-08-28; existing three webpack warnings remain)
- **Migration blockers still present**: root `new Vue()`, `Vue.use`, `Vue.filter`, `Vue.prototype`, `this.$on/$off/$once`, `inline-template`, `.sync`, legacy scoped slots, and Vue 2-only dependencies
- **Incompatible libraries still in use**: `@riophae/vue-treeselect`, `@trevoreyre/autocomplete-vue`, `vuejs-title`, `@iconify/vue2`
- **Compiler dependency cleanup pending**: `vue-template-compiler` is still in `devDependencies`; remove it only after legacy Vue 2 dependencies no longer require it.
- **Current live diagnosis**: Dashboard renders. `ModulesIndex` is temporarily replaced by `DEBUG: modules-index...` in `templates/Pages/Modules/index.twig`; this confirms the root app mounts and isolates the module page failure to its component/template boundary.

---

## Phase 1: Preparation & Assessment (2-3 weeks)

### 1.1 Upgrade to Latest Vue 2
- [x] Update `vue` and `vue-template-compiler` to `^2.7.16` (latest Vue 2)
- [x] Test thoroughly - Vue 2.7 includes Vue 3 compatibility features
- [x] This gives you Composition API support to start migrating patterns early

### 1.2 Audit Dependencies
Check Vue 3 compatibility for:
- [x] `@fullcalendar/vue` → Already using v6 (Vue 3 compatible ✅)
- [x] `@riophae/vue-treeselect` → Not Vue 3 compatible ❌ (find alternative: `vue3-treeselect`)
- [x] `@trevoreyre/autocomplete-vue` → Not Vue 3 compatible ❌ (needs replacement)
- [x] `vue-chartjs` → Already on v5 (Vue 3 compatible ✅)
- [x] `vuejs-title` → Not Vue 3 compatible ❌ (use `@vueuse/head` or `vue-meta` v3)
- [ ] `@iconify/vue2` → Replace with `@iconify/vue` v4+ (ONLY after Vue 3 upgrade)
- [x] `eslint-plugin-vue` → Update to v9+ for Vue 3 rules ✅

### 1.3 Code Audit
- [x] Identify all uses of deprecated patterns:
  - Global event bus (`resources/js/app/components/event-bus.vue`)
  - Vue filters (found in `resources/js/libs/filters.js`)
  - `$on`, `$off`, `$once` on component instances
  - `Vue.extend()` usage in directives
  - Global mixins
  - `Vue.prototype` additions

---

## Phase 2: Create Migration Branch & Setup (1 week)

### 2.1 Branch Strategy & Migration Approach
- [x] Create `vue3-migration` branch
- [x] Plan to use `@vue/compat` (Vue 3 Migration Build) for gradual migration
- [x] Configure initial compat mode (`MODE: 2`) and verify app bootstrap
- [ ] Document rollback plan (can always revert to `main` branch with Vue 2.7)
- [ ] Set up local development environment for testing Vue 3 branch

### 2.2 Build Tool Updates
- [x] Update `vue-loader` from v15 to v17
- [x] Add `@vue/compiler-sfc` and `@vue/compat`
- [ ] Remove `vue-template-compiler` after replacing remaining Vue 2-only dependencies
- [x] Update webpack config for Vue 3 loader and compat alias
- [x] Configure compat mode settings (start with `MODE: 2`)
- [x] Test build process (production webpack build passes)
- [ ] Monitor browser console for compat warnings during development

---

## Phase 3: Breaking Changes - Code Patterns (3-4 weeks)

### 3.1 Replace Event Bus
**Priority: HIGH** - Global `event-bus.vue` is already removed, but Vue-instance event buses are still used

- [x] Choose solution pattern by scope (props/emits + provide/inject + local scoped bus where needed)
- [x] **COMPLETED**: Simple parent-child EventBus patterns migrated to props/emits
- [x] **COMPLETED**: Sibling component EventBus patterns migrated to provide/inject
- [x] Migrated `object-property-add.vue` + `object-properties.vue` - parent/child pattern
- [x] Migrated `map-view.js` + `coordinates-view.vue` - sibling pattern via provide/inject
- [x] Both components have backward compatibility fallbacks to EventBus
- [x] Remove legacy `event-bus.vue` component
- [ ] Replace remaining `new Vue()` buses and root-instance listeners:
  - [x] `resources/js/app/components/panel-view.js` (`PanelEvents`): replaced the Vue-instance bus with a module-local emitter while preserving the existing `listen`, `stop`, and send helper API
  - `resources/js/app/components/placeholder-bus.vue` (`PlaceholderBus`)
  - `resources/js/app/components/permission-toggle/permission-toggle.vue` (`PermissionEvents`)
  - root `this.$on(...)` listeners in `resources/js/app/app.js`

**Example migration to mitt:**
```javascript
// Old (Vue 2)
import EventBus from './event-bus.vue';
EventBus.$emit('event-name', data);
EventBus.$on('event-name', callback);

// New (Vue 3)
import mitt from 'mitt';
const emitter = mitt();
emitter.emit('event-name', data);
emitter.on('event-name', callback);
```

**Example migration for sibling components (provide/inject):**
```javascript
// Parent component provides coordination
provide() {
  return {
    onCoordinatesUpdate: (coords) => {
      this.listeners.forEach(fn => fn(coords));
    },
    registerListener: (callback) => {
      this.listeners.push(callback);
    }
  };
}

// Emitter component injects callback
inject: ['onCoordinatesUpdate'],
methods: {
  emitChange() {
    if (this.onCoordinatesUpdate) {
      this.onCoordinatesUpdate(data);
    }
  }
}

// Listener component injects registration
inject: ['registerListener'],
mounted() {
  if (this.registerListener) {
    this.registerListener((data) => {
      this.handleUpdate(data);
    });
  }
}
```

### 3.2 Remove/Replace Filters
Found in `resources/js/libs/filters.js`:
- [x] Convert `humanize` filter to method/composable in Vue components
- [x] Convert `capitalize` filter to method/composable (not used in Vue templates)
- [x] Update all Vue template usages (`{{ value | filter }}` → `{{ filter(value) }}`)
- [x] Confirm Vue template filter-pipe syntax removed; Twig filters are separate and unaffected

**Completed:** Updated 2 Vue components:
- `resources/js/app/components/json-fields/string-list.vue`
- `resources/js/app/components/json-fields/key-value-list.vue`

**Example migration:**
```vue
<!-- Old (Vue 2) -->
<template>
  <div>{{ username | capitalize }}</div>
</template>

<!-- New (Vue 3) -->
<template>
  <div>{{ capitalize(username) }}</div>
</template>

<script>
export default {
  methods: {
    capitalize(str) {
      return str.charAt(0).toUpperCase() + str.slice(1);
    }
  }
}
</script>
```

### 3.3 Update Global APIs
- [ ] Replace `Vue.prototype.$helpers` with app.config.globalProperties or composables
- [ ] Convert global mixin to composables where possible
- [ ] Review all `Vue.directive()` calls - update lifecycle hooks:
  - `bind` → `beforeMount`
  - `inserted` → `mounted`
  - `update` → (removed, use `updated`)
  - `componentUpdated` → `updated`
  - `unbind` → `unmounted`

### 3.4 Custom Directives Migration
Update these directives with new lifecycle hooks:
- [x] `v-uri` (`resources/js/app/directives/uri.js`)
- [x] `v-email` (`resources/js/app/directives/email.js`)
- [x] `v-datepicker` (`resources/js/app/directives/datepicker.js`)
- [x] `v-jsoneditor` (`resources/js/app/directives/jsoneditor.js`)
- [x] `v-richeditor` (`resources/js/app/directives/richeditor.js`)

**Completed in this step:** replaced deprecated hooks (`bind/inserted/componentUpdated/unbind`) with Vue 3 equivalents and removed `Vue.extend()` usage from directive helper mounts.

**Example directive migration:**
```javascript
// Old (Vue 2)
Vue.directive('example', {
  bind(el, binding) { /* ... */ },
  inserted(el) { /* ... */ },
  update(el) { /* ... */ },
  componentUpdated(el) { /* ... */ },
  unbind(el) { /* ... */ }
});

// New (Vue 3)
app.directive('example', {
  beforeMount(el, binding) { /* was bind */ },
  mounted(el) { /* was inserted */ },
  updated(el) { /* was update + componentUpdated */ },
  unmounted(el) { /* was unbind */ }
});
```

---

## Phase 4: Component Updates (4-6 weeks)

### 4.1 Update Component Props & Events
- [ ] Add `emits` option to all components using `$emit`
- [ ] Review v-model usage (syntax changed)
- [ ] Update `.sync` modifier to `v-model:propName`

**Example:**
```vue
<!-- Old (Vue 2) -->
<child-component :value.sync="title" />

<!-- New (Vue 3) -->
<child-component v-model:value="title" />
```

### 4.2 Render Function Updates
If you have render functions (check components):
- [ ] Update h() function signature (2nd param no longer `data`)
- [ ] Update functional components syntax

### 4.3 Key Component Files Priority
Start with these based on your structure:
1. [ ] `resources/js/app/app.js` (root instance, plugin registration, global components, `$on` listeners)
2. [ ] `resources/js/config/config.js` (`Vue.config`, `Vue.options`, global mixin)
3. [ ] Directives in `resources/js/app/directives/*.js` (`Vue.directive`, `Vue.extend`)
4. [ ] Event channels in `panel-view.js`, `placeholder-bus.vue`, `permission-toggle.vue`
5. [ ] Components using component-instance events (`$on/$off/$once`) and `$children`
6. [ ] Remaining form/list/tree components for `emits` hardening and compat cleanup

### 4.4 Component Instance Changes
- [ ] Replace `$on`, `$off`, `$once` on component instances
- [ ] Update `$listeners` usage (no longer available, merged with `$attrs`)
- [ ] Update `$scopedSlots` (merged with `$slots`)

---

## Phase 5: Dependency Replacements (2-3 weeks)

### 5.1 Find & Replace Incompatible Libraries
- [ ] `@riophae/vue-treeselect` → Evaluate: `vue3-treeselect` or `@vueform/multiselect`
- [ ] `@trevoreyre/autocomplete-vue` → Custom implementation or `@vueuse/core` composables
- [ ] `vuejs-title` → `@vueuse/head` or integrate with Vue Router meta
- [ ] `@iconify/vue2` → `@iconify/vue` v4+ (simple find/replace in imports)

### 5.2 Update Each Replacement
- [ ] Install new dependency
- [ ] Update import statements
- [ ] Update component usage
- [ ] Test functionality
- [ ] Remove old dependency

---

## Phase 6: Vue 3 Core Migration (1-2 weeks)

### 6.1 Update package.json

**Install Vue 3 with Compat Build:**
```json
{
  "vue": "^3.4.0",
  "@vue/compat": "^3.4.0",
  "@vue/compiler-sfc": "^3.4.0",
  "vue-loader": "^17.0.0"
}
```

**Remove:**
```json
{
  "vue-template-compiler": "^2.5.16"
}
```

**Configure webpack to use compat build:**
```javascript
// webpack.config.js
module.exports = {
  resolve: {
    alias: {
      vue: '@vue/compat'
    }
  }
}
```

### 6.2 Update App Initialization
**Old (Vue 2):**
```javascript
import Vue from 'vue';
import App from './App.vue';

new Vue({
  render: h => h(App)
}).$mount('#app');
```

**New (Vue 3):**
```javascript
import { createApp } from 'vue';
import App from './App.vue';

const app = createApp(App);
app.mount('#app');
```

### 6.3 Update Plugin Registration
**Old (Vue 2):**
```javascript
import Vue from 'vue';
import MyPlugin from './my-plugin';

Vue.use(MyPlugin);
Vue.prototype.$myMethod = () => {};
```

**New (Vue 3):**
```javascript
import { createApp } from 'vue';
import MyPlugin from './my-plugin';

const app = createApp(App);
app.use(MyPlugin);
app.config.globalProperties.$myMethod = () => {};
```

### 6.4 Update Webpack Config
In `webpack.config.js`:
```javascript
// Remove
const { VueLoaderPlugin } = require('vue-loader');

// Update to ensure Vue 3 compatibility
const { VueLoaderPlugin } = require('vue-loader');
```

Update dependencies in build:
- [ ] Remove `vue-template-compiler`
- [ ] Add `@vue/compiler-sfc`
- [ ] Test build

---

## Phase 7: Testing & Validation (3-4 weeks)

### 7.1 Unit Tests
- [ ] Update test setup for Vue 3
- [ ] Update `@vue/test-utils` to v2
- [ ] Fix all broken tests
- [ ] Add tests for migrated components

### 7.2 Integration Testing
- [ ] Test all user workflows
- [ ] Test all forms and data entry
- [ ] Test file uploads/media handling
- [ ] Test admin panels
- [ ] Test import functionality
- [ ] Test permission systems

### 7.3 Browser Testing
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

### 7.4 Performance Testing
- [ ] Bundle size comparison
- [ ] Initial load time
- [ ] Runtime performance
- [ ] Memory usage

---

## Phase 8: Optional Enhancements (2-3 weeks)

### 8.1 Adopt New Vue 3 Features
- [ ] Convert Options API to Composition API (gradually)
- [ ] Use `<script setup>` syntax for new components
- [ ] Implement Suspense for async components
- [ ] Use Teleport for modals/overlays
- [ ] Add TypeScript support (optional but recommended)

**Example Composition API:**
```vue
<!-- Options API (Vue 2/3) -->
<script>
export default {
  data() {
    return { count: 0 };
  },
  methods: {
    increment() {
      this.count++;
    }
  }
}
</script>

<!-- Composition API (Vue 3) -->
<script setup>
import { ref } from 'vue';

const count = ref(0);
const increment = () => count.value++;
</script>
```

### 8.2 State Management
- [ ] Consider Pinia instead of event bus pattern
- [ ] Consolidate global state
- [ ] Migrate from event-based communication to state-based

**Example Pinia store:**
```javascript
import { defineStore } from 'pinia';

export const useMainStore = defineStore('main', {
  state: () => ({
    count: 0
  }),
  actions: {
    increment() {
      this.count++;
    }
  }
});
```

---

## Phase 9: Deployment (1-2 weeks)

### 9.1 Staging Deployment
- [ ] Deploy to staging environment
- [ ] Full regression testing
- [ ] Performance monitoring
- [ ] Gather feedback

### 9.2 Production Deployment
- [ ] Create rollback plan
- [ ] Deploy to production
- [ ] Monitor errors
- [ ] Monitor performance
- [ ] User acceptance

---

## Estimated Timeline
**Total: 4-6 months** (assuming 1-2 developers working part-time on migration)

| Phase | Duration | Notes |
|-------|----------|-------|
| Preparation | 2-3 weeks | Vue 2.7 upgrade, audit |
| Setup | 1 week | Branch, build config |
| Pattern migration | 3-4 weeks | Event bus, filters, directives |
| Component updates | 4-6 weeks | All .vue files |
| Dependencies | 2-3 weeks | Replace incompatible libs |
| Core migration | 1-2 weeks | Vue 3 upgrade |
| Testing | 3-4 weeks | Comprehensive testing |
| Enhancements | 2-3 weeks | Optional, new features |
| Deployment | 1-2 weeks | Staging + production |

---

## Next Steps (Execution Plan)

### Sprint A (stabilize migration baseline)
- [x] Introduce `@vue/compat` + `@vue/compiler-sfc`; move `vue-loader` to v17; keep app running in compat mode
- [x] Add webpack alias `vue: '@vue/compat'`
- [x] Convert custom directives to Vue 3 lifecycle names (`beforeMount`, `mounted`, `updated`, `unmounted`)
- [x] Replace `Vue.extend()` usage in directive helper mounts
- [x] Document and classify compat warnings by severity (blocker / warning / cleanup)

**Exit criteria:** project builds, app boots, no fatal compat errors in core pages.

#### Sprint A warning classification (2026-02-26)

**Build result:** production webpack build passes; no fatal compiler errors.

- **Blocker:** none currently.
- **Warning (functional-risk / standards):** invalid HTML table nesting in `resources/js/app/components/object-property/object-property.vue` (`<tr>` directly under `<table>`). Vue 3 compiler warns this may cause hydration/runtime fragility.
- **Warning (functional-risk / standards):** invalid HTML content in `resources/js/app/components/tree-compact/tree-panel.vue` (`<span>` inside `<option>`).
- **Cleanup (build-config):** `DefinePlugin` conflict for `process.env.NODE_ENV` in webpack config (`'"development"' !== '"development"'`).

**Priority order for cleanup:**
1. Fix invalid table markup in `object-property.vue` (wrap rows in `<tbody>` and keep valid table structure).
2. Replace rich markup inside `<option>` in `tree-panel.vue` with plain text.
3. Normalize `DefinePlugin` env value wiring in webpack config.

### Sprint B (remove major Vue 2 APIs)
- [ ] Refactor bootstrap from `new Vue(...)` toward `createApp(...)` entry strategy
- [ ] Replace `Vue.use`, `Vue.component`, `Vue.directive`, `Vue.mixin`, `Vue.prototype` usages
- [ ] Replace `$on/$off/$once` and `$children` usages in remaining components/pages
- [ ] Start dependency replacements: treeselect + autocomplete + title plugin + iconify package

**Exit criteria:** no critical runtime reliance on removed Vue 2 APIs; core workflows manually validated.

### Inline Template Migration Inventory

`inline-template` is removed in Vue 3. Migrate each owner by moving its Twig-provided markup into the corresponding Vue component, then replace the Twig usage with props, events, and slots. Validate each component in `ComponentsPlayground` where its dependencies can be supplied without page-specific data.

**Priority 1 - currently blocking module index**
- [ ] `ModulesIndex`: `templates/Pages/Modules/index.twig`, `templates/Pages/Translations/index.twig`, `templates/Element/Form/multiupload.twig`
- [ ] `FilterBoxView`: `templates/Element/Modules/index_header.twig`, `templates/Element/Modules/list.twig`, `templates/Element/Panel/relations_add.twig`, plus its embedded uses in page/form templates

**Priority 2 - shared page shells and panels**
- [ ] `PanelView` scoped slot: `templates/Element/Panel/panel.twig` (`slot-scope` to `v-slot`)
- [ ] `MainMenu`: `templates/Element/Menu/menu.twig`
- [ ] `Dashboard`: `templates/Pages/Dashboard/index.twig`
- [ ] `AdminIndex`: `templates/Element/Admin/index_content.twig`, `templates/Pages/Admin/RolesModules/index.twig`
- [ ] `ModulesView`: `templates/Element/translation.twig`, `templates/Pages/Model/ObjectTypes/view.twig`, `templates/Pages/Model/Relations/view.twig`, `templates/Pages/Modules/view.twig`, `templates/Pages/UserProfile/view.twig`
- [ ] `TrashIndex` and `TrashView`: `templates/Pages/Trash/index.twig`, `templates/Pages/Trash/view.twig`
- [ ] `ModelIndex`: `templates/Pages/Model/PropertyTypes/index.twig`, `templates/Pages/Model/Tags/index.twig`

**Priority 3 - form and relation components**
- [ ] `PropertyView`: `templates/Element/Form/{advanced_properties,annotations,calendar,captions,categories,core_properties,history,map,media,meta,other_properties,permissions,publish_properties,related_translations,relations,resource_relations,roles,tags,trees}.twig`, `templates/Pages/Admin/RolesModules/index.twig`, `templates/Pages/Model/ObjectTypes/view.twig`, `templates/Pages/UserProfile/view.twig`
- [ ] `RelationView`: `templates/Element/Form/relation.twig`, `templates/Element/Form/related_translations.twig`
- [ ] `ResourceRelationView`: `templates/Element/Form/resource_relations.twig`
- [ ] `FormFileUpload`: `templates/Element/Form/form_file_upload.twig`
- [ ] Remaining embedded owners: `templates/Element/Form/upload.twig`, `templates/Element/Form/roles.twig`, `templates/Pages/Translations/index.twig`

**Per-component workflow**
1. Add the component's template to its `.vue` file, retaining Twig-only strings and server data as props or slots.
2. Replace the Twig `inline-template` block with the component invocation.
3. Add a minimal `ComponentsPlayground` fixture when the component can be initialized without a page request.
4. Rebuild, test the original route, and clear that component's Vue 3 warnings before moving on.

---

## Critical Success Factors

1. **Incremental approach** - Don't try to do everything at once
2. **Comprehensive testing** - Test after each phase
3. **Documentation** - Document all breaking changes and patterns
4. **Team training** - Ensure team understands Vue 3 differences
5. **Rollback capability** - Always have a way back

---

## Recommended Tools

- **Migration helper**: `@vue/compat` (Vue 3 compatibility build) - allows gradual migration
- **ESLint**: `eslint-plugin-vue` with Vue 3 rules
- **Codemod**: `vue-codemod` for automated transformations
- **Event bus replacement**: `mitt` or `tiny-emitter`
- **State management**: Pinia (official Vue 3 store)

---

## Key Files to Focus On

Based on codebase analysis:

### High Priority (Breaking Changes)
1. `resources/js/app/app.js` - root Vue bootstrap (`new Vue`, `Vue.use`, `Vue.component`, root `$on`)
2. `resources/js/config/config.js` - `Vue.config`, `Vue.options`, global mixin
3. `resources/js/app/helpers/view.js` + `resources/js/app/helpers/api-translation.js` - `Vue.prototype` modifications
4. `resources/js/app/components/panel-view.js`, `resources/js/app/components/placeholder-bus.vue`, `resources/js/app/components/permission-toggle/permission-toggle.vue` - `new Vue()` event buses
5. All directive files in `resources/js/app/directives/` - `Vue.directive` + `Vue.extend`

### Medium Priority (Component Updates)
1. `resources/js/app/pages/modules/view.vue` - dynamic `new Vue()` usage
2. Tree components (`tree-panel.vue`, `tree-folder.vue`, `tree-node.vue`)
3. `resources/js/app/components/relation-view/relation-view.vue` and other components using `$on/$off/$once`
4. Form components with v-model
5. Components with custom events

### Low Priority (After Migration)
1. Composition API conversion
2. TypeScript introduction
3. Performance optimizations

---

## Breaking Changes Checklist

### Global API
- [ ] `Vue.config` → `app.config`
- [ ] `Vue.use()` → `app.use()`
- [ ] `Vue.mixin()` → `app.mixin()`
- [ ] `Vue.component()` → `app.component()`
- [ ] `Vue.directive()` → `app.directive()`
- [ ] `Vue.prototype` → `app.config.globalProperties`
- [ ] `Vue.extend()` → Use `defineComponent()`

### Component API
- [ ] `$on`, `$off`, `$once` removed
- [ ] `$listeners` merged into `$attrs`
- [ ] `$scopedSlots` merged into `$slots`
- [ ] `$children` removed
- [ ] Filters removed
- [ ] `.sync` modifier removed (use `v-model:propName`)

### Functional Components
- [ ] `functional: true` option removed
- [ ] Must be plain functions now
- [ ] Different signature for `h()`

### Other Changes
- [ ] `v-for` ref arrays are no longer automatically populated
- [ ] Key attribute on `<template v-for>` should be on `<template>` tag
- [ ] Async components must use `defineAsyncComponent()`
- [ ] Transition class names changed (`v-enter` → `v-enter-from`)

---

## Resources

- [Vue 3 Migration Guide](https://v3-migration.vuejs.org/)
- [Vue 3 Documentation](https://vuejs.org/)
- [Vue Compat (Migration Build)](https://v3-migration.vuejs.org/migration-build.html)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Vite](https://vitejs.dev/) - Consider migrating from Webpack to Vite in the future

---

## Notes

- Start migration in January 2026
- Consider using Vue Compat build for gradual migration
- Keep Vue 2.7 as intermediate step for at least 2-4 weeks
- Maintain detailed changelog of all breaking changes
- Set up automated testing before starting migration
- Consider feature freeze during critical migration phases
