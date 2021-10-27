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
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ApiComponent;
use App\Controller\Component\ModulesComponent;
use Cake\Controller\Component\AuthComponent;
use Cake\Controller\Controller;
use Cake\TestSuite\TestCase;

/**
 * {@see App\Controller\Component\ApiComponent} Test Case
 *
 * @coversDefaultClass App\Controller\Component\ApiComponent
 */
class ApiComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ApiComponent
     */
    public $Api;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Auth');
        $this->Modules = $registry->load(ModulesComponent::class);
        $this->Auth = $registry->load(AuthComponent::class);
        $this->Api = $registry->load(ApiComponent::class);
        $controller->Auth = $this->Auth;
        $controller->Modules = $this->Modules;
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Api);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetObjects` method
     *
     * @return array
     */
    public function getObjectsProvider(): array
    {
        return [
            'objects, empty query' => [
                'objects',
                [],
                ['data', 'links', 'meta'],
            ],
        ];
    }

    /**
     * Test `getObjects()` method.
     *
     * @param string $objectType The object type
     * @param array $query The query
     * @return void
     * @covers ::getObjects()
     * @dataProvider getObjectsProvider()
     */
    public function testGetObjects(string $objectType, array $query, array $expectedKeys): void
    {
        $response = $this->Api->getObjects($objectType, $query);
        foreach ($expectedKeys as $key) {
            static::assertArrayHasKey($key, $response);
        }
    }
}
