<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Utility;

use Cake\Utility\Hash;

/**
 * Date Ranges tools
 */
class DateRangesTools
{
    /**
     * Prepare date ranges.
     *
     * @param array|null $inputDateRanges Date ranges to format.
     * @return array|null
     */
    public static function prepare(?array $inputDateRanges): ?array
    {
        if (empty($inputDateRanges)) {
            return $inputDateRanges;
        }
        $dateRanges = $inputDateRanges;
        $dateRanges = array_filter(
            (array)$dateRanges,
            function ($item) {
                $sd = (string)Hash::get($item, 'start_date');
                $ed = (string)Hash::get($item, 'end_date');

                return !empty($sd) || !empty($ed);
            }
        );
        $dateRanges = array_map(
            function ($item) {
                $ed = (string)Hash::get($item, 'end_date');
                if (empty($ed)) {
                    return $item;
                }
                $item['end_date'] = str_replace(':59:00.000', ':59:59.000', $ed);

                return $item;
            },
            $dateRanges
        );
        foreach ($dateRanges as &$item) {
            $params = (string)Hash::get($item, 'params');
            if (empty($params)) {
                continue;
            }
            $params = json_decode($params, true);
            $sd = (string)Hash::get($item, 'start_date');
            $ed = (string)Hash::get($item, 'end_date');
            $allDay = Hash::get($params, 'all_day');
            if (!empty($sd) && empty($ed)) {
                if (!array_key_exists('all_day', $params) || $allDay === false) {
                    $item['params'] = null;

                    continue;
                }
                // all_day is true, no need for weekdays
                $item['params'] = json_encode([
                    'all_day' => true,
                    'every_day' => true,
                ]);
            }
        }
        $dateRanges = array_values($dateRanges);

        return $dateRanges;
    }
}
