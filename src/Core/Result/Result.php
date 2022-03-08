<?php

namespace App\Core\Result;

/**
 * Basic result class
 */
class Result
{
    /**
     * Message separato
     *
     * @var string
     */
    public const MSG_SEPARATOR = '<br/>';

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
     * @param int $created the counter of created objects
     * @param int $updated the counter of updated objects
     * @param int $errors the counter of errors (not created)
     * @param string $info the info message
     * @param string $warn the warn message
     * @param string $error the error message
     * @return void
     */
    public function __construct(
        $created = 0,
        $updated = 0,
        $errors = 0,
        $info = '',
        $warn = '',
        $error = ''
    ) {
        $this->created = $created;
        $this->updated = $updated;
        $this->errors = $errors;
        $this->info = $info;
        $this->warn = $warn;
        $this->error = $error;
    }

    /**
     * Add info, error, warn or other custom message
     *
     * @param string $name Message type name: 'info', 'error', 'warn'
     * @param string $msg Message string
     * @return void
     */
    public function addMessage(string $name, string $msg): void
    {
        if (property_exists($this, $name) && is_string($this->{$name})) {
            $this->{$name} .= $msg . static::MSG_SEPARATOR;
        }
    }

    /**
     * Increment counter, like `created` or `updated`
     *
     * @param string $name Counter name: `created` or `updated`
     * @return void
     */
    public function increment(string $name): void
    {
        if (property_exists($this, $name) && is_int($this->{$name})) {
            $this->{$name}++;
        }
    }
}
