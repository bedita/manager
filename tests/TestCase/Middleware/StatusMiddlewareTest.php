<?php
declare(strict_types=1);

namespace App\Test\TestCase\Middleware;

use BEdita\WebTools\ApiClientProvider;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\RequestInterface;

/**
 * {@see \App\Middleware\StatusMiddleware} Test Case.
 *
 * @covers \App\Middleware\StatusMiddleware
 */
class StatusMiddlewareTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        ApiClientProvider::setApiClient(null);

        parent::tearDown();
    }

    /**
     * Data provider for {@see StatusMiddlewareTest::testProcess()} test case.
     *
     * @return array[]
     */
    public function processProvider(): array
    {
        $config = function (?int $status): array {
            return [
                'API' => [
                    'apiBaseUrl' => 'https://bedita.example.com',
                    'apiKey' => 'test-api-key',
                    'guzzleConfig' => [
                        'handler' => MockHandler::createWithMiddleware([
                            $status !== null ? new Response($status) : function (RequestInterface $request): void {
                                throw new ConnectException('Connection refused for URI https://bedita.example.com', $request);
                            },
                        ]),
                    ],
                ],
            ];
        };

        return [
            'not status endpoint' => [200, '<title>Login | BEdita 4</title>', '/login'],
            'method not allowed' => [405, 'Method Not Allowed', '/status', 'DELETE'],
            'liveliness probe' => [204, null, '/status', 'GET', $config(503)],
            'api status check (ok)' => [204, null, '/status?full=1', 'GET', $config(204)],
            'api status check (error)' => [503, 'API status returned an error or is unreachable', '/status?full=1', 'GET', $config(503)],
            'api status check (unreachable)' => [503, 'API status returned an error or is unreachable', '/status?full=1', 'GET', $config(null)],
        ];
    }

    /**
     * Test {@see \App\Middleware\StatusMiddleware::process()} method.
     *
     * @param int $expectedStatus Expected response status.
     * @param string|null $expectedBody Expected response body contents, or `null` to expect an empty response.
     * @param string $url Request URL (path and query).
     * @param string $method Request method.
     * @param array $config Configuration.
     * @return void
     * @dataProvider processProvider()
     */
    public function testProcess(int $expectedStatus, ?string $expectedBody, string $url, string $method = 'GET', array $config = []): void
    {
        Configure::write($config);

        $this->_sendRequest($url, $method);

        $this->assertResponseCode($expectedStatus);
        if ($expectedBody === null) {
            $this->assertResponseEmpty();
        } else {
            $this->assertResponseContains($expectedBody);
        }
    }
}
