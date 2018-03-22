<?php

namespace App\Core\Result;

use App\Core\Result\Result;

/**
 * Import result class
 * @extends App\Core\Result\Result
 */
class ImportResult extends Result
{
    /**
     * File name
     *
     * @var string
     */
    protected $filename;

    /**
     * Constructor
     *
     * @param string $filename the filename
     * @param int|null $objects the counter of created objects
     * @param int|null $errors the counter of errors (not created)
     * @param string|null $message the result message
     * @param string|null $error the error message
     */
    public function __construct($filename, $objects, $errors, $message, $error)
    {
        parent::__construct($objects, $errors, $message, $error);
        $this->filename = $filename;
    }
}
