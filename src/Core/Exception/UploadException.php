<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita:you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Core\Exception;

use Cake\Core\Exception\CakeException;

/**
 * Exception raised when uploading from form fails
 */
class UploadException extends CakeException
{
    /**
     * Array to map upload error codes with proper message string
     *
     * @var array
     */
    protected $messagesMap = [
        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds current max size of {0}',
        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file was uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
        UPLOAD_ERR_EXTENSION => 'File upload stopped by extension',
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(?string $message, int $code, $previous = null)
    {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Php code to message for exception
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php for details
     * @param int $code The php code
     * @return string
     */
    private function codeToMessage(int $code): string
    {
        if (in_array($code, array_keys($this->messagesMap))) {
            return __($this->messagesMap[$code], ini_get('upload_max_filesize'));
        }

        return __('Unknown upload error');
    }
}
