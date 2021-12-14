<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
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
 * Helper class to generate properties html for admin pages
 *
 * @property \Cake\View\Helper\FormHelper $Form The form helper
 * @property \App\View\Helper\PropertyHelper $Property The properties helper
 * @property \App\View\Helper\SchemaHelper $Schema The schema helper
 */
class AdminHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Form', 'Property', 'Schema'];

    /**
     * Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->options = [
            'text' => [
                'label' => false,
                'size' => 40,
            ],
            'bool' => [
                'label' => false,
                'type' => 'radio',
                'options' => [
                    ['value' => 1, 'text' => __('Yes')],
                    ['value' => 0, 'text' => __('No')],
                ],
            ],
            'json' => [
                'label' => false,
                'type' => 'textarea',
                'v-jsoneditor' => 'true',
                'class' => 'json',
            ],
            'combo' => [
                'label' => false,
            ],
        ];
    }

    /**
     * Control by type, property, value.
     *
     * @param string $type The type
     * @param string $property The property
     * @param mixed $value The value
     * @return string
     */
    public function control(string $type, string $property, $value): string
    {
        $readonly = (bool)$this->_View->get('readonly');
        $resource = (array)$this->_View->get('resource');
        $unchangeable = (bool)Hash::get($resource, 'meta.unchangeable', false);
        if ($readonly === true || $unchangeable === true) {
            $schema = (array)$this->_View->get('schema');

            return $this->Schema->format($value, (array)Hash::get($schema, sprintf('properties.%s', $property)));
        }

        if (in_array($type, ['bool', 'json', 'text'])) {
            return $this->Form->control($property, $this->options[$type] + compact('value'));
        }

        if ($type === 'applications') {
            $options = (array)$this->_View->get('applications');

            return $this->Form->control($property, $this->options['combo'] + compact('options', 'value'));
        }

        return $this->Property->control($property, $value, $this->options['text']);
    }
}
