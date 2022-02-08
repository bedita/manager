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

namespace App\Form;

use Cake\Core\Configure;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Form class provides utilities for \App\Form classes.
 */
class Form
{
    /**
     * Return method [$className, $methodName], if it's callable.
     * Otherwise, throw \InvalidArgumentException
     *
     * @param string $className The class name
     * @param string $name The method name
     * @return array
     */
    public static function getMethod(string $className, string $name): array
    {
        $methodName = Inflector::variable(str_replace('-', '_', $name));
        $method = [$className, $methodName];
        if (is_callable($method)) {
            return $method;
        }

        throw new \InvalidArgumentException(sprintf('Method "%s" is not callable', $methodName));
    }

    /**
     * Return label by field name.
     * Use `__` and `__d` to try to translate it.
     *
     * @param string $name The field name
     * @return string|null
     */
    public static function label(string $name): ?string
    {
        $text = Inflector::humanize(Inflector::underscore($name));
        $label = __($text);
        $plugins = (array)Configure::read('Plugins');
        $pluginName = Hash::get(array_keys($plugins), 0);
        // if we have no actual translation and a plugin let's try with plugin's gettext
        if ($pluginName && $label === $text) {
            return __d($pluginName, $text);
        }

        return $label;
    }
}
