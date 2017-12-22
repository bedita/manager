<meta name="BEDITA.currLang" content="eng" />
<meta name="BEDITA.currLang2" content="en" />
<!-- <meta name="BEDITA.webroot" content="{$session->webroot}" /> -->
<meta name="BEdita.base"  content="<?= $baseUrl ?>" />

<script type="text/javascript">

    // global JSON BEDITA config
    var BEDITA = {
        'currLang': 'eng',
        'currLang2': 'en',
        // 'webroot': '{$session->webroot}',
        'base': '<?= $baseUrl ?>',
        <?php if (!empty($object['id'])): ?>'id': <?= $object['id'] ?>,<?php endif; ?>
        'currentModule': <?php if (!empty($currentModule)): ?> <?= json_encode($currentModule, true) ?> <?php else: ?>{ name: 'home' }<?php endif; ?>,
        'action': '{$view->action|default:"index"}',
        'relations': {}
    };

	// {if !empty($allObjectsRelations)}
	// 	BEDITA.relations = {$allObjectsRelations|json_encode};
	// {/if}

</script>
