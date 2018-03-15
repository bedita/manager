const ModulesIndex = Vue.options.components["modules-index"];

/**
 * Templates that uses this component (directly or indirectly)
 *  Template/Trash/index.twig
 *  - Element/Toolbar/filter.twig
 *  - Element/Toolbar/pagination.twig
 *
 * <trash-index> component used for TrashPage -> Index
 *
 * @extends ModulesIndex
 */
Vue.component('trash-index', {
    extends: ModulesIndex,
});


