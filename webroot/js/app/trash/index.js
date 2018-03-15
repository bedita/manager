

/**
 * Templates that uses this component (directly or indirectly)
 *
 * Template/Trash/index.twig
 * - Element/Toolbar/filter.twig
 * - Element/Toolbar/pagination.twig
 *
 */

const ModulesIndex = Vue.options.components["modules-index"];

Vue.component('trash-index', {
    extends: ModulesIndex,
});


