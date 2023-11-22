<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\AppearanceController;
use BEdita\WebTools\ApiClientProvider;
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

        $client = ApiClientProvider::getApiClient();
        $response = $client->authenticate(getenv('BEDITA_ADMIN_USR'), getenv('BEDITA_ADMIN_PWD'));
        $client->setupTokens($response['meta']);
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
                        'property_name' => 'properties',
                        'property_value' => '[]',
                    ],
                ]
            )
        );
        $this->Appearance->save();
        $viewVars = (array)$this->Appearance->viewBuilder()->getVars();
        static::assertSame('Configuration saved', $viewVars['response']);

        $client = ApiClientProvider::getApiClient();
        $client->setupTokens([]);
        $this->Appearance->save();
        $viewVars = (array)$this->Appearance->viewBuilder()->getVars();
        static::assertSame('[401] Unauthorized', $viewVars['error']);
    }
}
