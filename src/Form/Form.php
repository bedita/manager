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
}
