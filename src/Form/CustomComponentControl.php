<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Form;

use Cake\Utility\Hash;

/**
 * Return a custom component to handle input for a property
 */
class CustomComponentControl implements CustomHandlerInterface
{
    /**
     * @inheritDoc
     */
    public function control(string $name, $value, array $options): array
    {
        // you can define a custom component via `tag` option, default is <key-value-list>
        $tag = Hash::get($options, 'tag', 'key-value-list');
        $label = (string)Hash::get($options, 'label', $name);
        $readonly = (bool)Hash::get($options, 'readonly', false);
        $type = (string)Hash::get($options, 'type');
        if ($type === 'json' || is_array($value) || is_object($value)) {
            $value = htmlspecialchars($this->jsonValue($value));
        }
        $html = sprintf(
            '<%s label="%s" name="%s" value="%s" :readonly=%s></%s>',
            $tag,
            $label,
            $name,
            $value,
            $readonly ? 'true' : 'false',
            $tag,
        );

        return compact('type', 'html', 'readonly');
    }

    /**
     * Return a string representing the json value.
     *
     * @param mixed|null $value The value
     * @return string
     */
    protected function jsonValue($value): string
    {
        if (empty($value)) {
            return '';
        }
        if (is_array($value) || is_object($value)) {
            return json_encode($value);
        }

        return json_encode(json_decode($value, true));
    }
}
