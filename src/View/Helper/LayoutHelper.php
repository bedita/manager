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
     * Primary sidebar visibility
     *
     * @return bool True if visible for view
     */
    public function primarySidebar(): bool
    {
        return in_array($this->_View->getName(), ['Dashboard']);
    }

    /**
     * Secondary sidebar visibility
     *
     * @return bool True if visible for view
     */
    public function secondarySidebar(): bool
    {
        return !in_array($this->_View->getName(), ['Dashboard', 'Login']);
    }

    /**
     * Menu header visibility
     *
     * @return bool True if visible for view
     */
    public function layoutHeader(): bool
    {
        return !in_array($this->_View->getName(), ['Dashboard', 'Login']);
    }

    /**
     * Layout content visibility. Always visible (for now)
     *
     * @return bool True if visible for view
     */
    public function layoutContent(): bool
    {
        return true;
    }

    /**
     * Layout footer visibility
     *
     * @return bool True if visible for view
     */
    public function layoutFooter(): bool
    {
        return !in_array($this->_View->getName(), ['Dashboard', 'Login']);
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

            return $this->Html->link(
                Inflector::humanize($name),
                ['_name' => 'modules:list', 'object_type' => $name],
                ['class' => sprintf('has-background-module-%s', $name)]
            );
        }

        // if no `currentModule` has been set a `moduleLink` must be set in controller otherwise current link is displayed
        return $this->Html->link(
            Inflector::humanize($this->getView()->getName()),
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
        $pluginClass = (string)Configure::read(sprintf('PluginModules.%s.class.dashboard', $this->_View->getName()));
        if ($pluginClass) {
            return $pluginClass;
        }

        $moduleClasses = [
            'UserProfile' => 'has-background-black icon-user',
            'Import' => 'has-background-black icon-download-alt',
            'ObjectTypes' => 'has-background-black',
        ];

        return (string)Hash::get($moduleClasses, $this->_View->getName(), 'commands-menu__module');
    }
}
