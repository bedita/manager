<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\TranslatorComponent;
use App\Test\TestCase\Core\I18n\DummyTranslator;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\Component\TranslatorComponent} Test Case
 *
 * @coversDefaultClass \App\Controller\Component\TranslatorComponent
 */
class TranslatorComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\TranslatorComponent
     */
    public $Translator;

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        unset($this->Translator);

        parent::tearDown();
    }

    /**
     * Create test case component
     *
     * @return void
     */
    protected function createComponent(): void
    {
        $controller = new Controller();
        $registry = $controller->components();
        $this->Translator = $registry->load(TranslatorComponent::class);
    }

    /**
     * Test `translate()` method.
     *
     * @return void
     *
     * @covers ::translate()
     * @covers ::initialize()
     */
    public function testTranslateInternalErrorException(): void
    {
        $this->expectException(get_class(new InternalErrorException()));
        $this->createComponent();
        $this->Translator->translate(['gustavo is a friend'], 'en', 'it');
    }

    /**
     * Test `translate()` method.
     *
     * @return void
     *
     * @covers ::translate()
     * @covers ::initialize()
     */
    public function testTranslate(): void
    {
        Configure::write('Translator', [
            'class' => '\App\Test\TestCase\Core\I18n\DummyTranslator',
            'options' => [
                'url' => 'www.my-dummy-translator.com',
                'apiKey' => 'abcde',
            ],
        ]);
        $this->createComponent();
        $texts = ['gustavo is a friend'];
        $actual = $this->Translator->translate($texts, 'en', 'it');
        $translator = new DummyTranslator();
        $expected = $translator->translate($texts, 'en', 'it');
        static::assertEquals($actual, $expected);
    }
}
