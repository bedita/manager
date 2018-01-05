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
namespace App\Controller;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Utility\Hash;

/**
 * Dashboard controller
 */
class DashboardController extends AppController
{
    /**
     * Displays dashboard
     */
    public function display()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(Event $event)
    {
        // force read from /home
        $this->readModules();
    }
}
