<?php
namespace App\Test\TestCase\Controller;

use BEdita\WebTools\ApiClientProvider;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Laminas\Diactoros\UploadedFile;

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
     * Uname for test media
     *
     * @var string
     */
    protected $mediaUname = 'controller-test-media';

    /**
     * Uname for test media with stream
     *
     * @var string
     */
    protected $mediaWithStreamUname = 'controller-test-media-stream';

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
     * Get a media for test purposes
     *
     * @return array|null
     */
    protected function getTestMedia(): ?array
    {
        $response = $this->client->getObjects('files', ['filter' => ['uname' => $this->mediaUname]]);

        if (!empty($response['data'][0])) {
            return $response['data'][0];
        }

        return null;
    }

    /**
     * Get a media for test purposes
     *
     * @return array|null
     */
    protected function getTestMediaWithStream(): ?array
    {
        $response = $this->client->getObjects('files', ['filter' => ['uname' => $this->mediaWithStreamUname]]);
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
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    public function createTestObjectWithTranslation(): array
    {
        $o = $this->getTestObject();
        if ($o == null) {
            // create document
            $response = $this->client->save('documents', ['title' => 'translations controller test document', 'uname' => $this->uname]);
            $o = $response['data'];

            // create translation
            $this->client->save('translations', ['object_id' => $o['id'], 'status' => 'draft', 'lang' => 'it', 'translated_fields' => ['title' => 'Titolo di test']]);
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
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    protected function createTestMedia(): array
    {
        $o = $this->getTestMedia();
        if ($o == null) {
            $response = $this->client->save('files', [
                'title' => 'controller test media',
                'uname' => $this->mediaUname,
            ]);
            $o = $response['data'];
        }

        return $o;
    }

    /**
     * Create a object for test purposes (if not available already)
     *
     * @return array
     */
    protected function createTestMediaWithStream(): array
    {
        $o = $this->getTestMediaWithStream();
        if ($o == null) {
            // upload file
            $filename = sprintf('%s/tests/files/%s', getcwd(), 'test.png');
            $file = new UploadedFile($filename, filesize($filename), 0, $filename);
            $filename = basename($file->getClientFileName());
            $filepath = $file->getStream()->getMetadata('uri');
            $headers = ['Content-Type' => $file->getClientMediaType()];
            $response = $this->client->upload($filename, $filepath, $headers);
            $streamId = $response['data']['id'];
            $type = 'images';
            $attributes = [];
            $data = compact('type', 'attributes');
            $body = compact('data');
            $response = $this->client->createMediaFromStream($streamId, $type, $body);
            $response = $this->client->save('images', [
                'id' => $response['data']['id'],
                'title' => 'controller test media',
                'uname' => $this->mediaWithStreamUname,
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
        $this->client->setupTokens((array)Hash::get($response, 'meta'));
    }

    public function testDummy(): void
    {
        static::assertEquals(1, 1); // dummy test to avoid warning
    }
}
