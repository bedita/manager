<?php
namespace App\Test\TestCase\Controller;

use App\Controller\CourtesyPageController;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\CourtesyPageController} Test Case
 *
 * @coversDefaultClass \App\Controller\CourtesyPageController
 * @uses \App\Controller\CourtesyPageController
 */
class CourtesyPageControllerTest extends TestCase
{
    /**
     * Test `index` method
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $expected = 'this page is under work';
        Configure::write('Maintenance', ['message' => $expected]);
        $config = [
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ];
        $request = new ServerRequest($config);
        $controller = new CourtesyPageController($request);
        $controller->index();
        $actual = $controller->viewBuilder()->getVar('message');
        static::assertEquals($expected, $actual);
    }
}
