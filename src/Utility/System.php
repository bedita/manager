<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Utility;

class System
{
    /**
     * Compare BEdita API version
     *
     * @param string $actual Actual API version
     * @param string $expected Expected API version
     * @return bool
     */
    public static function compareBEditaApiVersion(string $actual, string $expected): bool
    {
        $apiMajor = substr($actual, 0, strpos($actual, '.'));
        $requiredApiMajor = substr($expected, 0, strpos($expected, '.'));

        return $apiMajor === $requiredApiMajor && version_compare($actual, $expected) >= 0;
    }
}
