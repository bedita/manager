<?php
    $csrfToken = null;
    if (!empty($this->request->getParam('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getParam('_csrfToken'));
    } elseif (!empty($this->request->getData('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getData('_csrfToken'));
    }
?>

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
        'modules': <?= (!empty($modules)) ? json_encode(array_keys((array)$modules), true) : [] ?>,
        'plugins': <?= json_encode(\App\Plugin::loadedAppPlugins()) ?>,
        'locale': locale,
        <?php if (!empty($uploadable)): ?>
        // types having files to upload
        'uploadable': <?= json_encode((array)$uploadable, true) ?>,
        <?php endif; ?>
        'csrfToken': <?= $csrfToken ?>
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
