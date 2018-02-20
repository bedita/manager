<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Error;

use Cake\Error\ExceptionRenderer;
use Exception;

/**
 * Custom exception renderer class.
 * Handle with templates 500 and 400 (for status code < 500).
 */
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
