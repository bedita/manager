<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ModulesComponent;
use App\Model\API\BEditaClient;
use App\Model\API\BEditaClientException;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\Component\ModulesComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ModulesComponent
 */
class ModulesComponentTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Controller\Component\ModulesComponent
     */
    public $Modules;

    /**
     * {@inheritDoc}
     */
    public function setUp() : void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Auth');
        $this->Modules = $registry->load(ModulesComponent::class);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown() : void
    {
        unset($this->Modules);

        parent::tearDown();
    }

    /**
     * Data provider for `testGetModuleByName` test case.
     *
     * @return array
     */
    public function getModuleByNameProvider() : array
    {
        return [
            'found' => [
                [
                    'name' => 'gustavo',
                ],
                [
                    [
                        'name' => 'gustavo',
                    ],
                    [
                        'whereTheModulesHaveNoName' => 'supporto',
                    ],
                ],
                'gustavo',
            ],
            'not found' => [
                null,
                [
                    [
                        'name' => 'gustavo',
                    ],
                    [
                        'whereTheModulesHaveNoName' => 'supporto',
                    ],
                ],
                'bedita',
            ],
        ];
    }

    /**
     * Test `getModuleByName()` method.
     *
     * @param array|null $expected Expected result.
     * @param array $modules List of modules.
     * @param string $name Name of module to get.
     * @return void
     *
     * @dataProvider getModuleByNameProvider()
     * @covers ::getModuleByName()
     */
    public function testGetModuleByName($expected, array $modules, string $name) : void
    {
        $actual = $this->Modules->getModuleByName($modules, $name);

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testGetProject` test case.
     *
     * @return array
     */
    public function getProjectProvider()
    {
        return [
            'ok' => [
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                    'colophon' => '',
                ],
                [
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
            ],
            'empty' => [
                [
                    'name' => '',
                    'version' => '',
                    'colophon' => '',
                ],
                [],
            ],
            'client exception' => [
                [
                    'name' => '',
                    'version' => '',
                    'colophon' => '',
                ],
                new BEditaClientException('I am a client exception'),
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
            ],
        ];
    }

    /**
     * Test `getProject()` method.
     *
     * @param array|\Exception $expected Expected result.
     * @param array|\Exception $meta Response to `/home` endpoint.
     * @return void
     *
     * @dataProvider getProjectProvider()
     * @covers ::getMeta()
     * @covers ::getProject()
     */
    public function testGetProject($expected, $meta) : void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($meta instanceof \Exception) {
            $apiClient->method('get')
                ->with('/home')
                ->willThrowException($meta);
        } else {
            $apiClient->method('get')
                ->with('/home')
                ->willReturn(compact('meta'));
        }
        $this->Modules->setConfig(compact('apiClient'));

        $actual = $this->Modules->getProject();

        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testGetModules` test case.
     *
     * @return array
     */
    public function getModulesProvider() : array
    {
        return [
            'ok' => [
                [
                    'bedita',
                    'supporto',
                    'gustavo',
                    'trash',
                    'plugin',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'bedita',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'trash',
                        ],
                    ],
                ],
                [
                    'bedita',
                    'supporto',
                ],
                [
                    [
                        'name' => 'plugin',
                    ],
                ],
            ],
            'ok (trash first)' => [
                [
                    'trash',
                    'supporto',
                    'bedita',
                    'gustavo',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'bedita',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'trash',
                        ],
                    ],
                ],
                [
                    'trash',
                    'supporto',
                ],
            ],
            'client exception' => [
                [],
                new BEditaClientException('I am a client exception'),
            ],
            'other exception' => [
                new \RuntimeException('I am some other kind of exception', 999),
                new \RuntimeException('I am some other kind of exception', 999),
            ],
        ];
    }

    /**
     * Test `getModules()` method.
     *
     * @param string[]|\Exception $expected Expected result.
     * @param array|\Exception $meta Response to `/home` endpoint.
     * @param string[] $order Configured modules order.
     * @return void
     *
     * @dataProvider getModulesProvider()
     * @covers ::getMeta()
     * @covers ::getModules()
     */
    public function testGetModules($expected, $meta, array $order = [], array $plugins = []) : void
    {
        Configure::write('Modules.order', $order);
        Configure::write('Modules.plugins', $plugins);

        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        if ($meta instanceof \Exception) {
            $apiClient->method('get')
                ->with('/home')
                ->willThrowException($meta);
        } else {
            $apiClient->method('get')
                ->with('/home')
                ->willReturn(compact('meta'));
        }
        $this->Modules->setConfig(compact('apiClient'));

        $actual = Hash::extract($this->Modules->getModules(), '{*}.name');

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testBeforeRender` test case.
     *
     * @return array
     */
    public function beforeRenderProvider() : array
    {
        return [
            'without current module' => [
                [
                    'gustavo',
                    'supporto',
                ],
                null,
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                    'colophon' => '',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                    ],
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'gustavo',
                ],
            ],
            'with current module' => [
                [
                    'gustavo',
                    'supporto',
                ],
                'supporto',
                [
                    'name' => 'BEdita',
                    'version' => 'v4.0.0-gustavo',
                    'colophon' => '',
                ],
                [
                    'resources' => [
                        [
                            'name' => 'supporto',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                        [
                            'name' => 'gustavo',
                            'hints' => [
                                'object_type' => true,
                            ],
                        ],
                    ],
                    'project' => [
                        'name' => 'BEdita',
                    ],
                    'version' => 'v4.0.0-gustavo',
                ],
                [
                    'gustavo',
                ],
                'supporto',
            ],
        ];
    }

    /**
     * Test `beforeRender()` method.
     *
     * @param string[] $modules Expected module names.
     * @param string|null $currentModule Expected current module name.
     * @param array $project Expected project info.
     * @param array $meta Response to `/home` endpoint.
     * @param string[] $order Configured modules order.
     * @param string|null $currentModuleName Current module.
     * @return void
     *
     * @dataProvider beforeRenderProvider()
     * @covers ::beforeRender()
     */
    public function testBeforeRender($modules, ?string $currentModule, array $project, array $meta, array $order = [], ?string $currentModuleName = null) : void
    {
        Configure::write('Modules.order', $order);

        // Setup mock API client.
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $apiClient->method('get')
            ->with('/home')
            ->willReturn(compact('meta'));

        $this->Modules->setConfig(compact('apiClient', 'currentModuleName'));

        $controller = $this->Modules->getController();
        $controller->dispatchEvent('Controller.beforeRender');

        $viewVars = $controller->viewVars;
        static::assertArrayHasKey('project', $viewVars);
        static::assertEquals($project, $viewVars['project']);
        static::assertArrayHasKey('modules', $viewVars);
        static::assertSame($modules, Hash::extract($viewVars['modules'], '{*}.name'));
        if ($currentModule !== null) {
            static::assertArrayHasKey('currentModule', $viewVars);
            static::assertSame($currentModule, Hash::get($viewVars['currentModule'], 'name'));
        } else {
            static::assertArrayNotHasKey('currentModule', $viewVars);
        }
    }
}
