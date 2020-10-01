<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\HistoryComponent;
use App\Controller\Component\SchemaComponent;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \App\Controller\Component\HistoryComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\HistoryComponent
 */
class HistoryComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\HistoryComponent
     */
    public $HistoryComponent;

    /**
     * Http Request
     *
     * @var \Cake\Http\ServerRequest
     */
    private $Request = null;

    /**
     * Schema component
     *
     * @var \App\Controller\Component\SchemaComponent
     */
    public $SchemaComponent;

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
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $controller = new Controller();
        $registry = $controller->components();
        $this->HistoryComponent = $registry->load(HistoryComponent::class);
        $this->SchemaComponent = $registry->load(SchemaComponent::class);
        $user = getenv('BEDITA_ADMIN_USR');
        $pass = getenv('BEDITA_ADMIN_PWD');
        $this->ApiClient = ApiClientProvider::getApiClient();
        $response = $this->ApiClient->authenticate($user, $pass);
        $this->ApiClient->setupTokens($response['meta']);
        $this->Request = new ServerRequest([
            'environment' => [
                'REQUEST_METHOD' => 'GET',
            ],
        ]);
        $response = $this->ApiClient->save('documents', [
            'title' => 'test history controller',
        ]);
        $this->documentId = Hash::get($response, 'data.id');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->HistoryComponent);
        unset($this->SchemaComponent);
        unset($this->ApiClient);
        unset($this->documentId);
        parent::tearDown();
    }

    /**
     * Provider for `testLoad` method
     *
     * @return array
     */
    public function loadProvider(): array
    {
        return [
            'empty object' => [
                [],
                '',
            ],
            'object no data' => [
                [
                    'objectType' => 'documents',
                    'historyId' => 1,
                ],
                '',
            ],
            'a dummy document' => [
                [
                    'objectType' => 'documents',
                    'historyId' => 1,
                ],
                '{"title":"dummy"}',
            ],
        ];
    }

    /**
     * Test `load` method
     *
     * @covers ::load()
     * @dataProvider loadProvider()
     * @param array $object The object for test
     * @param string $expected The expected object
     * @return void
     */
    public function testLoad(array $object, string $expected): void
    {
        $session = $this->HistoryComponent->getController()->request->getSession();
        if ($expected != '') {
            $key = sprintf('history.%s.attributes', $this->documentId);
            $session->write($key, $expected);
        }
        $this->HistoryComponent->load($this->documentId, $object);
        if ($expected != '') {
            static::assertNull($session->read($key));
            static::assertEquals($object['attributes'], (array)json_decode($expected, true));
        } else {
            static::assertArrayNotHasKey('attributes', $object);
        }
    }

    /**
     * Provider for `testWrite` method
     *
     * @return array
     */
    public function writeProvider(): array
    {
        return [
            'test document, keepUname false' => [
                [
                    'objectType' => 'documents',
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => false,
                ],
                '{"status":"","uname":"","title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
            'test document, keepUname true' => [
                [
                    'objectType' => 'documents',
                    'id' => $this->documentId,
                    'historyId' => 1,
                    'keepUname' => true,
                ],
                '{"status":"","uname":null,"title":"","description":"","body":"","extra":"","lang":"","publish_start":"","publish_end":""}',
            ],
        ];
    }

    /**
     * Test `write` method
     *
     * @covers ::write()
     * @dataProvider writeProvider()
     * @param array $options The options for test
     * @param string $expected The expected serialized data
     * @return void
     */
    public function testWrite(array $options, string $expected): void
    {
        $options += [
            'ApiClient' => $this->ApiClient,
            'Schema' => $this->SchemaComponent,
            'Request' => $this->Request,
        ];
        $this->HistoryComponent->write($options);
        $session = $this->HistoryComponent->getController()->request->getSession();
        $actual = $session->read(sprintf('history.%s.attributes', $options['id']));
        static::assertEquals($actual, $expected);
    }
}
