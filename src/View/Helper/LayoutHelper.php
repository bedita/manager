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

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Helper for site layout
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 */
class LayoutHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Html'];

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
                $this->typeLabel(Inflector::humanize($label)),
                ['_name' => 'modules:list', 'object_type' => $name],
                ['class' => sprintf('has-background-module-%s', $name)]
            );
        }

        // if no `currentModule` has been set a `moduleLink` must be set in controller otherwise current link is displayed
        return $this->Html->link(
            $this->typeLabel(Inflector::humanize($this->getView()->getName())),
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
     * Get translated model by type, using plugins (if any) translations.
     *
     * @param string $type The type
     * @return string|null
     */
    public function typeLabel(string $type): ?string
    {
        $res = __($type);
        $pluginName = Hash::get((array)Configure::read('Plugins'), '0');
        // if we have no actual translation and a plugin let's try with plugin's gettext
        if ($pluginName && $res === $type) {
            return __d($pluginName, $type);
        }

        return $res;
    }
}
