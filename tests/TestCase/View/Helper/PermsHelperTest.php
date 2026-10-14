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
use RuntimeException;

/**
 * {@see \App\View\Helper\PermsHelper} Test Case
 */
#[CoversClass(PermsHelper::class)]
#[CoversMethod(PermsHelper::class, 'access')]
#[CoversMethod(PermsHelper::class, 'canCreate')]
#[CoversMethod(PermsHelper::class, 'canCreateModules')]
#[CoversMethod(PermsHelper::class, 'canDelete')]
#[CoversMethod(PermsHelper::class, 'canDo')]
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
     * Original API client, restored even when an assertion fails.
     *
     * @var \BEdita\SDK\BEditaClient
     */
    private BEditaClient $apiClient;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->apiClient = ApiClientProvider::getApiClient();
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
        $view->set('objectType', 'profiles');
        $view->set('user', new Identity(['id' => 2, 'roles' => ['manager']]));
        $this->Perms = new PermsHelper($view);
        $this->Perms->initialize([]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient($this->apiClient);
        parent::tearDown();
    }

    /**
     * Reinitialization must not retain removed or revoked module methods.
     *
     * @return void
     */
    public function testInitializeClearsAllowedMethods(): void
    {
        static::assertTrue($this->Perms->canSave('documents'));
        $this->Perms->getView()->set('modules', ['documents' => ['name' => 'documents']]);
        $this->Perms->initialize([]);
        static::assertFalse($this->Perms->canSave('documents'));

        $this->Perms->getView()->set('modules', []);
        $this->Perms->initialize([]);
        static::assertFalse($this->Perms->canRead('profiles'));
        static::assertSame([], $this->Perms->canCreateModules());
        static::assertSame([], $this->Perms->canSaveMap());
    }

    /**
     * Each method must be checked independently, including for administrators.
     *
     * @return array<string, array{'GET'|'POST'|'PATCH'|'DELETE', array<string>, bool}>
     */
    public static function moduleMethodsProvider(): array
    {
        $cases = [];
        foreach (['GET', 'POST', 'PATCH', 'DELETE'] as $method) {
            foreach ([false, true] as $admin) {
                $prefix = $method . ($admin ? ' admin ' : ' manager ');
                $cases[$prefix . 'allowed'] = [$method, [$method], $admin];
                $cases[$prefix . 'denied'] = [$method, [], $admin];
                $cases[$prefix . 'other methods'] = [
                    $method,
                    array_values(array_diff(['GET', 'POST', 'PATCH', 'DELETE'], [$method])),
                    $admin,
                ];
            }
        }

        return $cases;
    }

    /**
     * @param 'GET'|'POST'|'PATCH'|'DELETE' $method HTTP method
     * @param array<string> $allowed Allowed methods
     * @param bool $admin Whether user is admin
     * @return void
     */
    #[DataProvider('moduleMethodsProvider')]
    public function testModuleMethods(string $method, array $allowed, bool $admin): void
    {
        $view = $this->Perms->getView();
        $view->set('modules', ['documents' => ['hints' => ['allow' => $allowed]]]);
        $view->set('user', new Identity(['roles' => [$admin ? 'admin' : 'manager']]));
        $this->Perms->initialize([]);
        $expected = in_array($method, $allowed, true);

        static::assertSame($expected, $this->Perms->canDo($method, 'documents'));
        $actual = match ($method) {
            'GET' => $this->Perms->canRead('documents'),
            'POST' => $this->Perms->canCreate('documents'),
            'PATCH' => $this->Perms->canSave('documents'),
            'DELETE' => $this->Perms->canDelete(['type' => 'documents']),
        };
        static::assertSame($expected, $actual);
        static::assertFalse($this->Perms->canDo($method, 'unknown'));
    }

    /**
     * Explicit modules must supply their own method hints.
     *
     * @return void
     */
    public function testExplicitModuleWithoutMethods(): void
    {
        $this->Perms->getView()->set('modules', [
            'missing' => ['name' => 'missing'],
            'empty' => ['hints' => ['allow' => []]],
            'null' => ['hints' => ['allow' => null]],
        ]);
        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $this->Perms->initialize([]);
        foreach (['missing', 'empty', 'null'] as $module) {
            static::assertFalse($this->Perms->canRead($module));
            static::assertFalse($this->Perms->canCreate($module));
            static::assertFalse($this->Perms->canSave($module));
            static::assertFalse($this->Perms->canDelete(['type' => $module]));
        }
        static::assertSame([], $this->Perms->canCreateModules());
        static::assertSame(['missing' => false, 'empty' => false, 'null' => false], $this->Perms->canSaveMap());
    }

    /**
     * @return array<string, array{array|null, string|null, bool, bool}>
     */
    public static function currentModuleProvider(): array
    {
        return [
            'implicit readonly' => [['hints' => ['allow' => ['GET']]], 'profiles', true, false],
            'implicit writable' => [['hints' => ['allow' => ['GET', 'POST', 'PATCH']]], 'documents', true, true],
            'empty methods fallback' => [['hints' => ['allow' => []]], 'profiles', true, true],
            'missing methods fallback' => [['name' => 'profiles'], 'profiles', true, true],
            'missing module fallback' => [null, 'profiles', true, true],
            'missing object type' => [['hints' => ['allow' => ['GET']]], null, false, false],
        ];
    }

    /**
     * @param array|null $current Current module
     * @param string|null $objectType Current object type
     * @param bool $read Expected read permission
     * @param bool $write Expected create and save permissions
     * @return void
     */
    #[DataProvider('currentModuleProvider')]
    public function testCurrentModule(?array $current, ?string $objectType, bool $read, bool $write): void
    {
        $this->Perms->getView()->set(compact('objectType') + ['currentModule' => $current]);
        $this->Perms->initialize([]);
        foreach ([null, ''] as $module) {
            static::assertSame($read, $this->Perms->canRead($module));
            static::assertSame($write, $this->Perms->canCreate($module));
            static::assertSame($write, $this->Perms->canSave($module));
        }
    }

    /**
     * Folder restrictions apply to every operation and aggregate result.
     *
     * @return array<string, array{bool, array|null, array|null, bool}>
     */
    public static function folderPermissionsProvider(): array
    {
        return [
            'feature disabled' => [false, ['manager'], ['editor'], true],
            'matching role' => [true, ['manager'], ['manager'], true],
            'one matching role' => [true, ['guest', 'manager'], ['editor', 'manager'], true],
            'different roles' => [true, ['manager'], ['editor'], false],
            'no user' => [true, null, ['manager'], false],
            'no user roles' => [true, [], ['manager'], false],
            'unrestricted folder' => [true, ['manager'], [], true],
            'missing permissions' => [true, ['manager'], null, true],
            'admin bypass' => [true, ['admin'], ['editor'], true],
        ];
    }

    /**
     * @param bool $enabled Whether folder permissions are supported
     * @param array|null $roles User roles, null for no identity
     * @param array|null $folderRoles Folder roles, null for no metadata
     * @param bool $expected Expected permission
     * @return void
     */
    #[DataProvider('folderPermissionsProvider')]
    public function testFolderPermissions(bool $enabled, ?array $roles, ?array $folderRoles, bool $expected): void
    {
        $view = $this->Perms->getView();
        $view->set([
            'modules' => [
                'folders' => ['hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']]],
                'documents' => ['hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']]],
            ],
            'currentModule' => ['hints' => ['allow' => ['GET', 'POST', 'PATCH', 'DELETE']]],
            'objectType' => 'folders',
            'object' => $folderRoles === null ? null : ['meta' => ['perms' => ['roles' => $folderRoles]]],
            'foldersSchema' => ['associations' => $enabled ? ['Permissions'] : []],
            'user' => $roles === null ? null : new Identity(['roles' => $roles]),
        ]);
        $this->Perms->initialize([]);
        $apiClient = $this->createMock(BEditaClient::class);
        $apiClient->method('get')->with('/objects/123?include=parents')->willReturn([]);
        ApiClientProvider::setApiClient($apiClient);

        foreach ([null, '', 'folders'] as $module) {
            static::assertSame($expected, $this->Perms->userIsAllowed($module));
            static::assertSame($expected, $this->Perms->canRead($module));
            static::assertSame($expected, $this->Perms->canCreate($module));
            static::assertSame($expected, $this->Perms->canSave($module));
        }
        static::assertSame($expected, $this->Perms->canDelete(['id' => '123', 'type' => 'folders']));
        static::assertTrue($this->Perms->canRead('documents'));
        static::assertTrue($this->Perms->canSave('documents'));
        static::assertSame(
            $expected ? ['folders', 'documents'] : ['documents'],
            $this->Perms->canCreateModules(),
        );
        static::assertSame(['folders' => $expected, 'documents' => true], $this->Perms->canSaveMap());
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
                'profiles',
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
     * @param bool $isAdmin Is admin flag
     * @param string|null $module The module
     * @param bool $expected The expected result
     * @return void
     */
    #[DataProvider('canWriteProvider')]
    public function testCanCreate(bool $isAdmin, ?string $module, bool $expected): void
    {
        if ($isAdmin) {
            /** @var \Authentication\Identity $user */
            $user = new Identity(['id' => 2, 'roles' => ['admin']]);
            $this->Perms->getView()->set('user', $user);
        }
        static::assertSame($expected, $this->Perms->canCreate($module));
    }

    /**
     * Data provider for `canRead` method.
     *
     * @return array<string, array{bool, string|null, bool}>
     */
    public static function canReadProvider(): array
    {
        return [
            'null' => [
                false,
                null,
                false,
            ],
            'not available' => [
                false,
                'folders',
                false,
            ],
            'admin on readonly API module' => [
                true,
                'profiles', // only GET allowed
                true,
            ],
            'admin on non-readonly API module' => [
                true,
                'documents',
                true,
            ],
            'non admin on readonly API module' => [
                false,
                'profiles',
                true,
            ],
            'non admin on non-readonly API module' => [
                false,
                'documents',
                true,
            ],
        ];
    }

    /**
     * Test `canRead` method
     *
     * @param bool $isAdmin Is admin flag
     * @param string|null $module The module
     * @param bool $expected The expected result
     * @return void
     */
    #[DataProvider('canReadProvider')]
    public function testCanRead(bool $isAdmin, ?string $module, bool $expected): void
    {
        if ($module === null) {
            $this->Perms->getView()->set('objectType', null);
        }
        if ($isAdmin) {
            /** @var \Authentication\Identity $user */
            $user = new Identity(['id' => 2, 'roles' => ['admin']]);
            $this->Perms->getView()->set('user', $user);
        }
        static::assertSame($expected, $this->Perms->canRead($module));
    }

    /**
     * Data provider for `canCreate`, `canSave` methods.
     *
     * @return array<string, array{bool, string|null, bool}>
     */
    public static function canWriteProvider(): array
    {
        return [
            'null' => [
                false,
                null,
                false,
            ],
            'not available' => [
                false,
                'folders',
                false,
            ],
            'admin on readonly API module' => [
                true,
                'profiles', // only GET allowed
                false,
            ],
            'admin on non-readonly API module' => [
                true,
                'documents',
                true,
            ],
            'non admin on readonly API module' => [
                false,
                'profiles',
                false,
            ],
            'non admin on non-readonly API module' => [
                false,
                'documents',
                true,
            ],
        ];
    }

    /**
     * Test `canSave` method
     *
     * @param bool $isAdmin Is admin flag
     * @param string|null $module The module
     * @param bool $expected The expected result
     * @return void
     */
    #[DataProvider('canWriteProvider')]
    public function testCanSave(bool $isAdmin, ?string $module, bool $expected): void
    {
        if ($isAdmin) {
            /** @var \Authentication\Identity $user */
            $user = new Identity(['id' => 2, 'roles' => ['admin']]);
            $this->Perms->getView()->set('user', $user);
        }
        static::assertSame($expected, $this->Perms->canSave($module));
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
     * Object locks apply even to administrators and avoid parent requests.
     *
     * @return void
     */
    public function testCanDeleteLockedObjectAsAdmin(): void
    {
        $this->Perms->getView()->set('user', new Identity(['roles' => ['admin']]));
        $this->Perms->getView()->set('foldersSchema', ['associations' => ['Permissions']]);
        $this->Perms->initialize([]);
        $apiClient = $this->createMock(BEditaClient::class);
        $apiClient->expects(static::never())->method('get');
        ApiClientProvider::setApiClient($apiClient);

        static::assertFalse($this->Perms->canDelete([
            'id' => '123',
            'type' => 'documents',
            'meta' => ['locked' => true],
        ]));
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
            'unknown role' => [
                ['manager' => ['hidden' => ['cats']]],
                'guest',
                'cats',
                'write',
            ],
            'unlisted module' => [
                ['manager' => ['hidden' => ['cats'], 'readonly' => ['dogs']]],
                'manager',
                'horses',
                'write',
            ],
            'hidden takes precedence' => [
                ['manager' => ['hidden' => ['cats'], 'readonly' => ['cats']]],
                'manager',
                'cats',
                'hidden',
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
     * Data provider for `testUserIdentity` method.
     *
     * @return array<string, array{array|null, array, bool}>
     */
    public static function userRolesProvider(): array
    {
        return [
            'no identity' => [null, [], false],
            'missing roles' => [[], [], false],
            'null roles' => [['roles' => null], [], false],
            'empty roles' => [['roles' => []], [], false],
            'multiple roles' => [['roles' => ['manager', 'admin']], ['manager', 'admin'], true],
            'case sensitive' => [['roles' => ['Admin']], ['Admin'], false],
            'no loose admin match' => [['roles' => [true]], [true], false],
        ];
    }

    /**
     * Test user identity.
     *
     * @param array|null $user Identity data
     * @param array $expected Expected roles
     * @param bool $admin Expected administrator status
     * @return void
     */
    #[DataProvider('userRolesProvider')]
    public function testUserIdentity(?array $user, array $expected, bool $admin): void
    {
        $this->Perms->getView()->set('user', $user === null ? null : new Identity($user));
        static::assertSame($expected, $this->Perms->userRoles());
        static::assertSame($admin, $this->Perms->userIsAdmin());
        static::assertSame($admin, $this->Perms->canLock());
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
     * Data provider for `testParentLocks` method.
     *
     * @return array<string, array{bool, bool, array, bool}>
     */
    public static function parentsProvider(): array
    {
        $allowed = ['meta' => ['perms' => ['roles' => ['manager']]]];
        $denied = ['meta' => ['perms' => ['roles' => ['editor']]]];

        return [
            'feature disabled' => [false, false, [$denied], false],
            'admin bypass' => [true, true, [$denied], false],
            'no parents' => [true, false, [], false],
            'no metadata' => [true, false, [[]], false],
            'no permissions' => [true, false, [['meta' => []]], false],
            'empty roles' => [true, false, [['meta' => ['perms' => ['roles' => []]]]], false],
            'all parents allowed' => [true, false, [$allowed, $allowed], false],
            'unrestricted and allowed' => [true, false, [[], $allowed], false],
            'denied before allowed' => [true, false, [$denied, $allowed], true],
            'denied after allowed' => [true, false, [$allowed, $denied], true],
            'unrestricted and denied' => [true, false, [[], $denied], true],
        ];
    }

    /**
     * Test parents locks.
     *
     * @param bool $enabled Whether folder permissions are supported
     * @param bool $admin Whether user is admin
     * @param array $parents Included parents
     * @param bool $expected Expected parent lock
     * @return void
     */
    #[DataProvider('parentsProvider')]
    public function testParentLocks(bool $enabled, bool $admin, array $parents, bool $expected): void
    {
        $this->Perms->getView()->set([
            'foldersSchema' => ['associations' => $enabled ? ['Permissions'] : []],
            'user' => new Identity(['roles' => [$admin ? 'admin' : 'manager']]),
        ]);
        $this->Perms->initialize([]);
        $apiClient = $this->createMock(BEditaClient::class);
        $apiClient->expects($enabled && !$admin ? static::once() : static::never())
            ->method('get')
            ->with('/objects/123?include=parents')
            ->willReturn(['included' => $parents]);
        ApiClientProvider::setApiClient($apiClient);

        static::assertSame(!$expected, $this->Perms->canDelete(['id' => '123', 'type' => 'documents']));
    }

    /**
     * API failures must not be converted into permission grants.
     *
     * @return void
     */
    public function testParentLockApiFailure(): void
    {
        $this->Perms->getView()->set('foldersSchema', ['associations' => ['Permissions']]);
        $this->Perms->initialize([]);
        $apiClient = $this->createMock(BEditaClient::class);
        $apiClient->expects(static::once())->method('get')
            ->with('/objects/123?include=parents')
            ->willThrowException(new RuntimeException('API unavailable'));
        ApiClientProvider::setApiClient($apiClient);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('API unavailable');

        $this->Perms->canDelete(['id' => '123', 'type' => 'documents']);
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
