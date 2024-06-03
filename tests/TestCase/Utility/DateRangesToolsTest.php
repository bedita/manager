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
            'null data' => [
                null,
                null,
            ],
            'empty data' => [
                [],
                [],
            ],
            'start date, end_date null, params null' => [
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
            'start date, end date, params null' => [
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
            'start date, end date :59:00.000, params null' => [
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
            'start date, end date null, params every_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => json_encode(['every_day' => false]),
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
            'start date, end date null, params every_day false and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => json_encode(['every_day' => false, 'all_day' => false]),
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
            'start date, end date null, params every_day false and all_day true' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => json_encode(['every_day' => false, 'all_day' => true, 'weekdays' => ['monday']]),
                    ],
                ],
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => json_encode(['all_day' => true, 'every_day' => true]),
                    ],
                ],
            ],
            'start date, end date null, params every_day true and all_day false' => [
                [
                    [
                        'start_date' => '2021-01-01 00:00:00',
                        'end_date' => null,
                        'params' => json_encode(['every_day' => true, 'all_day' => false, 'weekdays' => ['monday']]),
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
        ];
    }

    /**
     * Test `prepare` method.
     *
     * @return void
     * @dataProvider prepareProvider()
     * @covers ::prepare()
     */
    public function testPrepare(?array $dateRanges, ?array $expected): void
    {
        $actual = DateRangesTools::prepare($dateRanges);
        static::assertEquals($expected, $actual);
    }
}
