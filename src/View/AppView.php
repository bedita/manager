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
namespace App\View;

use BEdita\WebTools\View\TwigView;
use Cake\Core\Configure;
use Cake\Utility\Hash;

/**
 * Application View default class
 *
 * @property \App\View\Helper\AdminHelper $Admin
 * @property \App\View\Helper\CalendarHelper $Calendar
 * @property \App\View\Helper\CategoriesHelper $Categories
 * @property \App\View\Helper\EditorsHelper $Editors
 * @property \App\View\Helper\LayoutHelper $Layout
 * @property \App\View\Helper\ArrayHelper $Array
 * @property \App\View\Helper\LinkHelper $Link
 * @property \App\View\Helper\PropertyHelper $Property
 * @property \App\View\Helper\PermsHelper $Perms
 * @property \App\View\Helper\SchemaHelper $Schema
 * @property \App\View\Helper\SystemHelper $System
 * @property \BEdita\WebTools\View\Helper\ThumbHelper $Thumb
 * @property \BEdita\I18n\View\Helper\I18nHelper $I18n
 */
class AppView extends TwigView
{
    /**
     * Constant for view file type 'element'
     *
     * @var string
     */
    public const TYPE_ELEMENT = 'Element';

    /**
     * Constant for view file type 'layout'
     *
     * @var string
     */
    public const TYPE_LAYOUT = 'Layout';

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->addHelper('Flash');
        $this->addHelper('Form', [
            'templates' => [
                'hiddenBlock' => '{{content}}',
                'submitContainer' => '{{content}}',
                'inputContainer' => '<div class="input {{type}}{{required}} {{containerClass}}">{{content}}</div>',
            ],
        ]);
        $this->addHelper('Admin');
        $this->addHelper('Calendar');
        $this->addHelper('Categories');
        $this->addHelper('Editors');
        $this->addHelper('Element');
        $this->addHelper('Layout');
        $this->addHelper('Array');
        $this->addHelper('Html');
        $this->addHelper('Link');
        $this->addHelper('Property');
        $this->addHelper('Time', ['outputTimezone' => Configure::read('I18n.timezone', 'UTC')]);
        $this->addHelper('Perms');
        $this->addHelper('Schema');
        $this->addHelper('System');
        $this->addHelper('Text');
        $this->addHelper('BEdita/WebTools.Thumb');
        $this->addHelper('Url');
        $this->addHelper('BEdita/I18n.I18n');
    }

    /**
     * {@inheritDoc}
     *
     * Retrieve custom view `element` from configuration mappings.
     *
     * If `Elements.{module_name}.{element_name}` exists in configuration a custom element is loaded
     */
    protected function _getElementFileName($name, $pluginCheck = true): string
    {
        $module = (array)$this->get('currentModule', []);
        $custom = Configure::read(sprintf('Elements.%s', Hash::get($module, 'name', '')));
        if (!empty($custom[$name])) {
            $name = $custom[$name];
        }

        return parent::_getElementFileName($name, $pluginCheck);
    }
}
