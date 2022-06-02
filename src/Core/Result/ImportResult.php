<?php

namespace App\Core\Result;

/**
 * {@inheritDoc}
 *
 * Import result class
 */
class ImportResult extends Result
{
    /**
     * File name
     *
     * @var string
     */
    public $filename;

    /**
     * Constructor
     *
     * @param string $filename the filename
     * @param int $created the counter of created objects
     * @param int $updated the counter of updated objects
     * @param int $errors the counter of errors (not created)
     * @param string $info the info message
     * @param string $warn the warn message
     * @param string $error the error message
     * @return void
     */
    public function __construct(
        $filename = '',
        $created = 0,
        $updated = 0,
        $errors = 0,
        $info = '',
        $warn = '',
        $error = ''
    ) {
        parent::__construct($created, $updated, $errors, $info, $warn, $error);
        $this->filename = $filename;
    }

    /**
     * Reset attributes
     *
     * @return void
     */
    public function reset(): void
    {
        $this->filename = $this->info = $this->warn = $this->error = '';
        $this->created = $this->updated = $this->errors = 0;
    }
}
