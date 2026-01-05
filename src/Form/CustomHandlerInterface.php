<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Form;

interface CustomHandlerInterface
{
    /**
     * Create custom form control options for a property
     *
     * @param string $name Property name
     * @param mixed $value Property value
     * @param array $options Options array from configuration
     * @return array
     */
    public function control(string $name, mixed $value, array $options): array;
}
