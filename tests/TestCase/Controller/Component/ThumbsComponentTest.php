<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\QueryComponent;
use App\Controller\Component\ThumbsComponent;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\ThumbsComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ThumbsComponent
 */
class ThumbsComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ThumbsComponent
     */
    public $Thumbs;

    /**
     * BEdita client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $controller = new Controller();
        $registry = $controller->components();
        /** @var \App\Controller\Component\ThumbsComponent $thumbsComponent */
        $thumbsComponent = $registry->load(ThumbsComponent::class);
        $this->Thumbs = $thumbsComponent;
        $this->client = ApiClientProvider::getApiClient();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Thumbs);
        ApiClientProvider::setApiClient($this->client);

        parent::tearDown();
    }

    /**
     * Data provider for `testUrls` test case.
     *
     * @return array
     */
    public function urlsProvider(): array
    {
        return [
            // test with empty object
            'emptyResponse' => [
                [],
                [],
            ],
            // test with objct without ids
            'responseWithoutIds 1' => [
                ['data' => []],
                ['data' => []],
            ],
            // test with objct without ids
            'responseWithoutIds 2' => [
                ['data' => [
                    'ids' => [],
                ]],
                ['data' => [
                    'ids' => [],
                ]],
            ],
            // correct result
            'correctResponseMock' => [
                [ // expected
                    'data' => [
                        [
                            'id' => '43',
                            'type' => 'images',
                            'meta' =>
                                [
                                    'thumb_url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb1.png',
                                ],
                        ],
                        [
                            'id' => '45',
                            'type' => 'images',
                            'meta' =>
                                [
                                    'thumb_url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb2.png',
                                ],
                        ],
                    ],
                ],
                [ // data
                    'data' => [
                        [
                            'id' => '43',
                            'type' => 'images',
                            'meta' => [],
                        ],
                        [
                            'id' => '45',
                            'type' => 'images',
                            'meta' => [],
                        ],
                    ],
                ],
                [ // mock response for api
                    'meta' => [
                        'thumbnails' => [
                            [
                                'url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb1.png',
                                'id' => 43,
                            ],
                            [
                                'url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb2.png',
                                'id' => 45,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `urls` method
     *
     * @param array $expected The expected result
     * @param array $data The data to process
     * @param ?mixed $mockResponse The mock response, if any
     * @return void
     * @dataProvider urlsProvider()
     * @covers ::urls()
     * @covers ::getThumbs()
     */
    public function testUrls(array $expected, array $data, $mockResponse = null): void
    {
        $controller = new Controller(new ServerRequest([]));
        if (!empty($mockResponse)) {
            $apiClient = $this->getMockBuilder(BEditaClient::class)
                ->setConstructorArgs(['https://media.example.com'])
                ->getMock();
            $apiClient->method('get')
                ->with('/media/thumbs?ids=43%2C45&preset=default')
                ->willReturn($mockResponse);
            ApiClientProvider::setApiClient($apiClient);
        }
        $registry = $controller->components();
        $registry->load(QueryComponent::class);
        $registry->load(ThumbsComponent::class);
        $this->Thumbs->urls($data, $errors);
        static::assertEquals($expected, $data);
    }

    /**
     * Test `urls` method, with errors from thumbnail generation API.
     *
     * @return void
     * @covers ::urls()
     * @covers ::getThumbs()
     */
    public function testUrlsThumbErrors(): void
    {
        $data = [
            'data' => [
                [
                    'id' => '45',
                    'type' => 'images',
                    'meta' => [],
                ],
            ],
        ];
        $expected = [
            'data' => [
                [
                    'id' => '45',
                    'type' => 'images',
                    'meta' =>
                        [
                            'thumb_url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb2.png',
                        ],
                ],
            ],
        ];
        $expectedErrors = ['Corrupted file'];
        $mockApiResponse = [
            'meta' => [
                'thumbnails' => [
                    [
                        'url' => 'https://media.example.com/be4-media-test/test-thumbs/thumb2.png',
                        'acceptable' => false,
                        'message' => 'Corrupted file',
                        'id' => 45,
                    ],
                ],
            ],
        ];

        $controller = new Controller(new ServerRequest([]));
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->with('/media/thumbs?ids=45&preset=default')
            ->willReturn($mockApiResponse);
        ApiClientProvider::setApiClient($apiClient);
        $registry = $controller->components();
        /** @var \App\Controller\Component\ThumbsComponent $thumbsComponent */
        $thumbsComponent = $registry->load(ThumbsComponent::class);
        $this->Thumbs = $thumbsComponent;
        $this->Thumbs->urls($data);
        static::assertEquals($expected, $data);
        $actual = $this->Thumbs->getController()->getRequest()->getSession()->read('Flash.flash.0.params.0');
        static::assertEquals($expectedErrors[0], $actual);
    }

    /**
     * Test `urls` method, exception case
     *
     * @covers ::urls()
     * @covers ::getThumbs()
     * @return void
     */
    public function testUrlsException(): void
    {
        $controller = new Controller(new ServerRequest([]));
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://media.example.com'])
            ->getMock();
        $apiClient->method('get')
            ->willThrowException(new BEditaClientException('test'));
        ApiClientProvider::setApiClient($apiClient);
        // on exception, no changes on data
        $data = [
            'data' => [
                [
                    'id' => '43',
                    'type' => 'images',
                    'attributes' => [
                        'provider_thumbnail' => 'gustavo',
                    ],
                    'meta' => [],
                ],
                [
                    'id' => '45',
                    'type' => 'images',
                    'meta' => [],
                ],
            ],
        ];
        $expected = $data;
        $expected['data'][0]['meta']['thumb_url'] = 'gustavo';
        $registry = $controller->components();
        $registry->load(QueryComponent::class);
        $registry->load(ThumbsComponent::class);
        $this->Thumbs->urls($data, $errors);
        static::assertEquals($expected, $data);
    }
}
