<?php
namespace App\Test\TestCase\Controller\Admin;

use App\Controller\Admin\RolesModulesController;
use App\View\AppView;
use BEdita\SDK\BEditaClient;
use BEdita\SDK\BEditaClientException;
use BEdita\WebTools\ApiClientProvider;
use Cake\Cache\Cache;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use DOMDocument;
use DOMElement;
use DOMXPath;
use LogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * First-use module access control tests without a live API.
 */
#[CoversClass(RolesModulesController::class)]
class RolesModulesSetupTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadRoutes();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        ApiClientProvider::setApiClient(null);
        Cache::disable();
        parent::tearDown();
    }

    /**
     * Page states with and without eligible roles and restrictions.
     *
     * @return array
     */
    public static function pageStatesProvider(): array
    {
        return [
            'first use' => [true, [], true, 'write'],
            'configured' => [true, ['editor' => ['readonly' => ['documents']]], false, 'read'],
            'configured hidden' => [true, ['editor' => ['hidden' => ['documents']]], false, 'hidden'],
            'unrestricted role' => [true, ['editor' => []], false, 'write'],
            'no eligible roles' => [false, [], true, 'write'],
            'configured without eligible roles' => [false, ['editor' => ['hidden' => ['documents']]], false, 'hidden'],
        ];
    }

    /**
     * Render actionable first-use and empty-role states.
     *
     * @param bool $hasRoles Whether eligible roles exist.
     * @param array $accessControl Module restrictions.
     * @param bool $showNotice Whether setup guidance is expected.
     * @param string $permission Expected selected permission.
     * @return void
     */
    #[DataProvider('pageStatesProvider')]
    public function testPageStates(bool $hasRoles, array $accessControl, bool $showNotice, string $permission): void
    {
        $view = new AppView(new ServerRequest());
        $view->getTwig()->setCache(false);
        $view->set([
            'resources' => $hasRoles ? [['id' => '2', 'attributes' => ['name' => 'editor']]] : [],
            'modules' => ['documents' => ['name' => 'documents']],
            'access_control' => $accessControl,
            'properties' => [],
            'propertiesSecrets' => [],
        ]);
        $html = $view->render('/Pages/Admin/RolesModules/index', false);

        static::assertSame($showNotice, str_contains($html, 'Module access control is not configured.'));
        static::assertSame(!$hasRoles, str_contains($html, 'No eligible roles available'));
        static::assertStringNotContainsString('Resources not available', $html);
        static::assertSame($hasRoles && array_key_exists('editor', $accessControl), str_contains($html, 'Remove module access configuration'));
        if (!$hasRoles) {
            static::assertStringContainsString('/admin/roles', $html);
            static::assertStringContainsString('/admin/endpoint_permissions', $html);
            static::assertStringNotContainsString('class="role-card"', $html);

            return;
        }

        $document = new DOMDocument();
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $selected = $xpath->query('//input[@name="roles[editor][documents]" and @checked]');
        static::assertCount(1, $selected);
        $input = $selected->item(0);
        static::assertInstanceOf(DOMElement::class, $input);
        static::assertSame($permission, $input->getAttribute('value'));
        static::assertStringContainsString('/admin/roles_modules/save', $html);
        if (array_key_exists('editor', $accessControl)) {
            static::assertStringContainsString('/admin/roles_modules/remove/2', $html);
            static::assertStringContainsString('API permissions will remain unchanged.', $html);
            static::assertCount(0, $xpath->query('//form//form'));
            static::assertCount(1, $xpath->query('//button[@type="button" and @form="form-remove-role-2" and @data-confirm]'));
            static::assertCount(1, $xpath->query('//form[@id="form-remove-role-2" and @method="post" and @hidden]'));
            static::assertStringContainsString('@remove-role="remove"', $html);
            static::assertStringContainsString('@click.prevent="$emit(\'remove-role\', $event)"', $html);
        }
    }

    /**
     * Save cases covering creation and existing configuration updates.
     *
     * @return array
     */
    public static function saveProvider(): array
    {
        $existing = [
            'editor' => ['hidden' => ['documents']],
            'guest' => ['readonly' => ['documents']],
        ];

        return [
            'create restrictions' => [
                [],
                ['documents' => 'read', 'users' => 'hidden', 'objects' => 'write'],
                ['editor' => ['readonly' => ['documents'], 'hidden' => ['users']]],
            ],
            'create all-write role' => [[], ['documents' => 'write'], ['editor' => []]],
            'clear restrictions and preserve other roles' => [
                $existing,
                ['documents' => 'write'],
                ['editor' => [], 'guest' => ['readonly' => ['documents']]],
            ],
        ];
    }

    /**
     * Verify exact persisted configuration and creation on first save.
     *
     * @param array $existing Existing configuration, if any.
     * @param array $permissions Submitted role permissions.
     * @param array $expected Expected persisted configuration.
     * @return void
     */
    #[DataProvider('saveProvider')]
    public function testSave(array $existing, array $permissions, array $expected): void
    {
        $config = $existing ? [
            'id' => '10',
            'attributes' => [
                'name' => 'AccessControl',
                'context' => 'app',
                'application_id' => 1,
                'content' => json_encode($existing),
            ],
        ] : null;
        $client = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $client->method('get')->willReturnCallback(
            fn($path) => match ($path) {
                '/config' => ['data' => $config ? [$config] : []],
                '/admin/applications' => ['data' => [['id' => '1']]],
                default => throw new LogicException(sprintf('Unexpected API request: %s', $path)),
            },
        );
        $data = [
            'type' => 'config',
            'attributes' => [
                'name' => 'AccessControl',
                'context' => 'app',
                'content' => json_encode($expected),
                'application_id' => 1,
            ],
        ];
        if ($existing) {
            $data['id'] = '10';
        }
        $client->expects($existing ? static::never() : static::once())->method('post')
            ->with('/admin/config', json_encode(['data' => $data]));
        $client->expects($existing ? static::once() : static::never())->method('patch')
            ->with('/admin/config/10', json_encode(['data' => $data]));
        ApiClientProvider::setApiClient($client);
        $controller = new class (new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
            'post' => ['roles' => ['editor' => $permissions]],
        ])) extends RolesModulesController
        {
            public ?BEditaClient $apiClient;
        };
        $controller->apiClient = $client;

        $response = $controller->save();

        static::assertSame(302, $response->getStatusCode());
        static::assertStringEndsWith('/admin/roles_modules', $response->getHeaderLine('Location'));
    }

    /**
     * Removal preserves other roles and handles empty or failed configuration.
     *
     * @return array
     */
    public static function removeProvider(): array
    {
        return [
            'preserve other roles' => [
                ['editor' => ['hidden' => ['documents']], 'guest' => ['readonly' => ['users']]],
                ['guest' => ['readonly' => ['users']]],
            ],
            'last role' => [['editor' => ['hidden' => ['documents']]], []],
            'unrestricted role' => [['editor' => []], []],
            'missing role' => [['guest' => []], null],
            'missing configuration' => [null, null],
            'API error' => [['editor' => []], [], true],
        ];
    }

    /**
     * Remove only the selected role's AccessControl entry.
     *
     * @param array|null $existing Existing configuration.
     * @param array|null $expected Expected configuration, null when removal is invalid.
     * @param bool $apiError Whether persistence fails.
     * @return void
     */
    #[DataProvider('removeProvider')]
    public function testRemove(?array $existing, ?array $expected, bool $apiError = false): void
    {
        $client = $this->getMockBuilder(BEditaClient::class)
            ->setConstructorArgs(['https://api.example.org'])
            ->getMock();
        $client->method('get')->willReturnCallback(
            fn($path) => match ($path) {
                '/roles/2' => ['data' => ['id' => '2', 'attributes' => ['name' => 'editor']]],
                '/config' => ['data' => $existing === null ? [] : [[
                    'id' => '10',
                    'attributes' => [
                        'name' => 'AccessControl',
                        'context' => 'app',
                        'application_id' => 1,
                        'content' => json_encode($existing),
                    ],
                ]]],
                default => throw new LogicException(sprintf('Unexpected API request: %s', $path)),
            },
        );
        $client->expects(static::never())->method('delete');
        $client->expects(static::never())->method('post');
        $patch = $client->expects($expected === null ? static::never() : static::once())->method('patch');
        if ($expected !== null) {
            $patch->with('/admin/config/10', json_encode(['data' => [
                'type' => 'config',
                'attributes' => [
                    'name' => 'AccessControl',
                    'context' => 'app',
                    'content' => json_encode($expected),
                    'application_id' => 1,
                ],
                'id' => '10',
            ]]));
            if ($apiError) {
                $patch->willThrowException(new BEditaClientException('Unable to save configuration'));
            }
        }
        ApiClientProvider::setApiClient($client);
        $controller = new class (new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'POST'],
        ])) extends RolesModulesController
        {
            public ?BEditaClient $apiClient;
        };
        $controller->apiClient = $client;
        if ($expected === null) {
            $this->expectException(NotFoundException::class);
        }

        $response = $controller->remove('2');

        static::assertSame(302, $response->getStatusCode());
        static::assertStringEndsWith('/admin/roles_modules', $response->getHeaderLine('Location'));
        if ($apiError) {
            static::assertSame(
                'Unable to save configuration',
                $controller->getRequest()->getSession()->read('Flash.flash.0.message'),
            );
        }
    }

    /**
     * Configuration removal must not accept GET requests.
     *
     * @return void
     */
    public function testRemoveRequiresPost(): void
    {
        $controller = new RolesModulesController(new ServerRequest([
            'environment' => ['REQUEST_METHOD' => 'GET'],
        ]));
        $this->expectException(MethodNotAllowedException::class);

        $controller->remove('2');
    }
}
