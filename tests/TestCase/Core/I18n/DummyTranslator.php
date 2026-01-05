<?php
declare(strict_types=1);

namespace App\Test\TestCase\Core\I18n;

use BEdita\I18n\Core\TranslatorInterface;

class DummyTranslator implements TranslatorInterface
{
    /**
     * The engine options.
     *
     * @var array
     */
    protected $options = [];

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
     * Translate an array of texts $text from language source $from to language target $to
     *
     * @param array $text The texts to translate
     * @param string $from The source language
     * @param string $to The target language
     * @return string The translation in json format as string, i.e.
     * {
     *     "translation": [
     *         "<translation of first text>",
     *         "<translation of second text>",
     *         [...]
     *         "<translation of last text>"
     *     ]
     * }
     */
    public function translate(array $text, string $from, string $to): string
    {
        $translation = [];
        foreach ($text as $tt) {
            $translation[] = sprintf('text: %s, from: %s, to: %s', $tt, $from, $to);
        }

        return json_encode(compact('translation'));
    }
}
