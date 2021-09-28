<?php
namespace App\Test\TestCase\Controller;

use App\Controller\BulkController;
use BEdita\SDK\BEditaClientException;
use Cake\Http\ServerRequest;

/**
 * {@see \App\Controller\BulkController} Test Case
 *
 * @coversDefaultClass \App\Controller\BulkController
 */
class BulkControllerTest extends BaseControllerTest
{
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
        // force modules load
        $this->controller->Auth->setUser(['id' => 'dummy']);
        $this->controller->Modules->startup();
        $this->setupApi();
        $this->createTestObject();
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
        $actual = (string)$this->controller->request->getParam('object_type');
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
        $this->controller->ids = [$o['id']];
        $attributes = ['status' => 'on'];

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('saveAttribute');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, [$attributes]);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // // do controller call
        $this->controller->ids = ['123456789'];
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

        // get object for test
        $o = $this->getTestObject();
        $this->controller->categories = '123,456,789';

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('loadCategories');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // // do controller call
        $this->controller->categories = '123,456,789';
        $this->controller->objectType = '&'; // this forces an error
        $method->invokeArgs($this->controller, []);

        // check not empty errors
        static::assertNotEmpty($this->controller->getErrors());
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
        $this->controller->ids = [$o['id']];

        // do controller call
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('saveCategories');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);

        // check empty errors
        static::assertEmpty($this->controller->getErrors());

        // do controller call
        $this->controller->ids = ['123456789'];
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
        $this->controller->ids = [$o['id']];

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
        $this->controller->ids = [$o['id']];

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
     * Test `errors` method
     *
     * @return void
     * @covers ::errors()
     */
    public function testErrors(): void
    {
        // Setup controller for test
        $this->setupController();

        // empty
        $this->controller->errors = [];
        $reflectionClass = new \ReflectionClass($this->controller);
        $method = $reflectionClass->getMethod('errors');
        $method->setAccessible(true);
        $method->invokeArgs($this->controller, []);
        $message = $this->controller->request->getSession()->read('Flash');
        static::assertEmpty($message);

        // not empty
        $this->controller->errors = ['something bad happened'];
        $method->invokeArgs($this->controller, []);
        $message = $this->controller->request->getSession()->read('Flash');
        static::assertNotEmpty($message);
    }
}
