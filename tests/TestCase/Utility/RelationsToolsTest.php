<?php
declare(strict_types=1);

namespace App\Test\TestCase;

use App\Utility\RelationsTools;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * App\Utility\RelationsTools Test Case
 */
#[CoversClass(RelationsTools::class)]
#[CoversMethod(RelationsTools::class, 'toString')]
class RelationsToolsTest extends TestCase
{
    /**
     * Test `toString` method.
     *
     * @return void
     */
    public function testToString(): void
    {
        $relations = [
            [
                'id' => 1,
                'type' => 'event',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                    ],
                ],
            ],
            [
                'id' => 2,
                'type' => 'document',
                'meta' => [
                    'relation' => [
                        'priority' => 2,
                    ],
                ],
            ],
        ];
        $expected = '1-event-{"priority":1},2-document-{"priority":2}';
        $actual = RelationsTools::toString($relations);
        static::assertEquals($expected, $actual);
    }
}
