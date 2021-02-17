<?php
    $csrfToken = null;
    if (!empty($this->request->getParam('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getParam('_csrfToken'));
    } elseif (!empty($this->request->getData('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getData('_csrfToken'));
    }
?>

<script type="text/javascript">

    const BEDITA = {
        'base': '<?= \Cake\Routing\Router::fullBaseUrl() ?>',
        'currentModule': <?= !empty($currentModule) ? json_encode($currentModule, true) : '{ name: "home" }' ?>,
        'template': '<?= $this->template ?>',
        'modules': <?= !empty($modules) ? json_encode(array_keys((array)$modules), true) : '[]' ?>,
        'plugins': <?= json_encode(\App\Plugin::loadedAppPlugins()) ?>,
        'locale': '<?= \Cake\I18n\I18n::getLocale() ?>',
        'uploadable': <?= !empty($uploadable) ? json_encode((array)$uploadable, true) : '[]' ?>,
        'csrfToken': <?= $csrfToken ?>
    };

</script>
