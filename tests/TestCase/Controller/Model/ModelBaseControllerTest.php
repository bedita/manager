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
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\Model\ModelBaseController} Test Case
 */
#[CoversClass(ModelBaseController::class)]
#[CoversMethod(ModelBaseController::class, 'beforeFilter')]
#[CoversMethod(ModelBaseController::class, 'beforeRender')]
#[CoversMethod(ModelBaseController::class, 'create')]
#[CoversMethod(ModelBaseController::class, 'doSave')]
#[CoversMethod(ModelBaseController::class, 'index')]
#[CoversMethod(ModelBaseController::class, 'indexQuery')]
#[CoversMethod(ModelBaseController::class, 'remove')]
#[CoversMethod(ModelBaseController::class, 'save')]
#[CoversMethod(ModelBaseController::class, 'view')]
class ModelBaseControllerTest extends TestCase
{
    /**
     * Test request config
     *
     * @var array
     */
    public array $defaultRequestConfig = [
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
    protected BEditaClient $client;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();

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
    public static function beforeFilterProvider(): array
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
     * @return void
     */
    #[DataProvider('beforeFilterProvider')]
    public function testBeforeFilter($expected, array $data): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        if (isset($data['tokens'])) {
            $data['tokens'] = $this->client->getTokens();
        }
        $controller->Authentication->setIdentity(new Identity($data));

        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
        }

        $event = $controller->dispatchEvent('Controller.beforeFilter');
        $result = $controller->beforeFilter($event);

        if (is_string($expected)) {
            static::assertInstanceOf($expected, $result);
        } else {
            static::assertNull($result);
        }
    }

    /**
     * Test `beforeRender` method
     *
     * @return void
     */
    public function testBeforeRender(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->dispatchEvent('Controller.beforeRender');
        static::assertNotEmpty($controller->viewBuilder()->getVar('resourceType'));
        static::assertNotEmpty($controller->viewBuilder()->getVar('moduleLink'));
    }

    /**
     * Test `index` method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->index();
        $vars = ['resources', 'meta', 'links', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `index` failure method
     *
     * @return void
     */
    public function testIndexFail(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->setResourceType('elements');
        $result = $controller->index();
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Test `view` method
     *
     * @return void
     */
    public function testView(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->view(1);
        $vars = ['resource', 'schema', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `view` failure method
     *
     * @return void
     */
    public function testViewFail(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $result = $controller->view(0);
        static::assertTrue(($result instanceof Response));
    }

    /**
     * Data provider for `testSave`
     *
     * @return array
     */
    public static function saveProvider(): array
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
     * @return void
     */
    #[DataProvider('saveProvider')]
    public function testSave(string $expected, array $data, bool $singleView): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->setSingleView($singleView);
        foreach ($data as $name => $value) {
            $controller->setRequest($controller->getRequest()->withData($name, $value));
        }
        $result = $controller->save();

        static::assertInstanceOf(Response::class, $result);
        $location = $result->getHeaderLine('Location');
        static::assertStringEndsWith($expected, $location);
    }

    /**
     * Test `save` failure method
     *
     * @return void
     */
    public function testSaveFail(): void
    {
        $data = [
            'id' => 99999,
        ];
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        foreach ($data as $name => $value) {
            $controller->setRequest($controller->getRequest()->withData($name, $value));
        }
        $result = $controller->save();
        static::assertInstanceOf(Response::class, $result);
        $flash = $controller->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $flash);
    }

    /**
     * Test `save` method, on redir
     *
     * @return void
     */
    public function testSaveRedir(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $redirTo = ['_name' => 'model:create:object_types'];
        $controller->setRequest($controller->getRequest()->withData('id', 1));
        $controller->setRequest($controller->getRequest()->withData('description', 'whatever'));
        $controller->setRequest($controller->getRequest()->withData('redirTo', $redirTo));
        $result = $controller->save();
        static::assertInstanceOf(Response::class, $result);
        $location = $result->getHeaderLine('Location');
        $expected = '/view';
        static::assertStringEndsWith($expected, $location);
    }

    /**
     * Test `remove` method
     *
     * @return void
     */
    public function testRemove(): void
    {
        $data = [
            'name' => 'foos',
            'singular' => 'foo',
        ];
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        foreach ($data as $name => $value) {
            $controller->setRequest($controller->getRequest()->withData($name, $value));
        }
        $result = $controller->save();
        static::assertInstanceOf(Response::class, $result);

        $result = $controller->remove('foos');
        static::assertInstanceOf(Response::class, $result);
    }

    /**
     * Test `remove` failure method
     *
     * @return void
     */
    public function testRemoveFail(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $result = $controller->remove('99999');
        static::assertInstanceOf(Response::class, $result);
        $flash = $controller->getRequest()->getSession()->read('Flash.flash.0.message');
        static::assertEquals('[404] Not Found', $flash);
    }

    /**
     * Test `create` method
     *
     * @return void
     */
    public function testCreate(): void
    {
        $config = array_merge($this->defaultRequestConfig, []);
        $request = new ServerRequest($config);
        $controller = new class ($request) extends ModelBaseController
        {
            protected ?string $resourceType = 'object_types';

            public function setResourceType(string $type): void
            {
                $this->resourceType = $type;
            }

            public function setSingleView(bool $view): void
            {
                $this->singleView = $view;
            }
        };
        $controller->create();
        $vars = ['resource', 'schema', 'properties'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
    }
}
