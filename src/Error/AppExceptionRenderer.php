<?php

namespace App\Error;

use Cake\Error\ExceptionRenderer;
use Exception;

class AppExceptionRenderer extends ExceptionRenderer
{
    /**
     * {@inheritDoc}
     */
    protected function _template(Exception $exception, $method, $code) : string
    {
        $exception = $this->_unwrap($exception);

        $template = 'error500';
        if ($code < 500) {
            $template = 'error400';
        }

        return $this->template = $template;
    }
}
