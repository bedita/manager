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

namespace App\Test\TestCase\Controller;

use App\Controller\TranslatorController;
use App\Test\TestCase\Core\I18n\DummyTranslator;
use Cake\Core\Configure;
use Cake\Http\Exception\MethodNotAllowedException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Controller\TranslatorController} Test Case
 *
 * @coversDefaultClass \App\Controller\TranslatorController
 * @uses \App\Controller\TranslatorController
 */
class TranslatorControllerTest extends TestCase
{
    /**
     * Test Translator controller
     *
     * @var App\Controller\TranslatorController
     */
    public $controller;

    /**
     * Test `translate` method
     *
     * @covers ::translate()
     *
     * @return void
     */
    public function testTranslateMethodNotAllowedException(): void
    {
        $this->expectException(get_class(new MethodNotAllowedException()));
        $this->controller = new TranslatorController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'GET',
                ],
            ])
        );
        $this->controller->translate();
    }

    /**
     * Test `translate` method
     *
     * @covers ::translate()
     *
     * @return void
     */
    public function testTranslateNoTranslatorEngine(): void
    {
        $this->controller = new TranslatorController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'post' => [
                    'text' => ['gustavo is a friend', 'gustavo is the best'],
                    'from' => 'en',
                    'to' => 'it',
                ],
            ])
        );
        $this->controller->translate();

        static::assertEquals('No translator engine is set in configuration', $this->controller->viewVars['error']);
    }

    /**
     * Test `translate` method
     *
     * @covers ::translate()
     *
     * @return void
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
        $texts = ['gustavo is a friend', 'gustavo is the best'];
        $this->controller = new TranslatorController(
            new ServerRequest([
                'environment' => [
                    'REQUEST_METHOD' => 'POST',
                ],
                'post' => [
                    'text' => $texts,
                    'from' => 'en',
                    'to' => 'it',
                ],
            ])
        );
        $this->controller->translate();
        $actual = $this->controller->viewVars['translation'];
        $translator = new DummyTranslator();
        $expected = json_decode($translator->translate($texts, 'en', 'it'));
        static::assertEquals($actual, $expected->translation);
    }
}
