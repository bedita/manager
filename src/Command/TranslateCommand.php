<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;

class TranslateCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addOption('input', [
            'short' => 'i',
            'help' => 'Input file path',
            'required' => true,
        ]);
        $parser->addOption('output', [
            'short' => 'o',
            'help' => 'Output file path',
            'required' => true,
        ]);
        $parser->addOption('from', [
            'short' => 'f',
            'help' => 'Source language',
            'required' => true,
        ]);
        $parser->addOption('to', [
            'short' => 't',
            'help' => 'Dest language',
            'required' => true,
        ]);
        $parser->addOption('translator', [
            'short' => 'e',
            'help' => 'Translator engine name',
            'required' => true,
        ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $i = (string)($args->getOption('input') ?? (string)$args->getOption('i'));
        if (!file_exists($i)) {
            $io->err(sprintf('Input file "%s" does not exist', $i));

            return self::CODE_ERROR;
        }
        // debug
        $o = (string)($args->getOption('output') ?? $args->getOption('o'));
        $f = (string)($args->getOption('from') ?? $args->getOption('f'));
        $t = (string)($args->getOption('to') ?? $args->getOption('t'));
        $e = (string)($args->getOption('translator') ?? $args->getOption('e'));
        $io->out(sprintf('"%s" [%s] -> "%s" [%s] using "%s" engine.', $i, $f, $o, $t, $e));
        $cfg = (array)Configure::read(sprintf('Translators.%s', $e));
        if (empty($cfg)) {
            $io->err(sprintf('No translator engine "%s" is set in configuration', $e));

            return self::CODE_ERROR;
        }
        $class = $cfg['class'];
        $options = $cfg['options'];
        $translator = new $class();
        $translator->setup($options);
        $translation = $translator->translate([(string)file_get_contents($i)], $f, $t);
        $translation = json_decode($translation, true);
        $translation = $translation['translation'];
        file_put_contents($o, $translation);
        $io->out('Done. Bye!');

        return self::CODE_SUCCESS;
    }
}
