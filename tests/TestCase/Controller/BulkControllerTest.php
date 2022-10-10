<?php
namespace App\Test\TestCase\Controller;

use App\Controller\BulkController;
use App\Controller\Component\SchemaComponent;
use App\Utility\CacheTools;
use Authentication\AuthenticationServiceInterface;
use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use ReflectionProperty;

/**
 * {@see \App\Controller\BulkController} Test Case
 *
 * @coversDefaultClass \App\Controller\BulkController
 * @uses \App\Controller\BulkController
 */
class BulkControllerTest extends BaseControllerTest
{
    /**
     * Test Modules controller
     *
     * @var \App\Controller\BulkController
     */
    public $controller;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        Cache::enable();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Setup controller to test with request config
     *
     * @param array|null $requestConfig
     * @return void
     */
    protected function setupController(?array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->controller = new BulkController($request);
        // Mock Authentication component
        $this->controller->setRequest($this->controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));
        $this->controller->Authentication->setIdentity(new Identity(['id' => 'dummy']));
        // Mock GET /config using cache
        Cache::write(CacheTools::cacheKey('config.AlertMessage'), []);
        Cache::write(CacheTools::cacheKey('config.Export'), []);
        Cache::write(CacheTools::cacheKey('config.Modules'), []);
        Cache::write(CacheTools::cacheKey('config.Pagination'), []);
        Cache::write(CacheTools::cacheKey('config.Properties'), []);
        Cache::write(CacheTools::cacheKey('config.Project'), []);

        // force modules load
        $this->controller->Modules->startup();
        $this->setupApi();
        $this->createTestObject();
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
     * Test `initialize` method
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        // Setup controller for test
        $this->setupController();
        $actual = (string)$this->controller->getRequest()->getParam('object_type');
        $expected = 'documents';
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `attribute` method
     *
     * @return void
     * @covers ::attribute()
     */
    public function testAttribute(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();

        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => $o['id'],
                'attributes' => [
                    'status' => $o['attributes']['status'],
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->attribute();

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `categories` method
     *
     * @return void
     * @covers ::categories()
     */
    public function testCategories(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();

        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => $o['id'],
                'attributes' => [
                    'categories' => [],
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->categories();

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `remapCategories` method
     *
     * @return void
     * @covers ::remapCategories()
     */
    public function testRemapCategories(): void
    {
        // Setup controller for test
        $this->setupController();

        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('remapCategories');
        $method->setAccessible(true);
        $input = ['Category 1', 'Category 2'];
        $actual = $method->invokeArgs($this->controller, [$input]);
        $expected = [
            ['name' => 'Category 1'],
            ['name' => 'Category 2'],
        ];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `position` method
     *
     * @return void
     * @covers ::position()
     */
    public function testPosition(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();

        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => $o['id'],
                'folderSelected' => '999',
                'action' => 'copy',
                'attributes' => [
                    'position' => [],
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->position();

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());

        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => $o['id'],
                'folderSelected' => '999',
                'action' => 'move',
                'attributes' => [
                    'position' => [],
                ],
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->position();

        // verify response status code and type
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals('text/html', $result->getType());
    }

    /**
     * Test `saveAttribute` method
     *
     * @return void
     * @covers ::saveAttribute()
     */
    public function testSaveAttribute(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        // set $this->controller->ids
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, [$o['id']]);
        $attributes = ['status' => 'on'];

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('saveAttribute');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, [$attributes]);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // do controller call
        // set $this->controller->ids
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, ['123456789']);
        $method->invokeArgs($this->controller, [$attributes]);

        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
    }

    /**
     * Test `loadCategories` method
     *
     * @return void
     * @covers ::loadCategories()
     */
    public function testLoadCategories(): void
    {
        // Setup controller for test
        $this->setupController();

        // set $this->controller->categories
        $property = new \ReflectionProperty(BulkController::class, 'categories');
        $property->setAccessible(true);
        $property->setValue($this->controller, '123,456,789');

        // mock schema component
        $mockResponse = [
            'categories' => [
                ['id' => '123', 'name' => 'Cat 1'],
                ['id' => '456', 'name' => 'Cat 2'],
                ['id' => '789', 'name' => 'Cat 3'],
                ['id' => '999', 'name' => 'Cat 4'],
            ],
        ];
        $this->controller->Schema = $this->createMock(SchemaComponent::class);
        $this->controller->Schema->method('getSchema')
            ->with('documents')
            ->willReturn($mockResponse);

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('loadCategories');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);
        $property = new ReflectionProperty($this->controller, 'categories');
        $property->setAccessible(true);
        $expected = ['Cat 1', 'Cat 2', 'Cat 3'];
        $actual = $property->getValue($this->controller);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `saveCategories` method
     *
     * @return void
     * @covers ::saveCategories()
     */
    public function testSaveCategories(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, [$o['id']]);

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('saveCategories');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // do controller call
        // set $this->controller->ids
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, ['123456789']);
        $method->invokeArgs($this->controller, []);

        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
    }

    /**
     * Test `copyToPosition` method
     *
     * @return void
     * @covers ::copyToPosition()
     */
    public function testCopyToPosition(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        // set $this->controller->ids
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, [$o['id']]);

        // get folder for test
        $f = $this->createTestFolder();

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('copyToPosition');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, [$f['id']]);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // do controller call
        $method->invokeArgs($this->controller, ['123456789']);

        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
    }

    /**
     * Test `moveToPosition` method
     *
     * @return void
     * @covers ::moveToPosition()
     */
    public function testMoveToPosition(): void
    {
        // Setup controller for test
        $this->setupController();

        // get object for test
        $o = $this->getTestObject();
        // set $this->controller->ids
        $property = new \ReflectionProperty(BulkController::class, 'ids');
        $property->setAccessible(true);
        $property->setValue($this->controller, [$o['id']]);

        // get folder for test
        $f = $this->createTestFolder();

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('moveToPosition');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, [$f['id']]);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // do controller call
        $method->invokeArgs($this->controller, ['123456789']);

        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
    }

    /**
     * Test `custom` method with missing custom action
     *
     * @return void
     * @covers ::custom()
     * @covers ::performCustomAction
     * @covers ::modulesListRedirect()
     */
    public function testCustomMissing(): void
    {
        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => '999',
                'custom_action' => 'undefinedAction',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $result = $this->controller->custom();
        static::assertEquals(302, $result->getStatusCode());
        static::assertEquals(['/documents'], $result->getHeader('Location'));
        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
    }

    /**
     * Test `custom` method with custom action
     *
     * @return void
     * @covers ::custom()
     * @covers ::performCustomAction
     */
    public function testCustomAction(): void
    {
        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => '999',
                'custom_action' => CustomBulkAction::class,
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);

        // do controller call
        $this->controller->custom();
        static::assertEmpty($this->controller->getErrors());
    }

    /**
     * Test `custom` method with bad custom action class
     *
     * @return void
     * @covers ::custom()
     * @covers ::performCustomAction
     */
    public function testCustomWrong(): void
    {
        // Setup again for test
        $this->setupController([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'post' => [
                'ids' => '999',
                'custom_action' => '\App\Utility\Schema',
            ],
        ]);

        // do controller call
        $this->controller->custom();
        static::assertEquals(['Custom action class \App\Utility\Schema is not valid'], $this->controller->getErrors());
    }

    /**
     * Test `errors` method
     *
     * @return void
     * @covers ::showResult()
     */
    public function testShowResult(): void
    {
        // Setup controller for test
        $this->setupController();

        // empty
        // set $this->controller->errors
        $property = new \ReflectionProperty(BulkController::class, 'errors');
        $property->setAccessible(true);
        $property->setValue($this->controller, []);
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('showResult');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);
        $message = $this->controller->getRequest()->getSession()->read('Flash');
        static::assertEquals(1, count($message['flash']));
        static::assertEquals('Bulk action performed on 0 objects', $message['flash'][0]['message']);
        static::assertEquals('flash/success', $message['flash'][0]['element']);

        // not empty
        // set $this->controller->errors
        $property = new \ReflectionProperty(BulkController::class, 'errors');
        $property->setAccessible(true);
        $property->setValue($this->controller, ['something bad happened']);
        $method->invokeArgs($this->controller, []);
        $message = $this->controller->getRequest()->getSession()->read('Flash');
        static::assertEquals(1, count($message['flash']));
        static::assertEquals('Bulk Action failed on: ', $message['flash'][0]['message']);
        static::assertEquals('flash/error', $message['flash'][0]['element']);
    }
}
