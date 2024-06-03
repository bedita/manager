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
            $item['params'] = self::parseParams(Hash::get($item, 'params'), self::isOneDayRange($item));
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
     * @param array|null $params Params to parse.
     * @param bool $oneDayRange Is one day range.
     * @return array|null
     */
    public static function parseParams(?array $params, bool $oneDayRange): ?array
    {
        // empty params
        if (empty($params)) {
            return $params;
        }
        // remove all_day and every_day if not needed
        $params = self::filterNotOn($params, 'all_day');
        $params = self::filterNotOn($params, 'every_day');
        // one day range
        $allDayOn = Hash::get($params, 'all_day') === 'on';
        if ($oneDayRange) {
            return $allDayOn ? ['all_day' => 'on', 'every_day' => 'on'] : null;
        }
        // if multi day, all_day has no sense: remove it
        $params = array_filter(
            $params,
            function ($key) {
                return $key !== 'all_day';
            },
            ARRAY_FILTER_USE_KEY
        );
        // multi days range
        $everyDayOn = Hash::get($params, 'every_day') === 'on';
        $weekdays = Hash::get($params, 'weekdays');
        if ($everyDayOn || count($weekdays) === 7) {
            $params['every_day'] = 'on';
            $params = array_filter(
                $params,
                function ($key) {
                    return $key !== 'weekdays';
                },
                ARRAY_FILTER_USE_KEY
            );
        }

        return $params === ['every_day' => 'on'] ? null : $params;
    }

    /**
     * Filter params removing key if value is 'on'.
     *
     * @param array $params Params to filter.
     * @param string $key Key to filter.
     * @return array
     */
    public static function filterNotOn(array $params, string $key): array
    {
        return Hash::get($params, $key) === 'on' ? $params : array_filter(
            $params,
            function ($k) use ($key) {
                return $k !== $key;
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
