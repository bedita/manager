<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\ElementHelper;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \App\View\Helper\ElementHelper} Test Case
 */
#[CoversClass(ElementHelper::class)]
#[CoversMethod(ElementHelper::class, 'categories')]
#[CoversMethod(ElementHelper::class, 'custom')]
#[CoversMethod(ElementHelper::class, 'dropupload')]
#[CoversMethod(ElementHelper::class, 'multiupload')]
#[CoversMethod(ElementHelper::class, 'sidebar')]
class ElementHelperTest extends TestCase
{
    /**
     * Test `categories` method
     *
     * @return void
     */
    public function testCategories(): void
    {
        $expected = 'Form/categories';
        $viewVars = [
            'currentModule' => ['name' => 'documents'],
        ];
        $view = new View(new ServerRequest(), null, null, compact('viewVars'));
        $element = new ElementHelper($view);
        $actual = $element->categories();
        static::assertSame($expected, $actual);

        Configure::write('Modules.documents.categories._element', 'another_categories');
        $actual = $element->categories();
        static::assertSame('another_categories', $actual);
    }

    /**
     * Data provider for `testCustom` test case.
     *
     * @return array
     */
    public static function customProvider(): array
    {
        return [
            'empty' => [
                '',
                'test',
            ],
            'empty relation' => [
                'empty',
                'my_relation',
                'relation',
                [
                    'relations' => [
                        '_element' => [
                            'my_relation' => 'empty',
                        ],
                    ],
                ],
            ],
            'my_element' => [
                'MyPlugin.my_element',
                'my_group',
                'group',
                [
                    'view' => [
                        'my_group' => ['_element' => 'MyPlugin.my_element'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `custom` method
     *
     * @param string $expected The expected element
     * @param string $item The item
     * @param string $type The item type
     * @param array $conf Configuration to use
     * @return void
     */
    #[DataProvider('customProvider')]
    public function testCustom(string $expected, string $item, string $type = 'relation', array $conf = []): void
    {
        Configure::write('Properties.documents', $conf);
        $view = new View();
        $view->set('currentModule', ['name' => 'documents']);
        $element = new ElementHelper($view);
        $result = $element->custom($item, $type);
        static::assertSame($expected, $result);
    }

    /**
     * Test `sidebar` method
     *
     * @return void
     */
    public function testSidebar(): void
    {
        $expected = 'Modules/sidebar';
        $viewVars = [
            'currentModule' => ['name' => 'documents'],
        ];
        $view = new View(new ServerRequest(), null, null, compact('viewVars'));
        $element = new ElementHelper($view);
        $actual = $element->sidebar();
        static::assertSame($expected, $actual);

        Configure::write('Modules.documents.sidebar._element', 'another_sidebar');
        $actual = $element->sidebar();
        static::assertSame('another_sidebar', $actual);
    }

    /**
     * Test `dropupload` method
     *
     * @return void
     */
    public function testDropupload(): void
    {
        $expected = 'Form/dropupload';
        $viewVars = [
            'currentModule' => ['name' => 'documents'],
        ];
        $view = new View(new ServerRequest(), null, null, compact('viewVars'));
        $element = new ElementHelper($view);
        $actual = $element->dropupload(['documents', 'events']);
        static::assertSame($expected, $actual);

        Configure::write('Modules.documents.dropupload._element', 'another_dropupload');
        $actual = $element->dropupload(['documents', 'events']);
        static::assertSame('another_dropupload', $actual);
    }

    /**
     * Test `multiupload` method
     *
     * @return void
     */
    public function testMultiupload(): void
    {
        $expected = 'Form/multiupload';
        $viewVars = [
            'currentModule' => ['name' => 'documents'],
        ];
        $view = new View(new ServerRequest(), null, null, compact('viewVars'));
        $element = new ElementHelper($view);
        $actual = $element->multiupload();
        static::assertSame($expected, $actual);

        Configure::write('Modules.documents.multiupload._element', 'another_multiupload');
        $actual = $element->multiupload();
        static::assertSame('another_multiupload', $actual);
    }
}
