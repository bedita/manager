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

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\CalendarHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * {@see \App\View\Helper\CalendarHelper} Test Case
 *
 * @coversDefaultClass \App\View\Helper\CalendarHelper
 */
class CalendarHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\CalendarHelper
     */
    public $Calendar;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $view = new View();
        $this->Calendar = new CalendarHelper($view);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Calendar);

        parent::tearDown();
    }

    /**
     * Data provider for `testList` test case.
     *
     * @return array
     */
    public function listProvider(): array
    {
        return [
            'empty date ranges' => [
                [],
                '',
            ],
            'multiple date ranges' => [
                [
                    ['start_date' => '2020-10-14 12:45:00'], // start date only
                    ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57'], // start + end date
                    ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57', 'params' => ['all_day' => true]], // all day
                ],
                '<div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020&nbsp;12:45</span></div></div><div class="date-range"><div class="date-item">from<span class="date">14 Oct 2020&nbsp;14:15</span></div><div class="date-item">to&nbsp;<span class="date">15 Oct 2020&nbsp;08:42</span></div></div><div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020</span></div></div>',
            ],
        ];
    }

    /**
     * Test `list()` method.
     *
     * @param array $dateRanges The array.
     * @param string $expected The expected string.
     * @return void
     * @dataProvider listProvider()
     * @covers ::list()
     */
    public function testList(array $dateRanges, string $expected): void
    {
        $actual = $this->Calendar->list($dateRanges);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testDateRange` test case.
     *
     * @return array
     */
    public function dateRangeProvider(): array
    {
        return [
            'empty date range' => [
                [],
                '',
            ],
            'start date only' => [
                ['start_date' => '2020-10-14 12:45:00'],
                '<div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020&nbsp;12:45</span></div></div>',
            ],
            'start + end date' => [
                ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57'],
                '<div class="date-range"><div class="date-item">from<span class="date">14 Oct 2020&nbsp;14:15</span></div><div class="date-item">to&nbsp;<span class="date">15 Oct 2020&nbsp;08:42</span></div></div>',
            ],
            'all day' => [
                ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57', 'params' => ['all_day' => true]],
                '<div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020</span></div></div>',
            ],
        ];
    }

    /**
     * Test `dateRange()` method.
     *
     * @param array $dateRange The date range.
     * @param string $expected The expected string.
     * @return void
     * @dataProvider dateRangeProvider()
     * @covers ::dateRange()
     */
    public function testDateRange(array $dateRange, string $expected): void
    {
        $actual = $this->Calendar->dateRange($dateRange);
        static::assertSame($expected, $actual);
    }
}
