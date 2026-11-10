<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Test\TestCase\View;

use App\View\AppView;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use DOMDocument;
use DOMElement;
use DOMXPath;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\TestWith;

/**
 * {@see \App\View\AppView} Test Case
 */
#[CoversClass(AppView::class)]
#[CoversMethod(AppView::class, 'initialize')]
#[CoversMethod(AppView::class, '_getElementFileName')]
class AppViewTest extends TestCase
{
    /**
     * Test `initialize` method
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $View = new AppView();
        $extensions = $View->getTwig()->getExtensions();
        static::assertNotEmpty($extensions);
        static::assertArrayHasKey('BEdita\WebTools\View\Twig\BeditaTwigExtension', $extensions);
    }

    /**
     * Test `_getElementFileName` method
     *
     * @return void
     */
    public function testCustomElement(): void
    {
        $View = new AppView();
        $View->set('currentModule', ['name' => 'cats']);
        $result = $View->elementExists('Form/meta');
        static::assertTrue($result);

        $custom = [
            'cats' => [
                'Form/meta' => 'MyPlugin',
            ],
        ];
        Configure::write('Elements', $custom);
        $result = $View->elementExists('Form/meta');
        static::assertFalse($result);
    }

    /**
     * Trash exposes user filters only to users allowed to read users.
     *
     * @param bool $canReadUsers Whether reading users is allowed.
     * @return void
     */
    #[TestWith([true])]
    #[TestWith([false])]
    public function testTrashUserFilters(bool $canReadUsers): void
    {
        $this->loadRoutes();
        $view = new AppView(new ServerRequest(), null, null, ['viewVars' => [
            'currentModule' => ['name' => 'trash'],
            'modules' => ['users' => ['hints' => ['allow' => $canReadUsers ? ['GET'] : []]]],
            'objects' => [],
            'meta' => ['pagination' => ['page_size' => 20]],
            'schema' => ['properties' => [
                'created_by' => ['type' => 'integer'],
                'modified_by' => ['type' => 'integer'],
            ]],
        ]]);
        $view->getTwig()->setCache(false);
        static::assertSame($canReadUsers, $view->Perms->canRead('users'));
        $html = $view->render('/Pages/Trash/index', false);
        $document = new DOMDocument();
        $document->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($document);
        $filterBox = $xpath->query('//filter-box-view')->item(0);
        static::assertInstanceOf(DOMElement::class, $filterBox);
        $filters = json_decode($filterBox->getAttribute(':filter-list'), true, 512, JSON_THROW_ON_ERROR);
        static::assertSame($canReadUsers ? ['created_by', 'modified_by'] : [], array_column($filters, 'name'));
        static::assertCount($canReadUsers ? 2 : 0, $xpath->query('//user-filter'));
        if ($canReadUsers) {
            foreach (['created_by', 'modified_by'] as $name) {
                static::assertCount(1, $xpath->query(sprintf('//user-filter[@name="%s"]', $name)));
            }
        }
    }
}
