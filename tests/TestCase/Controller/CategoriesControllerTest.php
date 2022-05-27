<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CategoriesController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\CategoriesController} Test Case
 *
 * @coversDefaultClass \App\Controller\CategoriesController
 * @uses \App\Controller\CategoriesController
 */
class CategoriesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\CategoriesController
     */
    protected $CategoriesController;

    /**
     * Test `initialize`
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $this->CategoriesController = new CategoriesController(new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]));
        static::assertNotEmpty($this->CategoriesController->{'Categories'});
        static::assertNotEmpty($this->CategoriesController->{'Properties'});
    }

    /**
     * Test `index`
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->CategoriesController = new CategoriesController(new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]));
        $result = $this->CategoriesController->index();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->CategoriesController->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->CategoriesController->getResponse()->getType());

        // verify expected vars in view
        $expected = ['resources', 'roots', 'categoriesTree', 'names', 'meta', 'links', 'schema', 'properties', 'filter', 'object_types', 'objectType'];
        $this->assertExpectedViewVars($expected);
    }

    /**
     * Test `save`
     *
     * @return void
     * @covers ::save()
     */
    public function testSave(): void
    {
        // Setup controller for test
        $this->CategoriesController = new CategoriesController(new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]));

        $result = $this->CategoriesController->save();

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->CategoriesController->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->CategoriesController->getResponse()->getType());
    }

    /**
     * Test `delete`
     *
     * @return void
     * @covers ::delete()
     */
    public function testDelete(): void
    {
        // Setup controller for test
        $this->CategoriesController = new CategoriesController(new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'POST',
            ],
            'get' => [],
            'params' => [
                'object_type' => 'documents',
            ],
        ]));

        $result = $this->CategoriesController->delete('999');

        // verify response status code and type
        static::assertNull($result);
        static::assertEquals(200, $this->CategoriesController->getResponse()->getStatusCode());
        static::assertEquals('text/html', $this->CategoriesController->getResponse()->getType());
    }

    /**
     * Verify existence of vars in controller view
     *
     * @param array $expected The expected vars in view
     * @return void
     */
    private function assertExpectedViewVars($expected): void
    {
        foreach ($expected as $varName) {
            static::assertArrayHasKey($varName, $this->CategoriesController->viewBuilder()->getVars());
        }
    }
}
