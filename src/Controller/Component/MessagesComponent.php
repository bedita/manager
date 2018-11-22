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

use Cake\Controller\Component;

/**
 * Component to handle custom messages.
 */
class MessagesComponent extends Component
{
    /**
     * Messages map. Custom messages for API or system messages.
     */
    public const MESSAGES_MAP = [
        'You are not authorized to access that location.' => 'You are not logged or your session has expired, please provide login credentials'
    ];

    /**
     * Customize flash messages not directly handled by controllers
     * I.e. auth or API error messages.
     *
     * @return void
     */
    public function customize() : void
    {
        if (empty($_SESSION['Flash']['flash'])) {
            return;
        }
        $keys = array_keys(MessagesComponent::MESSAGES_MAP);
        foreach ($_SESSION['Flash']['flash'] as &$flash) {
            if (in_array($flash['message'], $keys)) {
                $flash['message'] = MessagesComponent::MESSAGES_MAP[$flash['message']];
            }
        }
    }
}
