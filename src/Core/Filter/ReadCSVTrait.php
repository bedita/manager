<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Core\Filter;

/**
 * Add CSV read methods
 */
trait ReadCSVTrait
{
    /**
     * CSV default options
     *
     * @var array
     */
    protected $csvOptions = [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape' => '\\',
    ];

    /**
     * CSV file keys read from header
     *
     * @var array
     */
    protected $csvKeys = [];

    /**
     * CSV file data content organized as associative array with `key` => `value`
     *
     * @var array
     */
    protected $csvData = [];

    /**
     * Read CSV file and populate `csvKeys` and `csvData` arrays
     *
     * @param string $filepath CSV file path
     * @param array $options CSV options overriding defaults
     * @return void
     */
    public function readCSVFile(string $filepath, ?array $options = []): void
    {
        $options = array_merge($this->csvOptions, (array)$options);
        $this->csvKeys = $this->csvData = [];

        $filecontent = file($filepath); // <= into an array
        if (empty($filecontent)) {
            return;
        }
        // read keys from first line
        $line = array_shift($filecontent);
        $this->csvKeys = str_getcsv($line, $options['delimiter'], $options['enclosure'], $options['escape']);
        $this->csvKeys = array_map('trim', $this->csvKeys);
        // read data using keys
        foreach ($filecontent as $line) {
            $values = str_getcsv($line, $options['delimiter'], $options['enclosure'], $options['escape']);
            if (empty($values) || ((count($values) != count($this->csvKeys)))) {
                continue;
            }
            $this->csvData[] = array_combine($this->csvKeys, $values);
        }
    }
}
