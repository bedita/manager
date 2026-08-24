<?php

namespace App\Test\TestCase\Controller;

use App\Controller\SendMailController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\SendMailController} Test Case
 */
#[CoversClass(SendMailController::class)]
#[CoversMethod(SendMailController::class, 'index')]
#[CoversMethod(SendMailController::class, 'initialize')]
class SendMailControllerTest extends TestCase
{
    /**
     * Test `index` method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'data' => [
                    'user.name' => 'John',
                    'user.surname' => 'Doe',
                ],
            ],
        ];
        $request = new ServerRequest($config);
        $controller = new SendMailController($request);
        $controller->index();
        $expected = '[404] Not Found';
        $actual = $controller->viewBuilder()->getVar('error');
        static::assertEquals($expected, $actual);

        // mock /placeholders/send to simulate success response
        $safeClient = ApiClientProvider::getApiClient();
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('post')
            ->willReturn(null);
        ApiClientProvider::setApiClient($apiClient);
        $controller->index();
        $expected = 'Email sent successfully';
        $actual = $controller->viewBuilder()->getVar('response')['message'];
        static::assertEquals($expected, $actual);
        ApiClientProvider::setApiClient($safeClient);
    }
}
