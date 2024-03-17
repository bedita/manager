<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\Event;

use App\Event\TreeCacheEventHandler;
use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * {@see \App\Event\TreeCacheEventHandler} Test Case
 *
 * @coversDefaultClass \App\Event\TreeCacheEventHandler
 * @uses \App\Event\TreeCacheEventHandler
 */
class TreeCacheEventHandlerTest extends TestCase
{
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
     * Test `implementedEvents` method
     *
     * @covers ::implementedEvents()
     * @return void
     */
    public function testImplementedEvents(): void
    {
        static::assertCount(0, EventManager::instance()->listeners('Controller.afterDelete'));
        static::assertCount(0, EventManager::instance()->listeners('Controller.afterSave'));
        static::assertCount(0, EventManager::instance()->listeners('Controller.afterSaveRelated'));
        EventManager::instance()->on(new TreeCacheEventHandler());
        static::assertCount(1, EventManager::instance()->listeners('Controller.afterDelete'));
        static::assertCount(1, EventManager::instance()->listeners('Controller.afterSave'));
        static::assertCount(1, EventManager::instance()->listeners('Controller.afterSaveRelated'));
    }

    /**
     * Data provider for all test cases.
     *
     * @return array
     */
    public function dataProvider(): array
    {
        return [
            'afterDelete no data' => [
                'afterDelete',
                [],
                false,
            ],
            'afterDelete type is not folders' => [
                'afterDelete',
                ['type' => 'documents'],
                false,
            ],
            'afterDelete is folders' => [
                'afterDelete',
                ['type' => 'folders', 'id' => 999],
                true,
            ],
            'afterSave no data' => [
                'afterSave',
                [],
                false,
            ],
            'afterSave type is not folders' => [
                'afterSave',
                ['type' => 'documents'],
                false,
            ],
            'afterSave no children, no title, no status' => [
                'afterSave',
                ['type' => 'folders', 'id' => 999],
                false,
            ],
            'afterSave children' => [
                'afterSave',
                ['type' => 'folders', 'id' => 999, 'data' => ['relation' => 'children']],
                true,
            ],
            'afterSave title' => [
                'afterSave',
                ['type' => 'folders', 'id' => 999, 'data' => ['title' => 'test']],
                true,
            ],
            'afterSave status' => [
                'afterSave',
                ['type' => 'folders', 'id' => 999, 'data' => ['status' => 'on']],
                true,
            ],
            'afterSaveRelated no data' => [
                'afterSaveRelated',
                [],
                false,
            ],
            'afterSaveRelated type is not folders' => [
                'afterSaveRelated',
                ['type' => 'documents'],
                false,
            ],
            'afterSaveRelated no children, no title, no status' => [
                'afterSaveRelated',
                ['type' => 'folders', 'id' => 999],
                false,
            ],
            'afterSaveRelated children' => [
                'afterSaveRelated',
                ['type' => 'folders', 'id' => 999, 'data' => ['relation' => 'children']],
                true,
            ],
            'afterSaveRelated title' => [
                'afterSaveRelated',
                ['type' => 'folders', 'id' => 999, 'data' => ['title' => 'test']],
                true,
            ],
            'afterSaveRelated status' => [
                'afterSaveRelated',
                ['type' => 'folders', 'id' => 999, 'data' => ['status' => 'on']],
                true,
            ],
        ];
    }

    /**
     * Test `afterDelete`, `afterSave` and `afterSaveRelated` methods
     *
     * @param array $data Event data.
     * @param bool $cacheClear Expected cache action.
     * @return void
     * @dataProvider dataProvider
     * @covers ::afterDelete()
     * @covers ::updateCache()
     */
    public function testAll(string $method, array $data, bool $cacheClear): void
    {
        $randomString = Text::uuid();
        $randomNumber = rand(0, 100);
        Cache::write($randomString, $randomNumber, 'default');
        Cache::write('tree-parent-1', ['test' => 'data'], TreeCacheEventHandler::CACHE_CONFIG);
        $handler = new TreeCacheEventHandler();
        $event = new Event(sprintf('Controller.%s', $method), $this, $data);
        $handler->{$method}($event);
        $actual = Cache::read('tree-parent-1', TreeCacheEventHandler::CACHE_CONFIG);
        $expected = $cacheClear ? null : ['test' => 'data'];
        static::assertEquals($expected, $actual);
        static::assertEquals(Cache::read($randomString), $randomNumber);
    }
}
