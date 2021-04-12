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
namespace App\Utility;

/**
 * File utility class
 */
class File
{
    /**
     * Get bytes formatted string by size and precision
     *
     * @param float $size The size
     * @param int $precision The precision
     * @return string
     */
    public static function formatBytes(float $size, int $precision = 2): string
    {
        $base = log($size, 1024);
        $suffixes = ['', 'Kb', 'Mb', 'Gb', 'Tb'];
        $val = round(pow(1024, $base - floor($base)), $precision);
        $suffix = $suffixes[floor($base)];

        return sprintf('%f %s', $val, $suffix);
    }
}
