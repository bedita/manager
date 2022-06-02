<?php
namespace App\Test\TestCase\Controller;

use App\Controller\DownloadController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Client\Adapter\Stream;
use Cake\Http\Client\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\DownloadController} Test Case
 *
 * @coversDefaultClass \App\Controller\DownloadController
 * @uses \App\Controller\DownloadController
 */
class DownloadControllerTest extends TestCase
{
    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $apiClient = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->apiClient = ApiClientProvider::getApiClient();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * Test download from URL
     *
     * @return void
     * @covers ::download()
     * @covers ::content()
     */
    public function testDownloadUrl(): void
    {
        $filePath = sprintf('%s/tests/files/test.png', getcwd());
        $data = [
            'attributes' => [
                'file_name' => 'myfile.jpg',
                'mime_type' => 'image/png',
            ],
            'meta' => [
                'private_url' => false,
                'url' => $filePath,
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->willReturn(compact('data'));
        ApiClientProvider::setApiClient($apiClient);

        $uuid = '123';
        $controller = new DownloadController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'id' => $uuid,
                ],
            ])
        );

        $response = $controller->download($uuid);

        static::assertEquals(200, $response->getStatusCode());
        $content = (string)$response->getBody();
        $hash = hash('md5', $content);
        $expected = hash('md5', file_get_contents($filePath));
        static::assertEquals($expected, $hash);
    }

    /**
     * Test download from stream
     *
     * @return void
     * @covers ::download()
     * @covers ::content()
     * @covers ::streamDownload()
     */
    public function testDownloadStream(): void
    {
        $data = [
            'id' => '123',
            'attributes' => [
                'file_name' => 'myfile.jpg',
                'mime_type' => 'image/png',
            ],
            'meta' => [
                'url' => null,
            ],
        ];

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();

        $apiClient->method('get')
            ->willReturn(compact('data'));
        $apiClient->method('getApiBaseUrl')
            ->willReturn('https://api.example.org');
        ApiClientProvider::setApiClient($apiClient);

        $uuid = '123';
        $controller = new DownloadController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'id' => $uuid,
                ],
            ])
        );

        $response = new Response([], 'test');

        $mock = $this->getMockBuilder(Stream::class)
            ->getMock();
        $mock->method('send')
            ->willReturn([$response]);

        $controller->setConfig('client', ['adapter' => $mock]);
        $response = $controller->download($uuid);

        static::assertEquals(200, $response->getStatusCode());
        $content = (string)$response->getBody();
        static::assertEquals('test', $content);
    }
}
