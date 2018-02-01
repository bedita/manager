<meta name="BEDITA.currLang" content="eng" />
<meta name="BEDITA.currLang2" content="en" />
<meta name="BEdita.base" content="<?= \Cake\Routing\Router::fullBaseUrl() ?>" />

<script type="text/javascript">

    // global JSON BEDITA config
    var BEDITA = {
        'currLang': 'eng',
        'currLang2': 'en',
        'base': '<?= \Cake\Routing\Router::fullBaseUrl() ?>',
        <?php if (!empty($object['id'])): ?>'id': <?= $object['id'] ?>,<?php endif; ?>
        'currentModule': <?php if (!empty($currentModule)): ?> <?= json_encode($currentModule, true) ?> <?php else: ?>{ name: 'home' }<?php endif; ?>,
        'action': '{$view->action|default:"index"}',
        'relations': {}
    };

</script>
