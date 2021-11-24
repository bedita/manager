<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\CategoriesComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\CategoriesComponent Test Case
 */
class CategoriesComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\CategoriesComponent
     */
    public $Categories;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->Categories = new CategoriesComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Categories);

        parent::tearDown();
    }

    /**
     * Test initial setup
     *
     * @return void
     */
    public function testInitialization()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
