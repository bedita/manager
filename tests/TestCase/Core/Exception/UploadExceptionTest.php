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

namespace App\Test\TestCase\Core\Exception;

use App\Core\Exception\UploadException;
use Cake\TestSuite\TestCase;

/**
 * {@see \App\Core\Exception\UploadException} Test Case
 *
 * @coversDefaultClass \App\Core\Exception\UploadException
 */
class UploadExceptionTest extends TestCase
{
    /**
     * Data provider for `testCodeToMessage` test case.
     *
     * @return array
     */
    public function codeToMessageProvider(): array
    {
        return [
            'UPLOAD_ERR_INI_SIZE' => [
                UPLOAD_ERR_INI_SIZE,
                __('The uploaded file exceeds current max size of {0}', ini_get('upload_max_filesize')),
            ],
            'UPLOAD_ERR_FORM_SIZE' => [
                UPLOAD_ERR_FORM_SIZE,
                __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'),
            ],
            'UPLOAD_ERR_PARTIAL' => [
                UPLOAD_ERR_PARTIAL,
                __('The uploaded file was only partially uploaded'),
            ],
            'UPLOAD_ERR_NO_FILE' => [
                UPLOAD_ERR_NO_FILE,
                __('No file was uploaded'),
            ],
            'UPLOAD_ERR_NO_TMP_DIR' => [
                UPLOAD_ERR_NO_TMP_DIR,
                __('Missing a temporary folder'),
            ],
            'UPLOAD_ERR_CANT_WRITE' => [
                UPLOAD_ERR_CANT_WRITE,
                __('Failed to write file to disk'),
            ],
            'UPLOAD_ERR_EXTENSION' => [
                UPLOAD_ERR_EXTENSION,
                __('File upload stopped by extension'),
            ],
            'Unknown upload error' => [
                9999999,
                __('Unknown upload error'),
            ],
        ];
    }

    /**
     * Test `codeToMessage` method.
     *
     * @return void
     * @dataProvider codeToMessageProvider()
     * @covers ::codeToMessage()
     */
    public function testCodeToMessage($code, $message): void
    {
        $e = new UploadException('', $code);
        static::assertEquals($message, $e->getMessage());
    }
}
