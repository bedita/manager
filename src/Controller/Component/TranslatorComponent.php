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

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Core\Configure;
use Cake\Http\Exception\InternalErrorException;
use Cake\Utility\Hash;

/**
 * Translator component. Provide utilities to translate texts.
 */
class TranslatorComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'Translators' => [],
    ];

    /**
     * Translators engines registry
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Translate a text $text from language source $from to language target $to
     *
     * @param array $texts Array of texts to translate
     * @param string $from The source language
     * @param string $to The target language
     * @param string $translatorName The translator engine name
     * @return string The translation
     * @throws \Cake\Http\Exception\InternalErrorException when translator engine is not configured
     */
    public function translate(array $texts, string $from, string $to, string $translatorName): string
    {
        if (empty($this->registry[$translatorName])) {
            $translators = (array)Configure::read('Translators');
            $translator = (array)Hash::get($translators, $translatorName);
            if (empty($translator['class']) && empty($translator['options'])) {
                throw new InternalErrorException(sprintf('Translator engine "%s" not configured', $translatorName));
            }
            $class = $translator['class'];
            $options = $translator['options'];
            $translator = new $class();
            $translator->setup($options);
            $this->registry[$translatorName] = $translator;
        }

        return $this->registry[$translatorName]->translate($texts, $from, $to);
    }
}
