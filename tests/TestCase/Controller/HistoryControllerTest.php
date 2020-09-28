<?php
namespace App\Test\TestCase\Controller;

use App\Controller\HistoryController;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\HistoryController} Test Case
 *
 * @coversDefaultClass \App\Controller\HistoryController
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
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
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
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->HistoryController);
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
            'documents 10 history 1' => [
                [
                    'id' => 10,
                    'historyId' => 1,
                ],
            ],
        ];
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
            'documents 10 history 1' => [
                [
                    'id' => 10,
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
            'document with no history' => [
                [
                    'id' => 10,
                    'historyId' => 1,
                ],
                '{"id":null,"status":null,"uname":null,"locked":null,"created":null,"modified":null,"published":null,"title":null,"description":null,"body":null,"extra":null,"lang":null,"created_by":null,"modified_by":null,"publish_start":null,"publish_end":null}',
            ],
        ];
    }

    /**
     * Test `setHistory` method
     *
     * @covers ::setHistory()
     * @dataProvider setHistoryProvider()
     * @param array $data The data for test
     * @return void
     */
    public function testSetHistory(array $data, string $expected): void
    {
        /** @var string $id */
        $id = (string)$data['id'];
        /** @var string $historyId */
        $historyId = (string)$data['historyId'];

        // call protected method using AppControllerTest->invokeMethod
        $test = new AppControllerTest(new ServerRequest());
        $test->invokeMethod($this->HistoryController, 'setHistory', [$id, $historyId]);
        $actual = $this->HistoryController->request->getSession()->read(sprintf('history.%s.attributes', $id));
        static::assertEquals($actual, $expected);
    }
}
