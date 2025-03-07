<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Component\SchemaComponent;
use App\Controller\Model\CategoriesController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Model\CategoriesController} Test Case
 */
#[CoversClass(CategoriesController::class)]
#[CoversMethod(CategoriesController::class, 'index')]
#[CoversMethod(CategoriesController::class, 'initialize')]
class CategoriesControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\CategoriesController
     */
    public $Categories;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'query' => [
            'filter' => ['type' => 'documents'],
        ],
        'params' => [
            'resource_type' => 'categories',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Categories);
        parent::tearDown();
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        /** @var \BEdita\SDK\BEditaClient $apiClient */
        $apiClient = ApiClientProvider::getApiClient();
        $this->client = $apiClient;
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    /**
     * Setup controller to test with request config
     *
     * @param array $requestConfig
     * @return void
     */
    protected function setupController(array $requestConfig = []): void
    {
        $config = array_merge($this->defaultRequestConfig, $requestConfig);
        $request = new ServerRequest($config);
        $this->Categories = new class ($request) extends CategoriesController {
            public SchemaComponent $Schema;
            public function initialize(): void
            {
                $this->Schema = new class (new ComponentRegistry($this)) extends SchemaComponent {
                    public function objectTypesFeatures(): array
                    {
                        return [
                            'categorized' => [
                                'cats',
                                'dogs',
                                'horses',
                            ],
                        ];
                    }
                };
                parent::initialize();
            }
        };
        $this->setupApi();
    }

    /**
     * Test `index` method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->setupController();
        $beditaApiVersion = (string)Hash::get((array)ApiClientProvider::getApiClient()->get('/home'), 'meta.version');
        $this->Categories->viewBuilder()->setVar('project', ['version' => $beditaApiVersion]);
        $this->Categories->index();
        // verify expected vars in view
        $expected = ['resources', 'roots', 'categoriesTree', 'names', 'meta', 'links', 'schema', 'properties', 'filter', 'object_types'];
        $this->assertExpectedViewVars($expected);
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
            static::assertArrayHasKey($varName, $this->Categories->viewBuilder()->getVars());
        }
    }
}
