<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CategoriesController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\CategoriesController} Test Case
 */
#[CoversClass(CategoriesController::class)]
#[CoversMethod(CategoriesController::class, 'delete')]
#[CoversMethod(CategoriesController::class, 'index')]
#[CoversMethod(CategoriesController::class, 'initialize')]
#[CoversMethod(CategoriesController::class, 'save')]
class CategoriesControllerTest extends TestCase
{
    /**
     * Test `initialize`
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends CategoriesController {
            public function initialize(): void
            {
                parent::initialize();
                $this->loadComponent('Categories');
            }
        };
        static::assertNotEmpty($controller->{'Categories'});
        static::assertNotEmpty($controller->{'Properties'});
    }

    /**
     * Test `index`
     *
     * @return void
     */
    public function testIndex(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends CategoriesController {
            public function initialize(): void
            {
                parent::initialize();
                $this->loadComponent('Categories');
            }
        };
        $controller->viewBuilder()->setVar('project', ['version' => '5.26.0']);
        $result = $controller->index();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $controller->getResponse()->getType());

        // verify expected vars in view
        $expected = ['resources', 'roots', 'categoriesTree', 'names', 'meta', 'links', 'schema', 'properties', 'filter', 'object_types', 'objectType'];
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $controller->viewBuilder()->getVars());
        }
    }

    /**
     * Test `save`
     *
     * @return void
     */
    public function testSave(): void
    {
        // Setup controller for test
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends CategoriesController {
            public function initialize(): void
            {
                parent::initialize();
                $this->loadComponent('Categories');
            }
        };

        $result = $controller->save();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $controller->getResponse()->getType());
    }

    /**
     * Test `delete`
     *
     * @return void
     */
    public function testDelete(): void
    {
        // Setup controller for test
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends CategoriesController {
            public function initialize(): void
            {
                parent::initialize();
                $this->loadComponent('Categories');
            }
        };

        $result = $controller->delete('999');

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $controller->getResponse()->getStatusCode());
        static::assertEquals('text/html', $controller->getResponse()->getType());
    }
}
