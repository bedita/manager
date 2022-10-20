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
 * Helper for array utils
 */
class ArrayHelper extends Helper
{
    /**
     * Return array_combine of array using values as keys.
     *
     * @param array $arr The array.
     * @return array combined array.
     */
    public function combine(array $arr): array
    {
        return array_combine(array_values($arr), array_values($arr));
    }

    /**
     * Return array without specified keys.
     *
     * @param array $arr The array.
     * @param array $keys The keys to remove.
     * @return array The array without keys.
     */
    public function removeKeys(array $arr, array $keys): array
    {
        return array_diff_key($arr, array_flip($keys));
    }

    /**
     * Return array with only specified keys maintaining order.
     *
     * @param array $arr The array.
     * @param array $keys The ordered keys to maintain.
     * @return array The array with only selected keys.
     */
    public function onlyKeys(array $arr, array $keys): array
    {
        $res = [];
        foreach ($keys as $k) {
            $res[$k] = null;
            if (isset($arr[$k])) {
                $res[$k] = $arr[$k];
            }
        }

        return $res;
    }

    /**
     * Gets the values from an array matching the $path expression.
     *
     * The path expression is a dot separated expression, that can contain a set of patterns and expressions:
     *  {n} Matches any numeric key, or integer.
     *  {s} Matches any string key.
     *  {*} Matches any value.
     *  Foo Matches any key with the exact same value.
     *
     * @see \Cake\Utility\Hash::extract
     * @param array $data The array
     * @param string $path The path expression
     * @return array The result array
     */
    public function extract($data, $path): array
    {
        return (array)Hash::extract($data, $path);
    }
}
