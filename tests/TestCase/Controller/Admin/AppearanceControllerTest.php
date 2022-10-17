<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AppearanceController;
use BEdita\SDK\BEditaClient;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\AppearanceController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\AppearanceController
 */
class AppearanceControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\AppearanceController
     */
    public $Appearance;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
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
     * Test index
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex(): void
    {
        $this->Appearance = new AppearanceController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ]
            )
        );
        $this->Appearance->index();
        $viewVars = (array)$this->Appearance->viewBuilder()->getVars();
        static::assertNotEmpty($viewVars['configs']['modules']);
        static::assertNotEmpty($viewVars['configs']['pagination']);
        static::assertArrayHasKey('alert_message', $viewVars['configs']);
        static::assertArrayHasKey('export', $viewVars['configs']);
        static::assertArrayHasKey('modules', $viewVars['configs']);
        static::assertArrayHasKey('pagination', $viewVars['configs']);
        static::assertArrayHasKey('project', $viewVars['configs']);
        static::assertArrayHasKey('properties', $viewVars['configs']);
    }

    /**
     * Test save
     *
     * @return void
     * @covers ::save()
     */
    public function testSave(): void
    {
        $this->Appearance = new AppearanceController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'POST',
                    ],
                    'post' => [
                        'Properties' => '[]',
                        'property_name' => 'properties',
                    ],
                ]
            )
        );
        // mock GET /config and /admin/applications.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->will(
                $this->returnCallback(
                    function ($param) {
                        if ($param === '/config') {
                            return [];
                        }
                        if ($param === '/admin/applications') {
                            return [
                                'data' => [['id' => 123456789, 'attributes' => ['name' => 'manager']]],
                            ];
                        }
                    }
                )
            );
        // expect exception on redirect to admin uri, because test does not access admin routes as unauthenticated
        $this->expectException('Cake\Routing\Exception\MissingRouteException');
        $this->Appearance->save();
    }
}
