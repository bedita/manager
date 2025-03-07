<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\QueryComponent;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\QueryComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\QueryComponent
 */
class QueryComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\QueryComponent
     */
    public QueryComponent $Query;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $controller = new Controller(new ServerRequest());
        $registry = $controller->components();
        /** @var \App\Controller\Component\QueryComponent $queryComponent */
        $queryComponent = $registry->load(QueryComponent::class);
        $this->Query = $queryComponent;
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Query);

        parent::tearDown();
    }

    /**
     * Provider for `testIndex`.
     *
     * @return array
     */
    public static function indexProvider(): array
    {
        return [
            'query filter' => [
                ['filter' => ['gustavo' => true]], // query params
                [], // config
                ['filter' => ['gustavo' => true]], // expected
            ],
            'no query' => [
                ['some' => ['thing' => 'else']],
                [],
                ['some' => ['thing' => 'else'], 'sort' => '-id'],
            ],
            'config include object' => [
                [],
                ['include' => 'object'],
                ['sort' => '-id', 'include' => 'object'],
            ],
            'history filter' => [
                ['filter' => ['history_editor' => true]],
                [],
                ['filter' => ['history_editor' => '']],
            ],
            'query sort default -id' => [
                ['sort' => ''],
                ['include' => 'object'],
                ['sort' => '-id', 'include' => 'object'],
            ],
            'unset query sort' => [
                ['sort' => 'whatever', 'q' => 'search'],
                ['include' => 'object'],
                ['q' => 'search', 'include' => 'object'],
            ],
        ];
    }

    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     * @covers ::handleSort()
     * @dataProvider indexProvider()
     */
    public function testIndex(array $queryParams, array $config, array $expected): void
    {
        $controller = new Controller(
            new ServerRequest(
                [
                    'query' => $queryParams,
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ]
            )
        );
        $registry = $controller->components();
        /** @var \App\Controller\Component\QueryComponent $Query */
        $Query = $registry->load(QueryComponent::class);
        foreach ($config as $key => $val) {
            $Query->setConfig($key, $val);
        }
        $actual = $Query->index();
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testPrepare`
     *
     * @return array
     */
    public static function prepareProvider(): array
    {
        return [
            'simple' => [
                [
                    'page_size' => 7,
                    'q' => 'gustavo',
                ],
                [
                    'page_items' => 32,
                    'page_size' => 7,
                    'count' => 123,
                    'q' => 'gustavo',
                    'filter' => [],
                ],
            ],

            'filter 1' => [
                [
                    'filter' => [
                        'type' => 'documents',
                    ],
                ],
                [
                    'filter' => [
                        'type' => 'documents',
                        'b' => null,
                    ],
                ],
            ],
            'filter 2' => [
                [],
                [
                    'filter' => [
                        'type' => null,
                        'a' => '',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `prepare` method.
     *
     * @return void
     * @dataProvider prepareProvider
     * @covers ::prepare()
     */
    public function testPrepare(array $expected, array $query): void
    {
        $actual = $this->Query->prepare($query);
        static::assertEquals($expected, $actual);
    }
}
