<?php
namespace App\Test\TestCase\Controller;

use App\Controller\HistoryController;
use BEdita\WebTools\ApiClientProvider;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\HistoryController} Test Case
 *
 * @coversDefaultClass \App\Controller\HistoryController
 * @uses \App\Controller\HistoryController
 */
class HistoryControllerTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\HistoryController
     */
    public $HistoryController;

    /**
     * Client class
     *
     * @var \BEdita\SDK\BEditaClient
     */
    private $ApiClient = null;

    /**
     * Document ID
     *
     * @var string
     */
    private $documentId = null;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
        $this->HistoryController = new HistoryController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
                'params' => [
                    'object_type' => 'documents',
                ],
            ])
        );
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
        unset($this->HistoryController);
        unset($this->ApiClient);
        unset($this->documentId);
        parent::tearDown();
    }

    /**
     * Provider for `testClone` method
     *
     * @return array
     */
    public function cloneProvider(): array
    {
        return [
            'test document history 1' => [
                [
                    'id' => $this->documentId,
                    'historyId' => 1,
                ],
            ],
        ];
    }

    /**
     * Test `info` method
     *
     * @return void
     * @covers ::info()
     */
    public function testInfo(): void
    {
        $this->HistoryController->info($this->documentId);
        $vars = ['data', 'meta'];
        foreach ($vars as $var) {
            static::assertNotEmpty($this->HistoryController->viewBuilder()->getVar($var));
        }
    }

    /**
     * Test `clone` method
     *
     * @covers ::clone()
     * @dataProvider cloneProvider()
     * @param array $data The data for test
     * @return void
     */
    public function testClone(array $data): void
    {
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];

        $response = $this->HistoryController->clone($id, $historyId);
        static::assertEquals(302, $response->getStatusCode());
    }

    /**
     * Provider for `testRestore` method
     *
     * @return array
     */
    public function restoreProvider(): array
    {
        return [
            'test document history 1' => [
                [
                    'id' => $this->documentId,
                    'historyId' => 1,
                ],
            ],
        ];
    }

    /**
     * Test `restore` method
     *
     * @covers ::restore()
     * @dataProvider restoreProvider()
     * @param array $data The data for test
     * @return void
     */
    public function testRestore(array $data): void
    {
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];

        $response = $this->HistoryController->restore($id, $historyId);
        static::assertEquals(302, $response->getStatusCode());
    }

    /**
     * Provider for `testSetHistory` method
     *
     * @return array
     */
    public function setHistoryProvider(): array
    {
        return [
            'document keepUname false' => [
                [
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => false,
                ],
                '{"status":"","uname":"","title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
            'document keepUname true' => [
                [
                    'id' => $this->documentId,
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
     * @covers ::setHistory()
     * @dataProvider setHistoryProvider()
     * @param array $data
     * @param string $expected
     * @return void
     */
    public function testSetHistory(array $data, string $expected): void
    {
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];
        /** @var bool $keepUname */
        $keepUname = (bool)$data['keepUname'];

        // call protected method using AppControllerTest->invokeMethod
        $test = new AppControllerTest();
        $test->invokeMethod($this->HistoryController, 'setHistory', [$id, $historyId, $keepUname]);
        $actual = $this->HistoryController->getRequest()->getSession()->read(sprintf('history.%s.attributes', $id));
        static::assertEquals($actual, $expected);
    }
}
