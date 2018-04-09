<?php

namespace App\Core\Result;

/**
 * Basic result class
 */
class Result
{
    /**
     * Counter of created objects
     *
     * @var int
     */
    public $objects;

    /**
     * Counter of errors (not created)
     *
     * @var int
     */
    public $errors;

    /**
     * Result message
     *
     * @var string
     */
    public $message;

    /**
     * Result error
     *
     * @var string
     */
    public $error;

    /**
     * Constructor
     *
     * @param int|null $objects the counter of created objects
     * @param int|null $errors the counter of errors (not created)
     * @param string|null $message the result message
     * @param string|null $error the error message
     */
    public function __construct($objects = 0, $errors = 0, $message = null, $error = null)
    {
        $this->objects = $objects;
        $this->errors = $errors;
        $this->message = $message;
        $this->error = $error;
    }
}
