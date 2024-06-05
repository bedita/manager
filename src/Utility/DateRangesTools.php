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
     * @param array $inputDateRanges Date ranges to format.
     * @return array
     */
    public static function prepare(array $inputDateRanges): array
    {
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
            if (empty(Hash::get($item, 'params'))) {
                continue;
            }
            $params = Hash::get($item, 'params');
            $params = is_string($params) ? json_decode($params, true) : (array)$params;
            $item['params'] = self::parseParams($params, self::isOneDayRange((array)$item));
        }
        $dateRanges = array_values($dateRanges);

        return $dateRanges;
    }

    /**
     * Check if date range is one day only.
     *
     * @param array $dateRange Date range.
     * @return bool
     */
    public static function isOneDayRange(array $dateRange): bool
    {
        $sd = (string)Hash::get($dateRange, 'start_date');
        $ed = (string)Hash::get($dateRange, 'end_date');

        return empty($ed) || (substr($sd, 0, 10) === substr($ed, 0, 10));
    }

    /**
     * Parse params.
     *
     * @param array $params Params to parse.
     * @param bool $oneDayRange Is one day range.
     * @return array|null
     */
    public static function parseParams(array $params, bool $oneDayRange): ?array
    {
        $params = self::cleanParams($params);
        if (empty($params)) {
            return null;
        }
        // one day range
        if ($oneDayRange) {
            $allDay = Hash::get($params, 'all_day') === 'on';

            return $allDay ? ['all_day' => 'on', 'every_day' => 'on'] : null;
        }
        // multi days range

        return $params === ['every_day' => 'on'] ? null : $params;
    }

    /**
     * Clean params, remove all_day false, every_day false, weekdays empty.
     *
     * @param array $params Params to clean.
     * @return array|null
     */
    public static function cleanParams(array $params): ?array
    {
        $data = [];
        foreach ($params as $key => $value) {
            if ($key === 'all_day' && $value === true) {
                $data[$key] = 'on';

                continue;
            }
            if ($key === 'every_day' && $value === true) {
                $data[$key] = 'on';

                continue;
            }
            if ($key === 'weekdays' && !empty($value)) {
                $count = 0;
                foreach ($value as $kk => $vv) {
                    if ($vv === true) {
                        $data[$key][] = $kk;
                        $count++;
                    }
                }
                if ($count === 7 || $count === 0) {
                    $data['every_day'] = 'on';
                    unset($data[$key]);
                } elseif ($params['every_day'] === true) {
                    unset($data[$key]);
                }
            }
        }

        return empty($data) ? null : $data;
    }
}
