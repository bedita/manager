/**
 * Compare two categories by label, using name when label is empty.
 * @param {Object} a First category
 * @param {Object} b Second category
 * @returns {number} Sort order
 */
export function compareCategories(a, b) {
    return (a.label || a.name).localeCompare(b.label || b.name);
}

/**
 * Group categories in a two levels tree, sorted by label.
 * Objects can be assigned only to children and to root categories without children.
 * @param {Array} categories Categories with `id`, `name`, `label` and `parent_id`
 * @returns {Object} Tree with:
 *  - `common`: root categories without children
 *  - `roots`: root categories with children
 *  - `children`: children lists by parent id
 */
export function categoriesTree(categories = []) {
    const sorted = [...categories].sort(compareCategories);
    const children = {};
    for (const category of sorted.filter(category => category.parent_id)) {
        if (!children[category.parent_id]) {
            children[category.parent_id] = [];
        }
        children[category.parent_id].push(category);
    }
    const rootCategories = sorted.filter(category => !category.parent_id);

    return {
        common: rootCategories.filter(category => !children[category.id]),
        roots: rootCategories.filter(category => children[category.id]),
        children,
    };
}
