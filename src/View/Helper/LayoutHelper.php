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
use Cake\View\Helper;

/**
 * Helper for site layout
 */
class LayoutHelper extends Helper
{
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
        if (in_array($this->_View->name, ['Import', 'UserProfile'])) {
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
     * Return style class for command link
     *
     * @return string
     */
    public function commandLinkClass() : string
    {
        if ($this->_View->name === 'UserProfile') {
            return 'icon-user';
        }
        if ($this->_View->name === 'Import') {
            return 'icon-download-alt';
        }

        return 'commands-menu__module';
    }
}
