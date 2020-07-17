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

class DummyTranslator implements TranslatorInterface
{
    /**
     * Setup translator engine.
     *
     * @param array $options The options
     * @return void
     */
    public function setup(array $options = []): void
    {
        $this->options = $options;
    }

    /**
     * Translate a text $text from language source $from to language target $to
     *
     * @param string $text The text to translate
     * @param string $from The source language
     * @param string $to The target language
     * @return string The translation
     */
    public function translate(string $text, string $from, string $to): string
    {
        return sprintf('text: %s | from: %s | to: %s', $text, $from, $to);
    }
}
