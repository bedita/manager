<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Utility;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Translate class
 */
class Translate
{
    /**
     * Return translated string by input string.
     * Use `__` and `__d` to try to translate it.
     *
     * @param string $input The input string
     * @return string|null
     */
    public static function get(string $input): ?string
    {
        $text = Inflector::humanize($input);
        $translation = __($text);
        $plugins = (array)Configure::read('Plugins');
        $pluginName = Hash::get(array_keys($plugins), 0);
        if ($pluginName && $translation === $text) {
            return __d($pluginName, $text);
        }

        return $translation;
    }
}
