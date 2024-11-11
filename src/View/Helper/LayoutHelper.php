<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\View\Helper;

use App\Utility\CacheTools;
use App\Utility\Translate;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper for site layout
 *
 * @property \App\View\Helper\EditorsHelper $Editors
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \App\View\Helper\LinkHelper $Link
 * @property \App\View\Helper\PermsHelper $Perms
 * @property \App\View\Helper\SystemHelper $System
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class LayoutHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Editors', 'Html', 'Link', 'Perms', 'System', 'Url'];

    /**
     * Is Dashboard
     *
     * @return bool True if visible for view
     */
    public function isDashboard(): bool
    {
        return in_array($this->_View->getName(), ['Dashboard']);
    }

    /**
     * Is Login
     *
     * @return bool True if visible for view
     */
    public function isLogin(): bool
    {
        return in_array($this->_View->getName(), ['Login']);
    }

    /**
     * Properties for various publication status
     *
     * @param array $object The object
     * @return string pubstatus
     */
    public function publishStatus(array $object = []): string
    {
        if (empty($object)) {
            return '';
        }

        $end = (string)Hash::get($object, 'attributes.publish_end');
        $start = (string)Hash::get($object, 'attributes.publish_start');

        if (!empty($end) && strtotime($end) <= time()) {
            return 'expired';
        }
        if (!empty($start) && strtotime($start) > time()) {
            return 'future';
        }
        if (!empty((string)Hash::get($object, 'meta.locked'))) {
            return 'locked';
        }
        if ((string)Hash::get($object, 'attributes.status') === 'draft') {
            return 'draft';
        }

        return '';
    }

    /**
     * Messages visibility
     *
     * @return bool True if visible for view
     */
    public function messages(): bool
    {
        return $this->_View->getName() != 'Login';
    }

    /**
     * Return title with object title and current module name
     *
     * @return string title with object title and current module name separated by '|'
     */
    public function title(): string
    {
        $module = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($module, 'name');
        $object = (array)$this->getView()->get('object');
        $title = (string)Hash::get($object, 'attributes.title');

        return empty($title) ? $name : sprintf('%s | %s', $title, $name);
    }

    /**
     * Return dashboard module link
     *
     * @param string $name The module name
     * @param array $module The module data
     * @return string
     */
    public function dashboardModuleLink(string $name, array $module): string
    {
        if (in_array($name, ['trash', 'users'])) {
            return '';
        }
        $label = $name === 'objects' ? __('All objects') : Hash::get($module, 'label', $name);
        $route = (array)Hash::get($module, 'route');
        $param = empty($route) ? ['_name' => 'modules:list', 'object_type' => $name, 'plugin' => null] : $route;
        $count = !$this->showCounter($name) ? '' : $this->moduleCount($name);

        return sprintf(
            '<a href="%s" class="%s"><span>%s</span>%s%s</a>',
            $this->Url->build($param),
            sprintf('dashboard-item has-background-module-%s %s', $name, Hash::get($module, 'class', '')),
            $this->tr($label),
            $this->moduleIcon($name, $module),
            $count
        );
    }

    /**
     * Show counter for module
     *
     * @param string $name The module name
     * @return bool
     */
    public function showCounter(string $name): bool
    {
        $counters = Configure::read('UI.modules.counters', ['trash']);

        return is_array($counters) ? in_array($name, $counters) : $counters === 'all';
    }

    /**
     * Return module count span.
     *
     * @param string $name The module name
     * @param string|null $moduleClass The module class
     * @return string
     */
    public function moduleCount(string $name, ?string $moduleClass = null): string
    {
        $count = CacheTools::getModuleCount($name);

        return sprintf('<span class="module-count">%s</span>', $count);
    }

    /**
     * Return module icon.
     *
     * @param string $name The module name
     * @param array $module The module data
     * @return string
     */
    public function moduleIcon(string $name, array $module): string
    {
        if (Hash::get($module, 'hints.multiple_types') && !Hash::get($module, 'class')) {
            return '<app-icon icon="carbon:grid" :style="{ width: \'28px\', height: \'28px\' }"></app-icon>';
        }
        $icon = (string)Configure::read(sprintf('Modules.%s.icon', $name));
        if (!empty($icon)) {
            return sprintf('<app-icon icon="%s" :style="{ width: \'28px\', height: \'28px\' }"></app-icon>', $icon);
        }
        $map = [
            'audio' => 'carbon:document-audio',
            'categories' => 'carbon:collapse-categories',
            'documents' => 'carbon:document',
            'events' => 'carbon:event',
            'files' => 'carbon:document-blank',
            'folders' => 'carbon:tree-view',
            'images' => 'carbon:image',
            'links' => 'carbon:link',
            'locations' => 'carbon:location',
            'news' => 'carbon:calendar',
            'profiles' => 'carbon:person',
            'publications' => 'carbon:wikis',
            'tags' => 'carbon:tag',
            'users' => 'carbon:user',
            'videos' => 'carbon:video',
        ];
        if (!in_array($name, array_keys($map))) {
            return '';
        }

        return sprintf('<app-icon icon="%s" :style="{ width: \'28px\', height: \'28px\' }"></app-icon>', $map[$name]);
    }

    /**
     * Return module css class(es).
     * If object is locked by parents, return base class plus 'locked' class.
     * If object is locked by concurrent editors, return 'concurrent-editors' class plus publish status class.
     * If object is not locked, return base class plus publish status class.
     * Publish status class is 'expired', 'future', 'locked' or 'draft'.
     *
     * @return string
     */
    public function moduleClass(): string
    {
        $moduleClasses = ['app-module-box'];
        $object = (array)$this->getView()->get('object');
        if (!empty($object) && $this->Perms->isLockedByParents((string)Hash::get($object, 'id'))) {
            $moduleClasses[] = 'locked';

            return trim(implode(' ', $moduleClasses));
        }
        $editors = $this->Editors->list();
        if (count($editors) > 1) {
            $moduleClasses = ['app-module-box-concurrent-editors'];
        }
        $moduleClasses[] = $this->publishStatus($object);

        return trim(implode(' ', $moduleClasses));
    }

    /**
     * Module main link
     *
     * @return string The link
     */
    public function moduleLink(): string
    {
        $currentModule = (array)$this->getView()->get('currentModule');
        if (!empty($currentModule) && !empty($currentModule['name'])) {
            $name = $currentModule['name'];
            $label = Hash::get($currentModule, 'label', $name);
            $counters = Configure::read('UI.modules.counters', ['trash']);
            $showCounter = is_array($counters) ? in_array($name, $counters) : $counters === 'all';
            $count = !$showCounter ? '' : $this->moduleCount($name);

            return sprintf(
                '<a href="%s" class="%s"><span class="mr-05">%s</span>%s%s</a>',
                $this->Url->build(['_name' => 'modules:list', 'object_type' => $name]),
                sprintf('module-item has-background-module-%s', $name),
                $this->tr($label),
                $this->moduleIcon($name, $currentModule),
                $count
            );
        }

        // if no `currentModule` has been set a `moduleLink` must be set in controller otherwise current link is displayed
        return $this->Html->link(
            $this->tr($this->getView()->getName()),
            (array)$this->getView()->get('moduleLink'),
            ['class' => $this->commandLinkClass()]
        );
    }

    /**
     * Return module index default view type
     *
     * @return string
     */
    public function moduleIndexDefaultViewType(): string
    {
        $module = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($module, 'name');

        return $name === 'folders' ? 'tree' : 'list';
    }

    /**
     * Return module index view type
     *
     * @return string
     */
    public function moduleIndexViewType(): string
    {
        $query = (array)$this->getView()->getRequest()->getQueryParams();

        return (string)Hash::get($query, 'view_type', $this->moduleIndexDefaultViewType());
    }

    /**
     * Return module index view types
     *
     * @return array
     */
    public function moduleIndexViewTypes(): array
    {
        $defaultType = $this->moduleIndexDefaultViewType();

        return $defaultType === 'tree' ? ['tree', 'list'] : ['list'];
    }

    /**
     * Return style class for command link
     *
     * @return string
     */
    protected function commandLinkClass(): string
    {
        $moduleClasses = [
            'UserProfile' => 'has-background-black icon-user',
            'Import' => 'has-background-black icon-download-alt',
            'ObjectTypes' => 'has-background-black',
            'Relations' => 'has-background-black',
            'PropertyTypes' => 'has-background-black',
            'Categories' => 'has-background-black',
            'Appearance' => 'has-background-black',
            'Applications' => 'has-background-black',
            'AsyncJobs' => 'has-background-black',
            'Config' => 'has-background-black',
            'Endpoints' => 'has-background-black',
            'Roles' => 'has-background-black',
            'RolesModules' => 'has-background-black',
            'EndpointPermissions' => 'has-background-black',
            'Tags' => 'has-background-module-tags',
            'ObjectsHistory' => 'has-background-black',
            'SystemInfo' => 'has-background-black',
            'UserAccesses' => 'has-background-black',
            'Statistics' => 'has-background-black',
        ];

        return (string)Hash::get($moduleClasses, $this->_View->getName(), 'commands-menu__module');
    }

    /**
     * Get translated val by input string, using plugins (if any) translations.
     *
     * @param string $input The input string
     * @return string|null
     */
    public function tr(string $input): ?string
    {
        return Translate::get($input);
    }

    /**
     * Return configuration items to create JSON BEDITA object
     *
     * @return array
     */
    public function metaConfig(): array
    {
        return [
            'base' => $this->Link->baseUrl(),
            'currentModule' => $this->getView()->get('currentModule', ['name' => 'home']),
            'template' => $this->getView()->getTemplate(),
            'modules' => array_keys($this->getView()->get('modules', [])),
            'plugins' => \App\Plugin::loadedAppPlugins(),
            'uploadable' => $this->getView()->get('uploadable', []),
            'locale' => \Cake\I18n\I18n::getLocale(),
            'csrfToken' => $this->getCsrfToken(),
            'maxFileSize' => $this->System->getMaxFileSize(),
            'canReadUsers' => $this->Perms->canRead('users'),
            'canSave' => $this->Perms->canSave(),
            'cloneConfig' => (array)Configure::read('Clone'),
            'placeholdersConfig' => $this->System->placeholdersConfig(),
            'uploadConfig' => $this->System->uploadConfig(),
            'relationsSchema' => $this->getView()->get('relationsSchema', []),
            'richeditorConfig' => (array)Configure::read('Richeditor'),
            'richeditorByPropertyConfig' => (array)Configure::read('UI.richeditor', []),
        ];
    }

    /**
     * Get csrf token, searching in: request params, data, attributes and cookies
     *
     * @return string|null
     */
    public function getCsrfToken(): ?string
    {
        if (!empty($this->getView()->getRequest()->getParam('_csrfToken'))) {
            return (string)$this->getView()->getRequest()->getParam('_csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getData('_csrfToken'))) {
            return (string)$this->getView()->getRequest()->getData('_csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getAttribute('csrfToken'))) {
            return (string)$this->getView()->getRequest()->getAttribute('csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getCookie('csrfToken'))) {
            return (string)$this->getView()->getRequest()->getCookie('csrfToken');
        }

        return null;
    }

    /**
     * Trash link by type.
     *
     * @param string|null $type The object type, if any.
     * @return string
     */
    public function trashLink(?string $type): string
    {
        if (empty($type) || $type === 'trash' || $type === 'translations') {
            return '';
        }

        $modules = (array)$this->_View->get('modules');
        if (!Hash::check($modules, sprintf('%s.hints.object_type', $type))) {
            return '';
        }

        $classes = sprintf('button icon icon-only-icon has-text-module-%s', $type);
        $title = $this->tr($type) . __(' in Trashcan');
        $filter = ['type' => [$type]];

        return $this->Html->link(
            sprintf('<span class="is-sr-only">%s</span><app-icon icon="carbon:trash-can"></app-icon>', __('Trash')),
            ['_name' => 'trash:list', '?' => compact('filter')],
            ['class' => $classes, 'title' => $title, 'escape' => false]
        );
    }
}
