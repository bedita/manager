<?php
declare(strict_types=1);

namespace App\Test\TestCase;

use App\Utility\DateRangesTools;
use Cake\TestSuite\TestCase;

/**
 * App\Utility\DateRangesTools Test Case
 *
 * @coversDefaultClass App\Utility\DateRangesTools
 */
class DateRangesToolsTest extends TestCase
{
    /**
     * Data provider for `testPrepare` test case.
     *
     * @return array
     */
    public function prepareProvider(): array
    {
        return [
            'empty ranges' => [
                [],
                [],
            ],
            'one date only + params null' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => null,
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => null,
                    ],
                ],
            ],
            'same day + params null' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-01 23:59:00',
                        'params' => null,
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-01 23:59:00',
                        'params' => null,
                    ],
                ],
            ],
            'same day + end date :59:00.000 + params null' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-01 23:59:00.000',
                        'params' => null,
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-01 23:59:59.000',
                        'params' => null,
                    ],
                ],
            ],
            'one day + params every_day off' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => 'off'],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => null,
                    ],
                ],
            ],
            'one day + params every_day off and all_day off' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => 'off', 'all_day' => 'off'],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => null,
                    ],
                ],
            ],
            'one day + params every_day off and all_day on' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => 'on', 'all_day' => 'on', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['all_day' => 'on', 'every_day' => 'on'],
                    ],
                ],
            ],
            'one day + params every_day on and all_day off' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => 'on', 'all_day' => 'off', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => null,
                    ],
                ],
            ],
            'multi days + params every_day on and all_day off' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'on', 'all_day' => 'off', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => null,
                    ],
                ],
            ],
            'multi days + params every_day off and all_day on' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'off', 'all_day' => 'on', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['all_day' => 'on', 'weekdays' => ['monday']],
                    ],
                ],
            ],
            'multi days + params every_day off and all_day off' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'off', 'all_day' => 'off', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['weekdays' => ['monday']],
                    ],
                ],
            ],
            'multi days + params every_day on and all_day on' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'on', 'all_day' => 'on', 'weekdays' => ['monday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'on', 'all_day' => 'on'],
                    ],
                ],
            ],
            'range with weekdays' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => 'off', 'all_day' => 'off', 'weekdays' => ['monday', 'tuesday']],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['weekdays' => ['monday', 'tuesday']],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `prepare` method.
     *
     * @param array $dateRanges Date ranges to format.
     * @param array $expected Expected result.
     * @return void
     * @dataProvider prepareProvider()
     * @covers ::prepare()
     * @covers ::parseParams()
     * @covers ::filterNotOn()
     * @covers ::isOneDayRange()
     * @covers ::weekdays()
     */
    public function testPrepare(array $dateRanges, array $expected): void
    {
        $actual = DateRangesTools::prepare($dateRanges);
        static::assertEquals($expected, $actual);
    }
}
