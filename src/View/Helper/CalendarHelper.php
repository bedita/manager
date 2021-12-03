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
namespace App\View\Helper;

use Cake\Utility\Hash;
use Cake\View\Helper;

/**
 * Helper for calendars
 *
 * @property \Cake\View\Helper\TimeHelper $Time The time helper
 */
class CalendarHelper extends Helper
{
    /**
     * List of helpers used by this helper
     *
     * @var array
     */
    public $helpers = ['Time'];

    /**
     * Get calendar date ranges list html
     *
     * @param array $dateRanges The date ranges intervals
     * @return string
     */
    public function list(array $dateRanges): string
    {
        $list = '';
        foreach ($dateRanges as $dateRange) {
            $list .= $this->dateRange($dateRange);
        }

        return $list;
    }

    /**
     * Get single date range html representation
     *
     * @param array $dateRange The date range interval
     * @return string
     */
    public function dateRange(array $dateRange): string
    {
        if (empty($dateRange)) {
            return '';
        }
        $allDay = (string)Hash::get($dateRange, 'params.all_day');
        $start = (string)Hash::get($dateRange, 'start_date');
        $end = (string)Hash::get($dateRange, 'end_date');
        $isInterval = $end && !$allDay;
        $str = $isInterval ? __('from') : __('on');
        $html = '<div class="date-range"><div class="date-item">' . $str . '<span class="date">' . $this->Time->format($start, 'd MMM YYYY');
        if (!$allDay) {
            $html .= '&nbsp;' . $this->Time->format($start, 'HH:mm');
        }
        $html .= '</span></div>';
        if ($isInterval) {
            $html .= '<div class="date-item">' . __('to') . '&nbsp;<span class="date">' . $this->Time->format($end, 'd MMM YYYY') . '&nbsp;' . $this->Time->format($end, 'HH:mm') . '</span></div>';
        }
        $html .= '</div>';

        return $html;
    }
}
