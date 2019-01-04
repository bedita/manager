<meta name="BEDITA.currLang" content="eng" />
<meta name="BEDITA.currLang2" content="en" />
<meta name="BEdita.base" content="<?= \Cake\Routing\Router::fullBaseUrl() ?>" />

<script type="text/javascript">

    var locale = '<?= \Cake\I18n\I18n::getLocale() ?>';

    // global JSON BEDITA config
    var BEDITA = {
        'currLang': 'eng',
        'currLang2': 'en',
        'base': '<?= \Cake\Routing\Router::fullBaseUrl() ?>',
        <?php if (!empty($object['id'])): ?>'id': <?= $object['id'] ?>,<?php endif; ?>
        'currentModule': <?php if (!empty($currentModule)): ?> <?= json_encode($currentModule, true) ?> <?php else: ?>{ name: 'home' }<?php endif; ?>,
        'template': '<?= $this->template ?>',
        'relations': {},
        'plugins': '<?= json_encode(\App\Plugin::loadedAppPlugins()) ?>',
        'locale': locale
    };

    if (BEDITA.plugins) {
        try {
            BEDITA.plugins = JSON.parse(BEDITA.plugins);
        } catch(e) {
            console.err(e);
        }
    } else {
        BEDITA.plugins = [];
    }

</script>
