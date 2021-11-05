module.exports = {
    root: true,
    env: {
        'node': true,
        'es6': true,
        'browser': true,
    },
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
        'semi': 'off'
    },
    parserOptions: {
        ecmaVersion: 2021,
        sourceType: 'module',
    },
    ignorePatterns: [
        'node_modules',
        'webroot/js/*'
    ],
    extends: ['eslint:recommended']
}
