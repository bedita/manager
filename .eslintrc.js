module.exports = {
    root: true,
    env: {
        'node': true,
        'es6': true,
        'browser': true,
    },
    extends: [
        'eslint:recommended',
        'plugin:vue/recommended',
    ],
    globals: {
        'BEDITA': true,
        'tinymce': true,
        'vue': true
    },
    rules: {
        'quotes': [1, 'single'],
        'comma-dangle': 'off',
        'indent': [1, 4, {
            SwitchCase: 1,
        }],
        'no-extra-semi': 'off',
        'no-console': 'off',
        'semi': 'off',

        // override/add rules settings here
        'no-unused-vars': ['warn'],
        'vue/attributes-order': ['warn', {
            'order': [
                'GLOBAL',
                'OTHER_ATTR',
                'DEFINITION',
                'TWO_WAY_BINDING',
                'OTHER_DIRECTIVES',
                'EVENTS',
                'RENDER_MODIFIERS',
                'CONTENT',
                'CONDITIONALS',
            ],
            'alphabetical': false
        }],
        'vue/first-attribute-linebreak': ['warn', {
            'singleline': 'beside',
            'multiline': 'ignore'
        }],
        'vue/html-indent': ['warn', 4],
        'vue/multi-word-component-names': ['warn'],
        'vue/no-mutating-props': ['warn'],
        'vue/no-unused-components': ['warn'],
        'vue/no-unused-vars': ['warn'],
        'vue/no-use-v-if-with-v-for': ['warn'],
        'vue/no-v-html': ['off'],
        'vue/order-in-components': ['warn', {
            'order': [
                ['template', 'render'],
                'extends',
                'el',
                'name',
                'key',
                'parent',
                'functional',
                ['delimiters', 'comments'],
                ['components', 'directives', 'filters'],
                'mixins',
                ['provide', 'inject'],
                'ROUTER_GUARDS',
                'layout',
                'middleware',
                'validate',
                'scrollToTop',
                'transition',
                'loading',
                'inheritAttrs',
                'model',
                ['props', 'propsData'],
                'emits',
                'setup',
                'asyncData',
                'data',
                'fetch',
                'head',
                'computed',
                'watch',
                'watchQuery',
                'LIFECYCLE_HOOKS',
                'methods',
                'renderError'
            ]
        }],
        'vue/require-v-for-key': ['warn'],
    },
    parserOptions: {
        ecmaVersion: 2021,
        sourceType: 'module',
    },
    ignorePatterns: [
        'node_modules',
        'webroot/js/*'
    ]
}
