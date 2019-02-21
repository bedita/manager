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

namespace App\Controller\Component;

use BEdita\SDK\BEditaClientException;
use Cake\Controller\Component\FlashComponent as CakeFlashComponent;
use Cake\Utility\Hash;

/**
 * Extends CakePHP FlashComponent setting exception attributes.
 */
class FlashComponent extends CakeFlashComponent
{

    /**
     * {@inheritDoc}
     */
    public function set($message, array $options = [])
    {
        $error = Hash::get($options, 'params');
        if ($error && ($error instanceof \Exception)) {
            $options['params'] = [
                'message' => $error->getMessage(),
                'code' => $error->getCode(),
                'trace' => $error->getTraceAsString(),
            ];

            if ($error instanceof BEditaClientException) {
                $options['params']['attributes'] = $error->getAttributes();
            }
        }

        return parent::set($message, $options);
    }
}
