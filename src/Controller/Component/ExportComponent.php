<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
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
use Cake\Utility\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Export component. Utility to create csv, ods and xlsx spreadsheets.
 */
class ExportComponent extends Component
{
    /**
     * Allowed formats for export
     *
     * @var array
     */
    public const ALLOWED_FORMATS = ['csv', 'ods', 'xlsx'];

    /**
     * Spreadsheet columns int/letter mapping.
     *
     * @var array
     */
    public const LETTERS = [
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J',
        11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T',
        21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z',
    ];

    /**
     * Default properties for spreadsheet
     *
     * @var array
     */
    protected $defaultSpreadheetProperties = [
        'creator' => 'BEdita Manager',
        'lastModifiedBy' => 'BEdita Manager',
        'title' => '',
        'subject' => '',
        'description' => '',
        'keywords' => '',
        'category' => '',
    ];

    /**
     * Check if format is allowed
     *
     * @param string $format The format
     * @return bool
     */
    public function checkFormat(string $format): bool
    {
        return in_array($format, static::ALLOWED_FORMATS);
    }

    /**
     * Create spreadsheet with data from rows, using properties.
     *
     * @param string $format The format
     * @param array $rows The data
     * @param string $filename The file name
     * @param array $properties The properties
     * @return array
     */
    public function format(string $format, array $rows, string $filename, array $properties = []): array
    {
        $spreadsheet = new Spreadsheet();

        // set properties
        $properties = array_merge($this->defaultSpreadheetProperties, $properties);
        foreach ($properties as $property => $value) {
            $spreadsheet->getProperties()->{sprintf('set%s', ucfirst($property))}($value);
        }

        // create the worksheet
        $spreadsheet->setActiveSheetIndex(0);

        // set first row, column names
        if (!empty($rows)) {
            $first = array_values(array_shift($rows));
            foreach ($first as $k => $v) {
                $spreadsheet->getActiveSheet()->setCellValue(sprintf('%s1', $this->column($k + 1)), $v);
            }
        }

        // fill data
        $spreadsheet->getActiveSheet()->fromArray($rows, null, 'A2');

        // set autofilter
        $spreadsheet->getActiveSheet()->setAutoFilter($spreadsheet->getActiveSheet()->calculateWorksheetDimension());

        return $this->{$format}($spreadsheet, $filename);
    }

    /**
     * Calculate excel column name from integer number of column.
     *
     * @param int $num The number
     * @return string
     */
    protected function column(int $num): string
    {
        if (array_key_exists($num, self::LETTERS)) {
            return self::LETTERS[$num];
        }
        $div = intdiv($num, 26);
        $mod = $num % 26;
        if ($mod === 0) {
            $firstLetter = self::LETTERS[$div - 1];
            $secondLetter = 'Z';
        } else {
            $firstLetter = self::LETTERS[$div];
            $secondLetter = self::LETTERS[$mod];
        }

        return $firstLetter . $secondLetter;
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as csv
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return array
     */
    public function csv(Spreadsheet $spreadsheet, string $filename = 'export.csv'): array
    {
        $options = compact('filename') + ['format' => 'text/csv'];

        return $this->download($spreadsheet, 'Csv', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as ods
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return array
     */
    public function ods(Spreadsheet $spreadsheet, string $filename = 'export.ods'): array
    {
        $options = compact('filename') + ['format' => 'application/vnd.oasis.opendocument.spreadsheet'];

        return $this->download($spreadsheet, 'Ods', $options);
    }

    /**
     * Create spreadsheet file into memory and redirect output to download it as xlsx
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The spreadsheet
     * @param string $filename The file name
     * @return array
     */
    public function xlsx(Spreadsheet $spreadsheet, string $filename = 'export.xlsx'): array
    {
        $options = compact('filename') + ['format' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];

        return $this->download($spreadsheet, 'Xlsx', $options);
    }

    /**
     * Create spreadsheet, write to a temporary file, set content and content type and return them.
     *
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $spreadsheet The spreadsheet
     * @param string $extension The extension, can be 'Csv', 'Ods', 'Pdf', 'Xls', 'Xlsx'
     * @param array $options The options
     * @return array
     */
    protected function download(Spreadsheet $spreadsheet, string $extension, array $options): array
    {
        $tmpfilename = tempnam('/tmp', (string)Hash::get($options, 'filename'));
        $writer = IOFactory::createWriter($spreadsheet, $extension);
        $writer->save($tmpfilename);
        $content = file_get_contents($tmpfilename);
        $contentType = Hash::get($options, 'format');
        unlink($tmpfilename);

        return compact('content', 'contentType');
    }
}
