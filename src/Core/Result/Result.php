<?php

namespace App\Core\Result;

/**
 * Basic result class
 */
class Result
{
    /**
     * Counter of created resources
     *
     * @var int
     */
    public $created;

    /**
     * Counter of updated resources
     *
     * @var int
     */
    public $updated;

    /**
     * Counter of errors
     *
     * @var int
     */
    public $errors;

    /**
     * Info message
     *
     * @var string
     */
    public $info;

    /**
     * Warning message
     *
     * @var string
     */
    public $warn;

    /**
     * Error message
     *
     * @var string
     */
    public $error;

    /**
     * Constructor
     *
     * @param int|null $created the counter of created objects
     * @param int|null $updated the counter of updated objects
     * @param int|null $errors the counter of errors (not created)
     * @param string|null $info the info message
     * @param string|null $warn the warn message
     * @param string|null $error the error message
     */
    public function __construct(
        $created = 0,
        $updated = 0,
        $errors = 0,
        $info = null,
        $warn = null,
        $error = null
    ) {
        $this->created = $created;
        $this->updated = $updated;
        $this->errors = $errors;
        $this->info = $info;
        $this->warn = $warn;
        $this->error = $error;
    }
}
