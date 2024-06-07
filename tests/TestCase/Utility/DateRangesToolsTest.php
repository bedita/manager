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
            'one day + params every_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => false],
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
            'one day + params every_day false and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => false, 'all_day' => false],
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
            'one day + params every_day false and all_day true' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => true, 'all_day' => true, 'weekdays' => ['monday' => true]],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['all_day' => true, 'every_day' => true],
                    ],
                ],
            ],
            'one day + params every_day true and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => ['every_day' => true, 'all_day' => false, 'weekdays' => ['monday' => true]],
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
            'multi days + params every_day true and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => true, 'all_day' => false, 'weekdays' => ['monday' => true]],
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
            'multi days + params every_day false and all_day true' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => false, 'all_day' => true, 'weekdays' => ['monday' => true]],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['all_day' => true, 'weekdays' => ['monday' => true]],
                    ],
                ],
            ],
            'multi days + params every_day false and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => false, 'all_day' => false, 'weekdays' => ['monday' => true]],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['weekdays' => ['monday' => true]],
                    ],
                ],
            ],
            'multi days + params every_day true and all_day true' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => true, 'all_day' => true, 'weekdays' => ['monday' => true]],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => true, 'all_day' => true],
                    ],
                ],
            ],
            'range with weekdays' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => false, 'all_day' => false, 'weekdays' => ['monday' => true, 'tuesday' => true]],
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['weekdays' => ['monday' => true, 'tuesday' => true]],
                    ],
                ],
            ],
            'range with all weekdays and every_day true' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => true, 'all_day' => false, 'weekdays' => ['monday' => true, 'tuesday' => true, 'wednesday' => true, 'thursday' => true, 'friday' => true, 'saturday' => true, 'sunday' => true]],
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
            'range with no weekdays and every_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => '2021-01-02 00:00:00',
                        'params' => ['every_day' => false, 'all_day' => false, 'weekdays' => []],
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
     * @covers ::cleanParams()
     * @covers ::filterNotOn()
     * @covers ::isOneDayRange()
     */
    public function testPrepare(array $dateRanges, array $expected): void
    {
        $actual = DateRangesTools::prepare($dateRanges);
        static::assertEquals($expected, $actual);
    }
}
