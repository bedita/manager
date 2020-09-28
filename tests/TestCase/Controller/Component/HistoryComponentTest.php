<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\HistoryComponent;
use App\Controller\Component\SchemaComponent;
use BEdita\SDK\BEditaClient;
use BEdita\WebTools\ApiClientProvider;
use Cake\Controller\Controller;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\HistoryComponent Test Case
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
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->HistoryComponent);
        unset($this->SchemaComponent);
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
            'empty data' => [
                [
                    'objectType' => 'documents',
                    'id' => 10,
                    'historyId' => 1,
                ],
                '',
            ],
            'a dummy document' => [
                [
                    'objectType' => 'documents',
                    'id' => 10,
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
        $key = sprintf('history.%s.attributes', $object['id']);
        $session = $this->HistoryComponent->getController()->request->getSession();
        if ($expected != '') {
            $session->write($key, $expected);
        }
        $this->HistoryComponent->load($object);
        static::assertNull($session->read($key));
        if ($expected != '') {
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
            'non existent doc' => [
                [
                    'objectType' => 'documents',
                    'id' => 10,
                    'historyId' => 1,
                ],
                '{"id":null,"status":null,"uname":null,"locked":null,"created":null,"modified":null,"published":null,"title":null,"description":null,"body":null,"extra":null,"lang":null,"created_by":null,"modified_by":null,"publish_start":null,"publish_end":null}',
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
