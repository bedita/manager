<?php
/**
 * @var \App\View\AppView $this
 */
    $csrfToken = null;
    if (!empty($this->getRequest()->getParam('_csrfToken'))) {
        $csrfToken = $this->getRequest()->getParam('_csrfToken');
    } elseif (!empty($this->getRequest()->getData('_csrfToken'))) {
        $csrfToken = $this->getRequest()->getData('_csrfToken');
    }
    if (!isset($modules)) {
        $modules = [];
    }
    if (!isset($uploadable)) {
        $uploadable = [];
    }
    if (!isset($currentModule)) {
        $currentModule = ['name' => 'home'];
    }

    $conf = [
        'base' => $this->Link->baseUrl(),
        'currentModule' => $currentModule,
        'template' => $this->template,
        'modules' => array_keys($modules),
        'plugins' => \App\Plugin::loadedAppPlugins(),
        'uploadable' => $uploadable,
        'locale' => \Cake\I18n\I18n::getLocale(),
        'csrfToken' => $csrfToken,
    ];
?>

<script type="text/javascript">

    const BEDITA = <?= json_encode($conf) ?>;

</script>
