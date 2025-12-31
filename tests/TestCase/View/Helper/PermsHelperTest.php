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
namespace App\Test\TestCase\View\Helper;

use App\View\Helper\PermsHelper;
use Authentication\Identity;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\View\Helper\ArrayHelper} Test Case
 */
#[CoversClass(PermsHelper::class)]
#[CoversMethod(PermsHelper::class, 'access')]
#[CoversMethod(PermsHelper::class, 'canCreate')]
#[CoversMethod(PermsHelper::class, 'canCreateModules')]
#[CoversMethod(PermsHelper::class, 'canDelete')]
#[CoversMethod(PermsHelper::class, 'canLock')]
#[CoversMethod(PermsHelper::class, 'canRead')]
#[CoversMethod(PermsHelper::class, 'canSave')]
#[CoversMethod(PermsHelper::class, 'canSaveMap')]
#[CoversMethod(PermsHelper::class, 'initialize')]
#[CoversMethod(PermsHelper::class, 'isAllowed')]
#[CoversMethod(PermsHelper::class, 'isLockedByParents')]
#[CoversMethod(PermsHelper::class, 'userIsAdmin')]
#[CoversMethod(PermsHelper::class, 'userIsAllowed')]
#[CoversMethod(PermsHelper::class, 'userRoles')]
class PermsHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\PermsHelper
     */
    public PermsHelper $Perms;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $view = new View(null, null, null, []);
        $view->set('modules', [
            'documents' => [
                'name' => 'documents',
                'hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']],
            ],
            'profiles' => [
                'name' => 'profiles',
                'hints' => ['allow' => ['GET']],
            ],
        ]);
        $view->set('currentModule', [
            'name' => 'profiles',
            'hints' => ['allow' => ['GET']],
        ]);
        $this->Perms = new PermsHelper($view);
        $this->Perms->initialize([]);
    }

    /**
     * Data provider for `testIsAllowed` test case
     *
     * @return array
     */
    public static function isAllowedProvider(): array
    {
        return [
            [
                false,
                'canDelete',
                ['type' => 'documents', 'meta' => ['locked' => true]],
            ],
            [
                true,
                'canRead',
            ],
            [
                true,
                'canCreate',
                'documents',
            ],
            [
                false,
                'canSave',
                'events',
            ],
        ];
    }

    /**
     * Test main `isAllowed` method logic
     *
     * @param bool $expected Expected result
     * @param string $method Helper method
     * @param array|string $arg The argument for function
     * @return void
     */
    #[DataProvider('isAllowedProvider')]
    public function testIsAllowed(bool $expected, string $method, $arg = null): void
    {
        $result = $this->Perms->{$method}($arg);
        static::assertEquals($expected, $result);
    }

    /**
     * Test `isAllowed` method with no current module perms.
     *
     * @return void
     */
    public function testIsAllowedNoCurrent(): void
    {
        $this->Perms->getView()->set('currentModule', null);
        $this->Perms->initialize([]);
        $result = $this->Perms->canSave();
        static::assertTrue($result);
    }

    /**
     * Test `canLock` method.
     *
     * @return void
     */
    public function testCanLock(): void
    {
        $this->Perms->getView()->set('user', new Identity([]));
        $result = $this->Perms->canLock();
        static::assertFalse($result);

        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $result = $this->Perms->canLock();
        static::assertTrue($result);
    }

    /**
     * Test `canCreate` method
     *
     * @return void
     */
    public function testCanCreate(): void
    {
        static::assertFalse($this->Perms->canCreate());
    }

    /**
     * Test `canRead` method
     *
     * @return void
     */
    public function testCanRead(): void
    {
        static::assertTrue($this->Perms->canRead());
    }

    /**
     * Test `canSave` method
     *
     * @return void
     */
    public function testCanSave(): void
    {
        static::assertFalse($this->Perms->canSave());
    }

    /**
     * Test `canSaveMap` method
     *
     * @return void
     */
    public function testCanSaveMap(): void
    {
        $result = $this->Perms->canSaveMap();
        static::assertIsArray($result);
        static::assertArrayHasKey('documents', $result);
        static::assertTrue($result['documents']);
        static::assertArrayHasKey('profiles', $result);
        static::assertFalse($result['profiles']);
    }

    /**
     * Test `canDelete` method
     *
     * @return void
     */
    public function testCanDelete(): void
    {
        $document = [
            'type' => 'documents',
            'meta' => ['locked' => true],
        ];
        static::assertFalse($this->Perms->canDelete($document));

        // locked by parents
        $mock = $this->createPartialMock(PermsHelper::class, ['isLockedByParents']);
        $mock->method('isLockedByParents')->willReturn(true);
        $this->Perms = $mock;
        $document = [
            'type' => 'documents',
            'meta' => ['locked' => false],
        ];
        static::assertFalse($this->Perms->canDelete($document));
    }

    /**
     * Data provider for testAccess.
     *
     * @return array
     */
    public static function accessProvider(): array
    {
        return [
            'write' => [
                [],
                'manager',
                'cats',
                'write',
            ],
            'hidden' => [
                [
                    'manager' => [
                        'hidden' => ['cats', 'dogs', 'horses'],
                    ],
                ],
                'manager',
                'dogs',
                'hidden',
            ],
            'read' => [
                [
                    'manager' => [
                        'hidden' => ['cats', 'dogs'],
                        'readonly' => ['horses'],
                    ],
                ],
                'manager',
                'horses',
                'read',
            ],
        ];
    }

    /**
     * Test `access` method
     *
     * @param array $accessControl The access control
     * @param string $roleName The role name
     * @param string $moduleName The module name
     * @param string $expected The expected value
     * @return void
     */
    #[DataProvider('accessProvider')]
    public function testAccess(array $accessControl, string $roleName, string $moduleName, string $expected): void
    {
        $actual = $this->Perms->access($accessControl, $roleName, $moduleName);
        static::assertSame($expected, $actual);
    }

    /**
     * Test `userIsAdmin`
     *
     * @return void
     */
    public function testUserIsAdmin(): void
    {
        $this->Perms->getView()->set('user', new Identity([]));
        static::assertFalse($this->Perms->userIsAdmin());

        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        static::assertTrue($this->Perms->userIsAdmin());
    }

    /**
     * Test `userIsAllowed
     *
     * @return void
     */
    public function testUserIsAllowed(): void
    {
        // folders do not have association Permissions
        $this->Perms->getView()->set('foldersSchema', ['associations' => []]);
        $this->Perms->initialize([]);
        $this->Perms->getView()->set('object', ['meta' => ['perms' => ['roles' => ['a', 'b', 'c', 'd']]]]);
        $actual = $this->Perms->userIsAllowed(null);
        static::assertTrue($actual);
        $this->Perms->getView()->set('foldersSchema', ['associations' => ['Permissions']]);
        $this->Perms->initialize([]);

        // not folders
        $this->Perms->getView()->set('objectType', 'documents');
        $actual = $this->Perms->userIsAllowed(null);
        static::assertTrue($actual);

        // user admin
        $this->Perms->getView()->set('objectType', 'folders');
        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $actual = $this->Perms->userIsAllowed(null);
        static::assertTrue($actual);

        // empty meta.perms.roles
        $this->Perms->getView()->set('object', ['meta' => []]);
        $this->Perms->getView()->set('user', new Identity(['roles' => ['guest', 'manager', 'other']]));
        $actual = $this->Perms->userIsAllowed(null);
        static::assertTrue($actual);

        // has permission
        $this->Perms->getView()->set('object', ['meta' => ['perms' => ['roles' => ['a', 'b', 'manager', 'c', 'd']]]]);
        $actual = $this->Perms->userIsAllowed(null);
        static::assertTrue($actual);

        // no permission
        $this->Perms->getView()->set('object', ['meta' => ['perms' => ['roles' => ['a', 'b', 'c', 'd']]]]);
        $actual = $this->Perms->userIsAllowed(null);
        static::assertFalse($actual);
    }

    /**
     * Test `userRoles`
     *
     * @return void
     */
    public function testUserRoles(): void
    {
        $expected = ['a', 'b', 'c'];
        $this->Perms->getView()->set('user', new Identity(['roles' => $expected]));
        $actual = $this->Perms->userRoles();
        static::assertSame($expected, $actual);
    }

    /**
     * Test `isLockedByParents` method.
     *
     * @return void
     */
    public function testIsLockedByParents(): void
    {
        $this->Perms->getView()->set('foldersSchema', ['associations' => ['Permissions']]);
        $this->Perms->initialize([]);

        // user is admin => false
        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $actual = $this->Perms->isLockedByParents('123');
        static::assertFalse($actual);

        // user is not admin, no parents => false
        $safeApiClient = ApiClientProvider::getApiClient();
        $this->Perms->getView()->set('user', new Identity(['roles' => ['guest']]));
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Perms->isLockedByParents('123');
        static::assertFalse($actual);

        // user is not admin, has one parent with perms roles different than user's roles => true
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn([
                'included' => [
                    [
                        'id' => '123451',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['a', 'b', 'c'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['d', 'e', 'f'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['g', 'guest', 'i'],
                            ],
                        ],
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Perms->isLockedByParents('123');
        static::assertTrue($actual);

        // user is not admin, has parents, none with perms => true
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn([
                'included' => [
                    [
                        'id' => '123451',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['a', 'b', 'c'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['d', 'e', 'f'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['g', 'h', 'i'],
                            ],
                        ],
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Perms->isLockedByParents('123');
        static::assertTrue($actual);

        // user is not admin, has parents, one with no perms => true
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn([
                'included' => [
                    [
                        'id' => '123451',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['d', 'e', 'f'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['g', 'h', 'i'],
                            ],
                        ],
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Perms->isLockedByParents('123');
        static::assertTrue($actual);

        ApiClientProvider::setApiClient($safeApiClient);

        // user is not admin, has parents, all with perms => false
        $apiClient = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://example.com'])
            ->getMock();
        $apiClient->method('get')
            ->withAnyParameters()
            ->willReturn([
                'included' => [
                    [
                        'id' => '123451',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['a', 'b', 'c', 'guest'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['d', 'guest', 'e', 'f'],
                            ],
                        ],
                    ],
                    [
                        'id' => '123452',
                        'type' => 'folders',
                        'meta' => [
                            'perms' => [
                                'roles' => ['g', 'h', 'guest', 'i'],
                            ],
                        ],
                    ],
                ],
            ]);
        ApiClientProvider::setApiClient($apiClient);
        $actual = $this->Perms->isLockedByParents('123');
        static::assertFalse($actual);
    }

    /**
     * Test `canCreateModules` method
     *
     * @return void
     */
    public function testCanCreateModules(): void
    {
        $expected = ['documents'];
        $actual = $this->Perms->canCreateModules();
        static::assertSame($expected, $actual);
    }
}
