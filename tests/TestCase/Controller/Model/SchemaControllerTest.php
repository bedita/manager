<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\SchemaController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Model\SchemaController} Test Case
 */
#[CoversClass(SchemaController::class)]
#[CoversMethod(SchemaController::class, 'index')]
class SchemaControllerTest extends TestCase
{
    /**
     * The original API client (not mocked).
     *
     * @var \BEdita\SDK\BEditaClient|null
     */
    protected ?BEditaClient $apiClient = null;

    /**
     * Test subject
     *
     * @var \App\Controller\Model\SchemaController
     */
    public SchemaController $ModelController;

    /**
     * Client API
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public BEditaClient $client;

    /**
     * Test default request config
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->apiClient = ApiClientProvider::getApiClient();
        $this->loadRoutes();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    private function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
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
        $this->ModelController = new SchemaController($request);
        $this->setupApi();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->setupController();
        $result = $this->ModelController->index();
        $this->assertNull($result);
        $this->assertSame('schema', $this->ModelController->viewBuilder()->getVar('resourceType'));
        $this->assertSame(['_name' => 'model:schema'], $this->ModelController->viewBuilder()->getVar('moduleLink'));
    }
}
