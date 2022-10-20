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

use App\Utility\Translate;
use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper for site layout
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \App\View\Helper\LinkHelper $Link
 * @property \App\View\Helper\PermsHelper $Perms
 * @property \App\View\Helper\SystemHelper $System
 */
class LayoutHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html', 'Link', 'Perms', 'System'];

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

            return $this->Html->link(
                $this->tr($label),
                ['_name' => 'modules:list', 'object_type' => $name],
                ['class' => sprintf('has-background-module-%s', $name)]
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
        ];

        return (string)Hash::get($moduleClasses, $this->_View->getName(), 'commands-menu__module');
    }

    /**
     * Return custom element via `Properties` configuration for
     * a relation or property group in current module.
     *
     * @param string $item Relation or group name
     * @param string $type Item type: `relation` or `group`
     * @return string
     */
    public function customElement(string $item, string $type = 'relation'): string
    {
        $currentModule = (array)$this->getView()->get('currentModule');
        $name = (string)Hash::get($currentModule, 'name');
        if ($type === 'relation') {
            $path = sprintf('Properties.%s.relations._element.%s', $name, $item);
        } else {
            $path = sprintf('Properties.%s.view.%s._element', $name, $item);
        }

        return (string)Configure::read($path);
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
            return $this->getView()->getRequest()->getParam('_csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getData('_csrfToken'))) {
            return $this->getView()->getRequest()->getData('_csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getAttribute('csrfToken'))) {
            return $this->getView()->getRequest()->getAttribute('csrfToken');
        }
        if (!empty($this->getView()->getRequest()->getCookie('csrfToken'))) {
            return $this->getView()->getRequest()->getCookie('csrfToken');
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
        if (empty($type)) {
            return '';
        }

        $modules = (array)$this->_View->get('modules');
        if (!Hash::check($modules, sprintf('%s.hints.object_type', $type))) {
            return '';
        }

        $classes = sprintf('button icon icon-trash icon-only-icon has-text-module-%s', $type);
        $title = $this->tr($type) . __(' in Trashcan');
        $filter = ['type' => [$type]];

        return $this->Html->link(
            sprintf('<span class="is-sr-only">%s</span>', __('Trash')),
            ['_name' => 'trash:list', '?' => compact('filter')],
            ['class' => $classes, 'title' => $title, 'escape' => false]
        );
    }
}
