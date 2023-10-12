<?php
declare(strict_types=1);

namespace App\Core\I18n;

use BabyMarkt\DeepL\DeepL;
use Cake\Utility\Hash;

class DeepLTranslator implements TranslatorInterface
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
    public function translate($texts, string $from, string $to): string
    {
        $deepl = new DeepL((string)Hash::get($this->options, 'auth_key'), 2);
        $translation = $deepl->translate($texts, $from, $to);
        if (empty($translation)) {
            return json_encode(['translation' => []]);
        }
        if (is_array($translation)) {
            $translation = Hash::extract($translation, '{n}.text');
        }

        return json_encode(compact('translation'));
    }
}
