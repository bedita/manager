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
use Cake\Utility\Hash;
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
     * Generates a form control element for an object property by name, value and options.
     * Use SchemaHelper (@see \App\View\Helper\SchemaHelper) to get control options by schema model.
     * Use FormHelper (@see \Cake\View\Helper\FormHelper::control) to render control.
     *
     * @param string $name The property name
     * @param mixed|null $value The property value
     * @param array $options The form element options, if any
     * @return string
     */
    public function control(string $name, $value, array $options = []): string
    {
        $controlOptions = $this->Schema->controlOptions($name, $value, $this->schema($name));
        if (Hash::get($controlOptions, 'class') === 'json') {
            $jsonKeys = (array)Configure::read('_jsonKeys');
            Configure::write('_jsonKeys', array_merge($jsonKeys, [$name]));
        }
        if (Hash::check($controlOptions, 'html')) {
            return (string)Hash::get($controlOptions, 'html', '');
        }

        return $this->Form->control($name, array_merge($controlOptions, $options));
    }

    /**
     * Schema array by attribute name
     *
     * @param string $name The attribute name
     * @return array
     */
    public function schema(string $name): array
    {
        $schema = (array)$this->_View->get('schema');
        if (in_array($name, ['associations', 'categories', 'relations'])) {
            return [
                'type' => $name,
                $name => Hash::get($schema, sprintf('%s', $name)),
            ];
        }

        return Hash::get($schema, sprintf('properties.%s', $name), []);
    }

    /**
     * Get object property value by object and property.
     *
     * @param array $object The object
     * @param string $property The property
     * @return string
     */
    public function objectValue(array $object, string $property): string
    {
        $paths = [
            sprintf('attributes.%s', $property),
            sprintf('meta.%s', $property),
            sprintf('stream.attributes.%s', $property),
            sprintf('stream.meta.%s', $property),
        ];
        foreach ($paths as $path) {
            if (Hash::check($object, $path)) {
                return (string)Hash::get($object, $path);
            }
        }

        return '';
    }
}
