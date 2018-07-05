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
    public function primarySidebar() : bool
    {
        return in_array($this->_View->name, ['Dashboard']);
    }

    /**
     * Secondary sidebar visibility
     *
     * @return bool True if visible for view
     */
    public function secondarySidebar() : bool
    {
        if (in_array($this->_View->name, ['Import', 'UserProfile', 'Model'])) {
            return true;
        }

        return !empty($this->_View->viewVars['currentModule']);
    }

    /**
     * Menu header visibility
     *
     * @return bool True if visible for view
     */
    public function layoutHeader() : bool
    {
        return !in_array($this->_View->name, ['Dashboard', 'Login']);
    }

    /**
     * Layout content visibility. Always visible (for now)
     *
     * @return bool True if visible for view
     */
    public function layoutContent() : bool
    {
        return true;
    }

    /**
     * Layout footer visibility
     *
     * @return bool True if visible for view
     */
    public function layoutFooter() : bool
    {
        return !empty($this->_View->viewVars['currentModule']);
    }

    /**
     * Messages visibility
     *
     * @return bool True if visible for view
     */
    public function messages() : bool
    {
        return $this->_View->name != 'Login';
    }

    /**
     * Module main link
     *
     * @return string The link
     */
    public function moduleLink() : string
    {
        if (!empty($this->_View->viewVars['currentModule']['name'])) {
            $name = $this->_View->viewVars['currentModule']['name'];

            return $this->Html->link(
                Inflector::humanize($name),
                ['_name' => 'modules:list', 'object_type' => $name],
                ['class' => sprintf('has-background-module-%s', $name)]
            );
        }

        // if no `currentModule` has been set a `moduleLink` must be set in controller otherwise current link is displayed
        return $this->Html->link(
            Inflector::humanize($this->_View->name),
            (array)Hash::get($this->_View->viewVars, 'moduleLink', []),
            ['class' => $this->commandLinkClass()]
        );
    }

    /**
     * Return style class for command link
     *
     * @return string
     */
    protected function commandLinkClass() : string
    {
        $moduleClasses = [
            'UserProfile' => 'has-background-black icon-user',
            'Import' => 'has-background-black icon-download-alt',
            'Model' => 'has-background-black icon-database',
        ];

        return (string)Hash::get($moduleClasses, $this->_View->name, 'commands-menu__module');
    }
}
