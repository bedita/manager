<?php
namespace App\Test\TestCase\Controller;

use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * Base controller test class, with utils.
 */
class BaseControllerTest extends TestCase
{
    /**
     * Test api client
     *
     * @var \BEdita\SDK\BEditaClient
     */
    public $client;

    /**
     * Uname for test object
     *
     * @var string
     */
    protected $uname = 'controller-test-document';

    /**
     * Uname for test object
     *
     * @var string
     */
    protected $folderUname = 'controller-test-folder';

    /**
     * Test request config
     *
     * @var array
     */
    public $defaultRequestConfig = [
        'environment' => [
            'REQUEST_METHOD' => 'GET',
        ],
        'get' => [],
        'params' => [
            'object_type' => 'documents',
        ],
    ];

    /**
     * Get an object for test purposes
     *
     * @return array|null
     */
    protected function getTestObject(): ?array
    {
        $response = $this->client->getObjects('documents', ['filter' => ['uname' => $this->uname]]);

        if (!empty($response['data'][0])) {
            return (array)$response['data'][0];
        }

        return null;
    }

    /**
     * Get a folder for test purposes
     *
     * @return array|null
     */
    protected function getTestFolder(): ?array
    {
        $response = $this->client->getObjects('folders', ['filter' => ['uname' => $this->folderUname]]);

        if (!empty($response['data'][0])) {
            return $response['data'][0];
        }

        return null;
    }

    /**
     * Get test object id
     *
     * @return string
     */
    protected function getTestId(): string
    {
        // call index and get first available object, for test view
        $o = $this->getTestObject();

        return (string)Hash::get($o, 'id');
    }

    /**
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    protected function createTestObject(): array
    {
        $o = $this->getTestObject();
        if ($o == null) {
            $response = $this->client->save('documents', [
                'title' => 'controller test document',
                'uname' => $this->uname,
            ]);
            $o = $response['data'];
        }

        return $o;
    }

    /**
     * Create a folder for test purposes (if not available already)
     *
     * @return array
     */
    protected function createTestFolder(): array
    {
        $o = $this->getTestFolder();
        if ($o == null) {
            $response = $this->client->save('folders', [
                'title' => 'controller test folder',
                'uname' => $this->folderUname,
            ]);
            $o = $response['data'];
        }

        return $o;
    }

    /**
     * Restore object by id
     *
     * @param string $id The object ID
     * @param string $type The object type
     * @return void
     */
    protected function restoreTestObject(string $id, string $type): void
    {
        $o = $this->getTestObject();
        if ($o == null) {
            $this->client->restoreObject($id, $type);
        }
    }

    /**
     * Setup api client and auth
     *
     * @return void
     */
    protected function setupApi(): void
    {
        $this->client = ApiClientProvider::getApiClient();
        $adminUser = getenv('BEDITA_ADMIN_USR');
        $adminPassword = getenv('BEDITA_ADMIN_PWD');
        $response = $this->client->authenticate($adminUser, $adminPassword);
        $this->client->setupTokens($response['meta']);
    }

    public function testDummy(): void
    {
        static::assertEquals(1, 1); // dummy test to avoid warning
    }
}
