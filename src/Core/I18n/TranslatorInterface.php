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

namespace App\Core\I18n;

/**
 * Translator interface
 */
interface TranslatorInterface
{
    /**
     * Setup translator engine.
     *
     * @param array $options The options
     * @return void
     */
    public function setup(array $options = []): void;

    /**
     * Translate a text $text from language source $from to language target $to
     *
     * @param array $text The texts to translate
     * @param string $from The source language
     * @param string $to The target language
     * @return string The translation in json format, i.e.
     * {
     *     "translation": [
     *         "<translation of first text>",
     *         "<translation of second text>",
     *         [...]
     *         "<translation of last text>"
     *     ]
     * }
     */
    public function translate(array $text, string $from, string $to): string;
}
