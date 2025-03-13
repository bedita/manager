<?php
namespace App\Test\TestCase\Controller;

use App\Controller\LockController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use ReflectionClass;

/**
 * {@see \App\Controller\LockController} Test Case
 */
#[CoversClass(LockController::class)]
#[CoversMethod(LockController::class, 'add')]
#[CoversMethod(LockController::class, 'lock')]
#[CoversMethod(LockController::class, 'remove')]
class LockControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\LockController
     */
    public LockController $LockController;

    /**
     * Client class
     *
     * @var \BEdita\SDK\BEditaClient|null
     */
    private ?BEditaClient $ApiClient = null;

    /**
     * Document ID
     *
     * @var string|null
     */
    private ?string $documentId = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $user = getenv('BEDITA_ADMIN_USR');
        $pass = getenv('BEDITA_ADMIN_PWD');
        $this->ApiClient = ApiClientProvider::getApiClient();
        $response = $this->ApiClient->authenticate($user, $pass);
        $this->ApiClient->setupTokens($response['meta']);
        $response = $this->ApiClient->save('documents', [
            'title' => 'test lock controller',
        ]);
        $this->documentId = Hash::get($response, 'data.id');
        $this->LockController = new LockController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'object_type' => 'documents',
                    'id' => $this->documentId,
                ],
            ])
        );
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->LockController);
        parent::tearDown();
    }

    /**
     * Test `add` method
     *
     * @return void
     */
    public function testAdd(): void
    {
        $this->LockController->add();
        $response = $this->ApiClient->getObject($this->documentId);
        $actual = (bool)Hash::get($response, 'data.meta.locked');
        $expected = true;
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `remove` method
     *
     * @return void
     */
    public function testRemove(): void
    {
        $this->LockController->remove();
        $response = $this->ApiClient->getObject($this->documentId);
        $actual = (bool)Hash::get($response, 'data.meta.locked');
        $expected = false;
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `lock` method
     *
     * @return void
     */
    public function testLock(): void
    {
        $reflectionClass = new ReflectionClass($this->LockController);
        $method = $reflectionClass->getMethod('lock');
        $method->setAccessible(true);
        $method->invokeArgs($this->LockController, [true]);
        $response = $this->ApiClient->getObject($this->documentId);
        $actual = (bool)Hash::get($response, 'data.meta.locked');
        $expected = true;
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `lock` method, on exception
     *
     * @return void
     */
    public function testLockException(): void
    {
        $this->LockController = new LockController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'object_type' => 'documents',
                    'id' => 999999999,
                ],
            ])
        );
        $reflectionClass = new ReflectionClass($this->LockController);
        $method = $reflectionClass->getMethod('lock');
        $method->setAccessible(true);
        $actual = $method->invokeArgs($this->LockController, [true]);
        $expected = false;
        static::assertEquals($expected, $actual);
    }
}
