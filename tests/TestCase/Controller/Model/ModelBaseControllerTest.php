<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\ModelBaseController;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\Model\ModelBaseController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\ModelBaseController
 */
class ModelBaseControllerTest extends TestCase
{
    public $ModelController;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'object_types',
        ],
    ];

    /**
     * API client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    protected $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();

        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $this->ModelController = new class ($request) extends ModelBaseController
        {
            protected $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };

        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }

    /**
     * Data provider for `testBeforeFilter` test case.
     *
     * @return array
     */
    public function beforeFilterProvider(): array
    {
        return [
            'not authorized' => [
                new UnauthorizedException(__('Module access not authorized')),
                [
                    'username' => 'dummy',
                    'roles' => [ 'useless' ],
                    'tokens' => [],
                ],
            ],
            'authorized' => [
                null,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                    'tokens' => [],
                ],
            ],
            'expired' => [
                Response::class,
                [
                    'username' => 'bedita',
                    'roles' => [ 'admin' ],
                ],
            ],
        ];
    }

    /**
     * Test `beforeFilter` method
     *
     * @param \Exception|string|null $expected Expected result
     * @param array $data setup data for test
     * @covers ::beforeFilter()
     * @dataProvider beforeFilterProvider()
     * @return void
     */
    public function testBeforeFilter($expected, array $data): void
    {
        // Mock Authentication component
        $this->ModelController->setRequest($this->ModelController->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }
        $this->ModelController->Authentication->setIdentity(new Identity($data));

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $this->ModelController->dispatchEvent('Controller.beforeFilter');
        $result = $this->ModelController->beforeFilter($event);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        } else {
            static::assertNull($result);
        }
    }

    /**
     * Test `beforeRender` method
     *
     * @covers ::beforeRender()
     * @return void
     */
    public function testBeforeRender(): void
    {
        $this->ModelController->dispatchEvent('Controller.beforeRender');

        static::assertNotEmpty($this->ModelController->viewBuilder()->getVar('resourceType'));
        static::assertNotEmpty($this->ModelController->viewBuilder()->getVar('moduleLink'));
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     * @covers ::indexQuery()
     * @covers ::initialize()
     * @covers ::beforeFilter()
     * @return void
     */
    public function testIndex(): void
    {
        $this->ModelController->index();
        $vars = ['resources', 'meta', 'links', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `index` failure method
     *
     * @covers ::index()
     * @return void
     */
    public function testIndexFail(): void
    {
        $this->ModelController->setResourceType('elements');
        $result = $this->ModelController->index();
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     * @return void
     */
    public function testView(): void
    {
        $this->ModelController->view(1);
        $vars = ['resource', 'schema', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `view` failure method
     *
     * @covers ::view()
     * @return void
     */
    public function testViewFail(): void
    {
        $result = $this->ModelController->view(0);
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Data provider for `testSave`
     *
     * @return array
     */
    public function saveProvider(): array
    {
        return [
            'post' => [
                '/model/object_types',
                [
                    'name' => 'animals',
                    'singular' => 'animal',
                ],
                false,
            ],
            'patch' => [
                '/model/object_types/view/1',
                [
                    'id' => '1',
                    'description' => 'new description',
                ],
                true,
            ],
        ];
    }

    /**
     * Test `save` method
     *
     * @param string $expected Expected result
     * @param array $data Request data
     * @param bool $singleView Single view
     * @covers ::save()
     * @dataProvider saveProvider()
     * @return void
     */
    public function testSave(string $expected, array $data, bool $singleView): void
    {
        $this->ModelController->setSingleView($singleView);
        foreach ($data as $name => $value) {
            $this->ModelController->setRequest($this->ModelController->getRequest()->withData($name, $value));
        }
        $result = $this->ModelController->save();

        static::assertInstanceOf(Response::class, $result);
        $location = $result->getHeaderLine('Location');
        static::assertStringEndsWith($expected, $location);
    }

    /**
     * Test `save` failure method
     *
     * @covers ::save()
     * @return void
     */
    public function testSaveFail(): void
    {
        $data = [
            'id' => 99999,
        ];
        foreach ($data as $name => $value) {
            $this->ModelController->setRequest($this->ModelController->getRequest()->withData($name, $value));
        }
        $result = $this->ModelController->save();
        static::assertInstanceOf(Response::class, $result);
        $flash = $this->ModelController->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $flash);
    }

    /**
     * Test `remove` method
     *
     * @covers ::remove()
     * @return void
     */
    public function testRemove(): void
    {
        $data = [
            'name' => 'foos',
            'singular' => 'foo',
        ];
        foreach ($data as $name => $value) {
            $this->ModelController->setRequest($this->ModelController->getRequest()->withData($name, $value));
        }
        $result = $this->ModelController->save();
        static::assertInstanceOf(Response::class, $result);

        $result = $this->ModelController->remove('foos');
        static::assertInstanceOf(Response::class, $result);
    }

    /**
     * Test `remove` failure method
     *
     * @covers ::remove()
     * @return void
     */
    public function testRemoveFail(): void
    {
        $result = $this->ModelController->remove(99999);
        static::assertInstanceOf(Response::class, $result);
        $flash = $this->ModelController->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $flash);
    }

    /**
     * Test `create` method
     *
     * @covers ::create()
     * @return void
     */
    public function testCreate(): void
    {
        $this->ModelController->create();
        $vars = ['resource', 'schema', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->ModelController->viewBuilder()->getVar($var));
        }
    }
}
