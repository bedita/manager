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
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $this->HistoryComponent = $registry->load(HistoryComponent::class);
        $this->SchemaComponent = $registry->load(SchemaComponent::class);
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
     * {@inheritDoc}
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
        $session = $this->HistoryComponent->getController()->request->getSession();
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
                '{"status":"","uname":"","title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
            'test document, keepUname true' => [
                [
                    'objectType' => 'documents',
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => true,
                ],
                '{"status":"","uname":null,"title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
        ];
    }

    /**
     * Test `write` method
     *
     * @covers ::write()
     * @dataProvider writeProvider()
     * @param array $options The options for test
     * @param string $expected The expected serialized data
     * @return void
     */
    public function testWrite(array $options, string $expected): void
    {
        $options += [
            'ApiClient' => $this->ApiClient,
            'Schema' => $this->SchemaComponent,
            'Request' => $this->Request,
        ];
        $this->HistoryComponent->write($options);
        $session = $this->HistoryComponent->getController()->request->getSession();
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
                    $this->documentId,
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
                ],
                [
                    'title' => '<div class="input title text"><label for="title">Title</label><input type="text" name="title" class="title" id="title" value="test history controller"/></div>',
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
        $response = $this->HistoryComponent->fetch($data[0], $data[1]);
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
                                    'lang' => '<div class="input text"><label for="lang">Lang</label><input type="text" name="lang" id="lang" value="en"/></div>',
                                ],
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
        $test = new AppControllerTest(new ServerRequest());
        $test->invokeMethod($this->HistoryComponent, 'formatResponseData', [&$data[0], $data[1]]);
        $actual = $data[0];
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testFieldSchema` method
     *
     * @return array
     */
    public function fieldSchemaProvider(): array
    {
        return [
            'first level' => [
                ['dummy', ['dummy' => ['dummy-expected']]],
                ['dummy-expected'],
            ],
            'title' => [
                ['title', ['properties' => ['title' => ['title-expected']]]],
                ['title-expected'],
            ],
            'relations' => [
                ['relationName', ['relations' => ['relationName' => ['relationName-expected']]]],
                ['relationName-expected'],
            ],
            'associations' => [
                ['associationName', ['associations' => ['associationName' => ['associationName-expected']]]],
                ['associationName-expected'],
            ],
        ];
    }

    /**
     * Test `fieldSchema` method
     *
     * @covers ::fieldSchema()
     * @dataProvider fieldSchemaProvider()
     * @param array $data The data for test
     * @param mixed $expected The expected value
     * @return void
     */
    public function testFieldSchema(array $data, $expected): void
    {
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest(new ServerRequest());
        $actual = $test->invokeMethod($this->HistoryComponent, 'fieldSchema', [$data[0], $data[1]]);
        static::assertEquals($expected, $actual);
    }

    /**
     * Provider for `testField` method
     *
     * @return array
     */
    public function fieldProvider(): array
    {
        return [
            'title' => [
                [
                    'title',
                    'dummy',
                    [
                        'oneOf' => [
                            ['type' => null],
                            ['type' => 'string', 'contentMediaType' => 'text/Html'],
                        ],
                        '$id' => '/properties/title',
                        'title' => 'Title',
                        'description' => '',
                    ],
                ],
                '<div class="input title text"><label for="title">Title</label><input type="text" name="title" class="title" id="title" value="dummy"/></div>',
            ],
            'categories' => [
                [
                    'categories',
                    '',
                    [],
                ],
                '<div class="input select"><label for="categories">Categories</label><input type="hidden" name="categories" value=""/></div>',
            ],
        ];
    }

    /**
     * Test `field` method
     *
     * @covers ::field()
     * @dataProvider fieldProvider()
     * @param array $data The data for test
     * @param string $expected The expected value
     * @return void
     */
    public function testField(array $data, string $expected): void
    {
        // call private method using AppControllerTest->invokeMethod
        $test = new AppControllerTest(new ServerRequest());
        $actual = $test->invokeMethod($this->HistoryComponent, 'field', [$data[0], $data[1], $data[2]]);
        static::assertEquals($expected, $actual);
    }
}
