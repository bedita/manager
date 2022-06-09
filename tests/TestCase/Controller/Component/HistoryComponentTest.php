<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\HistoryComponent;
use App\Controller\Component\SchemaComponent;
use App\Test\TestCase\Controller\AppControllerTest;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\Component\HistoryComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\HistoryComponent
 */
class HistoryComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\HistoryComponent
     */
    public $HistoryComponent;

    /**
     * Http Request
     *
     * @var \Cake\Http\ServerRequest
     */
    private $Request = null;

    /**
     * Schema component
     *
     * @var \App\Controller\Component\SchemaComponent
     */
    public $SchemaComponent;

    /**
     * Client class
     *
     * @var \BEdita\SDK\BEditaClient
     */
    private $ApiClient = null;

    /**
     * Document ID
     *
     * @var string
     */
    private $documentId = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        /** @var \App\Controller\Component\HistoryComponent $historyComponent */
        $historyComponent = $registry->load(HistoryComponent::class);
        $this->HistoryComponent = $historyComponent;
        /** @var \App\Controller\Component\SchemaComponent $schemaComponent */
        $schemaComponent = $registry->load(SchemaComponent::class);
        $this->SchemaComponent = $schemaComponent;
        $user = getenv('BEDITA_ADMIN_USR');
        $pass = getenv('BEDITA_ADMIN_PWD');
        $this->ApiClient = ApiClientProvider::getApiClient();
        $response = $this->ApiClient->authenticate($user, $pass);
        $this->ApiClient->setupTokens($response['meta']);
        $this->Request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);
        $response = $this->ApiClient->save('documents', [
            'title' => 'test history controller',
        ]);
        $this->documentId = Hash::get($response, 'data.id');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->HistoryComponent);
        unset($this->SchemaComponent);
        unset($this->ApiClient);
        unset($this->documentId);
        parent::tearDown();
    }

    /**
     * Provider for `testLoad` method
     *
     * @return array
     */
    public function loadProvider(): array
    {
        return [
            'empty object' => [
                [],
                '',
            ],
            'object no data' => [
                [
                    'objectType' => 'documents',
                    'historyId' => 1,
                ],
                '',
            ],
            'a dummy document' => [
                [
                    'objectType' => 'documents',
                    'historyId' => 1,
                ],
                '{"title":"dummy"}',
            ],
        ];
    }

    /**
     * Test `load` method
     *
     * @covers ::load()
     * @dataProvider loadProvider()
     * @param array $object The object for test
     * @param string $expected The expected object
     * @return void
     */
    public function testLoad(array $object, string $expected): void
    {
        $session = $this->HistoryComponent->getController()->getRequest()->getSession();
        if ($expected != '') {
            $key = sprintf('history.%s.attributes', $this->documentId);
            $session->write($key, $expected);
        }
        $this->HistoryComponent->load($this->documentId, $object);
        if ($expected != '') {
            static::assertNull($session->read($key));
            static::assertEquals((array)json_decode($expected, true), $object['attributes']);
        } else {
            static::assertArrayNotHasKey('attributes', $object);
        }
    }

    /**
     * Provider for `testWrite` method
     *
     * @return array
     */
    public function writeProvider(): array
    {
        return [
            'test document, keepUname false' => [
                [
                    'objectType' => 'documents',
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => false,
                ],
                '{"status":"","uname":"","title":"a new title","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":"","field":"a new value for field"}',
            ],
            'test document, keepUname true' => [
                [
                    'objectType' => 'documents',
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => true,
                ],
                '{"status":"","uname":null,"title":"a new title","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":"","field":"a new value for field"}',
            ],
        ];
    }

    /**
     * Test `write` method
     *
     * @param array $options The options for test
     * @param string $expected The expected serialized data
     * @return void
     * @covers ::write()
     * @dataProvider writeProvider()
     */
    public function testWrite(array $options, string $expected): void
    {
        // mock api /history
        $response = [
            'data' => [
                [
                    'meta' => [
                        'changed' => [
                            'field' => 'a new value for field',
                        ],
                    ],
                ],
            ],
        ];
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/history')
            ->willReturn($response);
        // set options
        $options += [
            'ApiClient' => $apiClient,
            'Schema' => $this->SchemaComponent,
            'Request' => $this->Request->withQueryParams(['title' => 'a new title']),
        ];
        $this->HistoryComponent->write($options);
        $session = $this->HistoryComponent->getController()->getRequest()->getSession();
        $actual = $session->read(sprintf('history.%s.attributes', $options['id']));
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testFetch` method
     *
     * @return array
     */
    public function fetchProvider(): array
    {
        return [
            'a document history' => [
                [
                    'properties' => [
                        'title' => [
                            'oneOf' => [
                                ['type' => null],
                                ['type' => 'string'],
                            ],
                            '$id' => '/properties/title',
                            'title' => 'Title',
                        ],
                    ],
                ],
                [
                    'title' => '<div class="history-field"><label>Title</label>test history controller</div>',
                ],
            ],
        ];
    }

    /**
     * Test `fetch` method
     *
     * @covers ::fetch()
     * @dataProvider fetchProvider()
     * @param array $data The data for test
     * @param mixed $expected The expected value
     * @return void
     */
    public function testFetch(array $data, $expected): void
    {
        $response = $this->HistoryComponent->fetch($this->documentId, $data);
        $actual = $response['data'][0]['meta']['changed'];
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testFormatResponseData` method
     *
     * @return array
     */
    public function formatResponseDataProvider(): array
    {
        return [
            'empty response data' => [
                [['data' => []], ['something']],
                ['data' => []],
            ],
            'empty schema' => [
                [['data' => ['something']], []],
                ['data' => ['something']],
            ],
            'some history' => [
                [
                    [
                        'data' => [
                            [
                                'meta' => [
                                    'changed' => [
                                        'lang' => 'en',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'properties' => [
                            'lang' => [
                                'oneOf' => [
                                    ['type' => null],
                                    ['type' => 'string'],
                                ],
                                '$id' => '/properties/lang',
                                'title' => 'Lang',
                                'description' => 'language tag, RFC 5646',
                            ],
                        ],
                    ],
                ],
                [
                    'data' => [
                        [
                            'meta' => [
                                'changed' => [
                                    'lang' => '<div class="history-field"><label>Lang</label>en</div>',
                                ],
                                'application_name' => '',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `formatResponseData` method
     *
     * @covers ::formatResponseData()
     * @dataProvider formatResponseDataProvider()
     * @param array $data The data for test
     * @param mixed $expected The expected value
     * @return void
     */
    public function testFormatResponseData(array $data, $expected): void
    {
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest();
        $test->invokeMethod($this->HistoryComponent, 'formatResponseData', [&$data[0], $data[1]]);
        $actual = $data[0];
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testContent`.
     *
     * @return array
     */
    public function contentDataProvider(): array
    {
        return [
            'empty content' => [
                '',
                [],
                null,
                '-',
            ],
            'title' => [
                'title',
                [
                    'properties' => [
                        'title' => [
                            'oneOf' => [
                                ['type' => null],
                                ['type' => 'string'],
                            ],
                            '$id' => '/properties/title',
                            'title' => 'Title',
                        ],
                    ],
                ],
                'Dummy',
                'Dummy',
            ],
            'date_ranges' => [
                'date_ranges',
                [],
                [
                    ['start_date' => '2020-10-14 12:45:00'], // start date only
                    ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57'], // start + end date
                    ['start_date' => '2020-10-14 14:15:22', 'end_date' => '2020-10-15 08:42:57', 'params' => ['all_day' => true]], // all day
                ],
                '<date-ranges-list inline-template><div class="index-date-ranges" :class="show-all"><div><div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020&nbsp;12:45</span></div></div><div class="date-range"><div class="date-item">from<span class="date">14 Oct 2020&nbsp;14:15</span></div><div class="date-item">to&nbsp;<span class="date">15 Oct 2020&nbsp;08:42</span></div></div><div class="date-range"><div class="date-item">on<span class="date">14 Oct 2020</span></div></div></div></date-ranges-list>',
            ],
            'categories' => [
                'categories',
                [
                    'categories' => [
                        ['name' => 'red', 'label' => 'Red'],
                        ['name' => 'green', 'label' => 'Green'],
                        ['name' => 'blue', 'label' => 'Blue'],
                    ],
                ],
                [
                  ['name' => 'green'],
                ],
                '<div class="categories"><h3>Global</h3><div class="input select"><input type="hidden" name="categories" id="categories" value=""/><div class="checkbox"><label for="categories-red"><input type="checkbox" name="categories[]" value="red" id="categories-red">Red</label></div><div class="checkbox"><label for="categories-green" class="selected"><input type="checkbox" name="categories[]" value="green" checked="checked" id="categories-green">Green</label></div><div class="checkbox"><label for="categories-blue"><input type="checkbox" name="categories[]" value="blue" id="categories-blue">Blue</label></div></div></div>',
            ],
        ];
    }

    /**
     * Test `content`
     *
     * @param string $field The field
     * @param array $schema The schema
     * @param mixed $value The value
     * @param string $expected The expected content
     * @return void
     * @covers ::content()
     * @dataProvider contentDataProvider()
     */
    public function testContent(string $field, array $schema, $value, string $expected): void
    {
        $actual = $this->HistoryComponent->content($field, $schema, $value);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testLabel`.
     *
     * @return array
     */
    public function labelDataProvider(): array
    {
        return [
            'empty label' => [
                '',
                '',
            ],
            'date ranges / calendar' => [
                'date_ranges',
                __('Calendar'),
            ],
            'title' => [
                'title',
                __('Title'),
            ],
        ];
    }

    /**
     * Test `label`
     *
     * @param string $field The field
     * @param string $expected The expected label
     * @return void
     * @covers ::label()
     * @dataProvider labelDataProvider()
     */
    public function testLabel(string $field, string $expected): void
    {
        $actual = $this->HistoryComponent->label($field);
        static::assertSame($expected, $actual);
    }
}
