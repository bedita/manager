<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\CacheController;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\Admin\CacheController} Test Case
 */
#[CoversClass(CacheController::class)]
#[CoversMethod(CacheController::class, 'clear')]
class CacheControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Admin\CacheController
     */
    public CacheController $Cache;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Cache);
        Cache::disable();

        parent::tearDown();
    }

    /**
     * Test clear
     *
     * @return void
     */
    public function testClear(): void
    {
        // write something in cache
        Cache::write('something', 'true');
        $this->Cache = new CacheController(
            new ServerRequest(
                [
                    'environment' => [
                        'REQUEST_METHOD' => 'GET',
                    ],
                ],
            ),
        );
        $this->Cache->clear();
        static::assertEmpty(Cache::read('something'));
    }
}
