<?php
namespace App\Test\TestCase\Controller;

use App\Controller\Component\HistoryComponent;
use App\Controller\HistoryController;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\ComponentRegistry;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\Controller\HistoryController} Test Case
 */
#[CoversClass(HistoryController::class)]
#[CoversMethod(HistoryController::class, 'clone')]
#[CoversMethod(HistoryController::class, 'get')]
#[CoversMethod(HistoryController::class, 'info')]
#[CoversMethod(HistoryController::class, 'objects')]
#[CoversMethod(HistoryController::class, 'restore')]
#[CoversMethod(HistoryController::class, 'setHistory')]
class HistoryControllerTest extends TestCase
{
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
        $this->loadRoutes();
        $user = getenv('BEDITA_ADMIN_USR');
        $pass = getenv('BEDITA_ADMIN_PWD');
        $this->ApiClient = ApiClientProvider::getApiClient();
        $response = $this->ApiClient->authenticate($user, $pass);
        $this->ApiClient->setupTokens($response['meta']);
        $response = $this->ApiClient->save('documents', [
            'title' => 'test history controller',
        ]);
        $this->documentId = Hash::get($response, 'data.id');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->ApiClient);
        unset($this->documentId);
        parent::tearDown();
    }

    /**
     * Provider for `testClone` method
     *
     * @return array
     */
    public static function cloneProvider(): array
    {
        return [
            'test document history 1' => [
                [
                    'historyId' => 1,
                ],
            ],
        ];
    }

    /**
     * Test `info` method
     *
     * @return void
     */
    public function testInfo(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;
            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }
        };
        $controller->info($this->documentId, 1);
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `clone` method
     *
     * @param array $data The data for test
     * @return void
     */
    #[DataProvider('cloneProvider')]
    public function testClone(array $data): void
    {
        $data['id'] = $this->documentId;
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];

        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;
            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }
        };
        $response = $controller->clone($id, $historyId);
        static::assertEquals(302, $response->getStatusCode());
    }

    /**
     * Provider for `testRestore` method
     *
     * @return array
     */
    public static function restoreProvider(): array
    {
        return [
            'test document history 1' => [
                [
                    'historyId' => 1,
                ],
            ],
        ];
    }

    /**
     * Test `restore` method
     *
     * @param array $data The data for test
     * @return void
     */
    #[DataProvider('restoreProvider')]
    public function testRestore(array $data): void
    {
        $data['id'] = $this->documentId;
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;
            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }
        };
        $response = $controller->restore($id, $historyId);
        static::assertEquals(302, $response->getStatusCode());
    }

    /**
     * Provider for `testSetHistory` method
     *
     * @return array
     */
    public static function setHistoryProvider(): array
    {
        return [
            'document keepUname false' => [
                [
                    'historyId' => 1,
                    'keepUname' => false,
                ],
                '{"status":"","uname":"","title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
            'document keepUname true' => [
                [
                    'historyId' => 1,
                    'keepUname' => true,
                ],
                '{"status":"","uname":null,"title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
        ];
    }

    /**
     * Test `setHistory` method
     *
     * @param array $data
     * @param string $expected
     * @return void
     */
    #[DataProvider('setHistoryProvider')]
    public function testSetHistory(array $data, string $expected): void
    {
        $data['id'] = $this->documentId;
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];
        /** @var bool $keepUname */
        $keepUname = (bool)$data['keepUname'];

        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;

            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }

            public function setHistory(string|int $id, string|int $historyId, bool $keepUname): void
            {
                parent::setHistory($id, $historyId, $keepUname);
            }
        };
        $controller->setHistory($id, $historyId, $keepUname);
        $actual = $controller->getRequest()->getSession()->read(sprintf('history.%s.attributes', $id));
        $actual = json_decode($actual, true);
        $expected = json_decode($expected, true);
        $expected['uname'] = $keepUname ? $actual['uname'] : '';
        static::assertEquals($actual, $expected);
    }

    /**
     * Test `get` method
     *
     * @return void
     */
    public function testGet(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;

            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }
        };
        $controller->get();
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
        $actual = $controller->viewBuilder()->getVar('data')[0];
        $vars = [
            'id',
            'type',
            'meta',
        ];
        foreach ($vars as $var) {
            static::assertArrayHasKey($var, $actual);
        }
        static::assertArrayNotHasKey('links', $actual);
        static::assertArrayNotHasKey('relationships', $actual);
        $meta = $actual['meta'];
        $vars = [
            'resource_id',
            'resource_type',
            'created',
            'user_id',
            'application_id',
            'user_action',
            'changed',
        ];
        foreach ($vars as $var) {
            static::assertArrayHasKey($var, $meta);
        }
    }

    /**
     * Test `objects` method
     *
     * @return void
     */
    public function testObjects(): void
    {
        $request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
            'params' => [
                'object_type' => 'documents',
            ],
        ]);
        $controller = new class ($request) extends HistoryController {
            public HistoryComponent $History;
            public function __construct(ServerRequest $request)
            {
                $this->History = new HistoryComponent(new ComponentRegistry($this));
                parent::__construct($request);
            }
        };
        $controller->objects();
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertNotEmpty($controller->viewBuilder()->getVar($var));
        }
        $actual = $controller->viewBuilder()->getVar('data');
        foreach ($actual as $item) {
            static::assertArrayHasKey('id', $item);
            static::assertArrayHasKey('type', $item);
            static::assertArrayHasKey('attributes', $item);
            $attributes = $item['attributes'];
            static::assertArrayHasKey('uname', $attributes);
            static::assertArrayHasKey('title', $attributes);
            static::assertArrayNotHasKey('links', $item);
            static::assertArrayNotHasKey('relationships', $item);
        }
        $actual = $controller->viewBuilder()->getVar('meta');
        static::assertArrayHasKey('pagination', $actual);
        static::assertArrayNotHasKey('schema', $actual);
    }
}
