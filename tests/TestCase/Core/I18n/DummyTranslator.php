<?php
namespace App\Test\TestCase\Core\I18n;

class DummyTranslator
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
     * Translate an array of texts $texts from language source $from to language target $to
     *
     * @param array $texts The texts to translate
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
    public function translate(array $texts, string $from, string $to): string
    {
        $translation = [];
        foreach ($texts as $text) {
            $translation[] = sprintf('text: %s, from: %s, to: %s', $text, $from, $to);
        }

        return json_encode(compact('translation'));
    }
}
