<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Helper class to generate properties html
 *
 * @property \App\View\Helper\SchemaHelper $Schema The schema helper
 * @property \Cake\View\Helper\FormHelper $Form The form helper
 */
class PropertyHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Form', 'Schema'];

    /**
     * View the property
     *
     * @param string $key The property key
     * @param mixed|null $value The property value
     * @param array $schema The property schema
     * @param array $options The form element options, if any
     * @return string
     */
    public function view(string $key, $value, array $schema, array $options = []): string
    {
        $controlOptions = $this->Schema->controlOptions($key, $value, $schema);
        if (Hash::get($controlOptions, 'class') === 'json') {
            $jsonKeys = Configure::read('_jsonKeys');
            Configure::write('_jsonKeys', array_merge($jsonKeys, [$key]));
        }
        if (Hash::check($controlOptions, 'html')) {
            return Hash::get($controlOptions, 'html', '');
        }

        return $this->Form->control($key, array_merge($controlOptions, $options));
    }
}
