<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Model;

use App\Controller\Model\RelationsController;
use BEdita\SDK\BEditaClient;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Model\RelationsController} Test Case
 *
 * @coversDefaultClass \App\Controller\Model\RelationsController
 * @uses \App\Controller\Model\RelationsController
 */
class RelationsControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Model\RelationsController
     */
    public $Relations;

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'params' => [
            'resource_type' => 'relations',
        ],
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Relations = new RelationsController(
            new ServerRequest($this->defaultRequestConfig)
        );
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.com'])
            ->getMock();
        $response = [
            'data' => [['id' => 999, 'attributes' => ['name' => 'dummy']]],
            'meta' => [],
            'links' => [],
        ];
        $apiClient->method('get')
            ->willReturn($response);
        $this->Relations->apiClient = $apiClient;
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Relations);

        parent::tearDown();
    }

    /**
     * Test `index` method
     *
     * @covers ::index()
     * @return void
     */
    public function testIndex(): void
    {
        $this->Relations->index();
        $actual = $this->Relations->viewVars['resources'];
        static::assertTrue(is_array($actual));
    }

    /**
     * Test `view` method
     *
     * @covers ::view()
     * @return void
     */
    public function testView(): void
    {
        $this->Relations->view(1);
        foreach (['left', 'right'] as $side) {
            $actual = $this->Relations->viewVars[sprintf('%s_object_types', $side)];
            static::assertTrue(is_array($actual));
        }
    }

    /**
     * Test `relatedTypes` method
     *
     * @covers ::relatedTypes()
     * @return void
     */
    public function testRelatedTypes(): void
    {
        foreach (['left', 'right'] as $side) {
            $actual = $this->Relations->relatedTypes(1, $side);
            static::assertTrue(is_array($actual));
        }
    }
}
