<?php
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\ObjectsEditorsComponent;
use Authentication\AuthenticationServiceInterface;
use Authentication\Controller\Component\AuthenticationComponent;
use Authentication\Identity;
use Authentication\IdentityInterface;
use Cake\Cache\Cache;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * {@see \App\Controller\Component\ObjectsEditorsComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\ObjectsEditorsComponent
 */
class ObjectsEditorsComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\ObjectsEditorsComponent
     */
    public $ObjectsEditors;

    /**
     * Authentication component
     *
     * @var \Authentication\Controller\Component\AuthenticationComponent;
     */
    public $Authentication;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        Cache::enable();
        parent::setUp();
        $controller = new Controller();
        $registry = $controller->components();
        $registry->load('Authentication.Authentication');
        /** @var \App\Controller\Component\ObjectsEditorsComponent $objectsEditorsComponent */
        $objectsEditorsComponent = $registry->load(ObjectsEditorsComponent::class);
        $this->ObjectsEditors = $objectsEditorsComponent;
        /** @var \Authentication\Controller\Component\AuthenticationComponent $authenticationComponent */
        $authenticationComponent = $registry->load(AuthenticationComponent::class);
        $this->Authentication = $authenticationComponent;
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->ObjectsEditors);
        Cache::disable();

        parent::tearDown();
    }

    /**
     * Get mocked AuthenticationService.
     *
     * @return AuthenticationServiceInterface
     */
    protected function getAuthenticationServiceMock(): AuthenticationServiceInterface
    {
        $authenticationService = $this->getMockBuilder(AuthenticationServiceInterface::class)
            ->getMock();
        $authenticationService->method('clearIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response): array {
                return [
                    'request' => $request->withoutAttribute('identity'),
                    'response' => $response,
                ];
            });
        $authenticationService->method('persistIdentity')
            ->willReturnCallback(function (ServerRequestInterface $request, ResponseInterface $response, IdentityInterface $identity): array {
                return [
                    'request' => $request->withAttribute('identity', $identity),
                    'response' => $response,
                ];
            });

        return $authenticationService;
    }

    /**
     * Test initial setup
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        // verify concurrent check time is not empty
        $this->ObjectsEditors->initialize([]);
        static::assertNotEmpty($this->ObjectsEditors->concurrentCheckTime);

        // verify concurrent check time is read from config
        $expected = 15000;
        Configure::write('Editors.concurrentCheckTime', 15000); // 15 seconds
        $this->ObjectsEditors->initialize([]);
        $actual = $this->ObjectsEditors->concurrentCheckTime;
        static::assertEquals($expected, $actual);

        // verify objectsEditors is an array
        $expected = true;
        $actual = is_array($this->ObjectsEditors->objectsEditors);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `update` method
     *
     * @return void
     * @covers ::update()
     */
    public function testUpdate(): void
    {
        $controller = $this->ObjectsEditors->getController();
        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        $time = time();
        $this->ObjectsEditors->objectsEditors = [
            '99999' => [
                ['name' => 'gustavo', 'timestamp' => $time],
                ['name' => 'john doe', 'timestamp' => $time],
                ['name' => 'jane doe', 'timestamp' => $time],
            ],
        ];

        // id not found in objectsEditors
        $user = ['id' => 123, 'attributes' => ['name' => 'Gustavo', 'surname' => 'Support']];
        $this->Authentication->setIdentity(new Identity($user));
        $idNotInEditors = '999';
        $this->ObjectsEditors->update($idNotInEditors);
        $expected = 'Gustavo Support';
        $actual = Hash::get($this->ObjectsEditors->objectsEditors, sprintf('%s.0.name', $idNotInEditors));
        static::assertEquals($expected, $actual);
        $expected = [
            ['name' => 'gustavo', 'timestamp' => $time],
            ['name' => 'john doe', 'timestamp' => $time],
            ['name' => 'jane doe', 'timestamp' => $time],
        ];
        $actual = $this->ObjectsEditors->objectsEditors['99999'];
        static::assertEquals($expected, $actual);

        // id found in objectsEditors
        $user = ['id' => 12345, 'attributes' => ['name' => 'john', 'surname' => 'doe']];
        $this->Authentication->setIdentity(new Identity($user));
        $this->ObjectsEditors->update('99999');
        $expected = 'gustavo';
        $actual = $this->ObjectsEditors->objectsEditors['99999'][0]['name'];
        static::assertEquals($expected, $actual);
        $expected = 'john doe';
        $actual = $this->ObjectsEditors->objectsEditors['99999'][1]['name'];
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `editorName` method
     *
     * @return void
     * @covers ::editorName()
     */
    public function testEditorName(): void
    {
        $controller = $this->ObjectsEditors->getController();
        //$controller->Authentication = $this->Authentication;

        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        // empty user
        $expected = null;
        $actual = $this->ObjectsEditors->editorName();
        static::assertEquals($expected, $actual);

        // no username, no name, no surname
        $user = ['id' => 123];
        $this->Authentication->setIdentity(new Identity($user));
        $expected = null;
        $actual = $this->ObjectsEditors->editorName();
        static::assertEquals($expected, $actual);

        // username
        $user = ['id' => 123, 'attributes' => ['username' => 'gustavo']];
        $this->Authentication->setIdentity(new Identity($user));
        $expected = 'gustavo';
        $actual = $this->ObjectsEditors->editorName();
        static::assertEquals($expected, $actual);

        // name + surname
        $user = ['id' => 123, 'attributes' => ['name' => 'Gustavo', 'surname' => 'Support']];
        $this->Authentication->setIdentity(new Identity($user));
        $expected = 'Gustavo Support';
        $actual = $this->ObjectsEditors->editorName();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `getEditors` method
     *
     * @return void
     * @covers ::getEditors()
     */
    public function testGetEditors(): void
    {
        $controller = $this->ObjectsEditors->getController();
        //$controller->Authentication = $this->Authentication;

        // Mock Authentication component
        $controller->setRequest($controller->getRequest()->withAttribute('authentication', $this->getAuthenticationServiceMock()));

        // empty cache
        Cache::delete('objects_editors');
        $expected = [];
        $actual = $this->ObjectsEditors->getEditors();
        static::assertEquals($expected, $actual);

        // not empty cache
        $user = ['id' => 123, 'attributes' => ['username' => 'gustavo']];
        $this->Authentication->setIdentity(new Identity($user));
        $expected = $this->ObjectsEditors->update('999');
        $expected = json_decode($expected, true);
        $actual = $this->ObjectsEditors->getEditors();
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `cleanup` method
     *
     * @return void
     * @covers ::cleanup()
     */
    public function testCleanup(): void
    {
        // empty object editors
        $expected = [];
        $this->ObjectsEditors->objectsEditors = $expected;
        $this->ObjectsEditors->cleanup();
        static::assertEquals($expected, $this->ObjectsEditors->objectsEditors);

        // not empty, remove expired
        $validity = $this->ObjectsEditors->concurrentCheckTime / 1000;
        $time = time();
        $this->ObjectsEditors->objectsEditors = [
            '99999' => [
                ['name' => 'gustavo', 'timestamp' => $time], // valid
                ['name' => 'john doe', 'timestamp' => $time - $validity - 1], // expired
            ],
        ];
        $this->ObjectsEditors->cleanup();
        $expected = [
            '99999' => [
                ['name' => 'gustavo', 'timestamp' => $time], // valid
            ],
        ];
        static::assertEquals($expected, $this->ObjectsEditors->objectsEditors);
    }
}
