<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\CacheController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Admin\CacheController} Test Case
 *
 * @coversDefaultClass \App\Controller\Admin\CacheController
 */
class CacheControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var CacheController
     */
    public $Cache;

    /**
     * Test clear
     *
     * @return void
     * @covers ::clear()
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
                ]
            )
        );
        $this->Cache->clear();
        static::assertEmpty(Cache::read('something'));
    }
}
