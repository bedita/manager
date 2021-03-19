<?php
    $csrfToken = null;
    if (!empty($this->request->getParam('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getParam('_csrfToken'));
    } elseif (!empty($this->request->getData('_csrfToken'))) {
        $csrfToken = json_encode($this->request->getData('_csrfToken'));
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
        'base' => \Cake\Routing\Router::fullBaseUrl(),
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
