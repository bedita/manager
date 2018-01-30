<?php

namespace App\Model\API;

use Cake\Core\Exception\Exception;

/**
 * Network exception thrown by BEdita API Client.
 */
class BEditaClientException extends Exception
{

    /**
     * {@inheritDoc}
     */
    protected $_messageTemplate = '[%s] %s';
}
