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
namespace App\Controller;

use Cake\Utility\Hash;

/**
 * Translator controller.
 */
class TranslatorController extends AppController
{
    /**
     * {@inheritDoc}
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Translator');
        $this->Security->setConfig('unlockedActions', ['translate']);
    }

    /**
     * Translate text.
     *
     * @return void
     */
    public function translate(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->request->allowMethod(['post']);
        $data = $this->request->getData();
        $from = Hash::get($data, 'from');
        $to = Hash::get($data, 'to');
        $text = Hash::get($data, 'text');
        $texts = (is_array($text)) ? $text : [$text];
        try {
            $json = $this->Translator->translate($texts, $from, $to);
            $decoded = json_decode($json);
            $this->set('translation', $decoded->translation);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->set(compact('error'));
        }
        $this->set('_serialize', ['translation', 'error']);
    }
}
