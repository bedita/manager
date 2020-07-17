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

use BEdita\SDK\BEditaClientException;
use Cake\Utility\Hash;

/**
 * Translator controller.
 */
class TranslatorController extends AppController
{
    /**
     * Translate text.
     *
     * @return void
     */
    public function translate(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->request->allowMethod(['get']);
        $query = $this->request->getQueryParams();
        $from = Hash::get($query, 'from');
        $to = Hash::get($query, 'to');
        $text = Hash::get($query, 'text');
        try {
            $translation = $this->Translator->translate($text, $from, $to);
            $this->set(compact('translation'));
        } catch (\Exception $e) {
            $error = $e->getMessage();
            $this->set(compact('error'));
        }
        $this->set('_serialize', ['translation', 'error']);
    }
}
