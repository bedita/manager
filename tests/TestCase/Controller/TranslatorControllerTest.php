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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;

/**
 * {@see \App\Controller\TranslatorController} Test Case
 */
#[CoversClass(TranslatorController::class)]
#[CoversMethod(TranslatorController::class, 'initialize')]
#[CoversMethod(TranslatorController::class, 'translate')]
class TranslatorControllerTest extends TestCase
{
    /**
     * Test Translator controller
     *
     * @var \App\Controller\TranslatorController
     */
    public TranslatorController $controller;

    /**
     * Test `translate` method
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
                    'translator' => 'xxx',
                ],
            ])
        );
        $this->controller->translate();

        static::assertEquals('Translator engine "xxx" not configured', $this->controller->viewBuilder()->getVar('error'));
    }

    /**
     * Test `translate` method
     *
     * @return void
     */
    public function testTranslate(): void
    {
        Configure::write('Translators', [
            'dummy' => [
                'name' => 'Dummy',
                'class' => '\App\Test\TestCase\Core\I18n\DummyTranslator',
                'options' => [
                    'url' => 'www.my-dummy-translator.com',
                    'apiKey' => 'abcde',
                ],
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
                    'translator' => 'dummy',
                ],
            ])
        );
        $this->controller->translate();
        $actual = $this->controller->viewBuilder()->getVar('translation');
        $translator = new DummyTranslator();
        $expected = json_decode($translator->translate($texts, 'en', 'it'));
        static::assertEquals($actual, $expected->translation);
    }

    /**
     * test `initialize` function
     *
     * @return void
     */
    public function testInitialize(): void
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
        static::assertNotEmpty($this->controller->{'Translator'});
    }
}
