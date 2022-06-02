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

/**
 * Translator controller.
 *
 * @property \App\Controller\Component\TranslatorComponent $Translator
 */
class TranslatorController extends AppController
{
    /**
     * @inheritDoc
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
        $this->getRequest()->allowMethod(['post']);
        $text = $this->getRequest()->getData('text', '');
        $texts = is_array($text) ? $text : [$text];
        try {
            $json = $this->Translator->translate(
                $texts,
                (string)$this->getRequest()->getData('from'),
                (string)$this->getRequest()->getData('to')
            );
            $decoded = json_decode($json);
            $this->set('translation', $decoded->translation);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->set(compact('error'));
        }
        $this->setSerialize(['translation', 'error']);
    }
}
