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

use Cake\Core\Exception\Exception;

/**
 * Exception raised when uploading from form fails
 */
class UploadException extends Exception
{
    /**
     * {@inheritDoc}
     * @codeCoverageIgnore
     */
    public function __construct(?string $message, int $code, $previous = null)
    {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code, $previous);
    }

    /**
     * Php code to message for exception
     * @see http://php.net/manual/en/features.file-upload.errors.php for details
     *
     * @param int $code The php code
     * @return string
     */
    private function codeToMessage(int $code) :string
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = __('The uploaded file exceeds the upload_max_filesize directive in php.ini');
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form');
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = __('The uploaded file was only partially uploaded');
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = __('No file was uploaded');
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = __('Missing a temporary folder');
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = __('Failed to write file to disk');
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = __('File upload stopped by extension');
                break;
            default:
                $message = __('Unknown upload error');
                break;
        }

        return $message;
    }
}
