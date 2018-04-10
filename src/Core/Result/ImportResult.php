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
     * @param int|null $created the counter of created objects
     * @param int|null $updated the counter of updated objects
     * @param int|null $errors the counter of errors (not created)
     * @param string|null $info the info message
     * @param string|null $warn the warn message
     * @param string|null $error the error message
     */
    public function __construct(
        $filename,
        $created = 0,
        $updated = 0,
        $errors = 0,
        $info = null,
        $warn = null,
        $error = null
    ) {
        parent::__construct($created, $updated, $errors, $info, $warn, $error);
        $this->filename = $filename;
    }
}
