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

use Cake\Core\Configure;
use Cake\Routing\Router;
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
    public function combine(array $arr) : array
    {
        return array_combine(array_values($arr), array_values($arr));
    }
}
