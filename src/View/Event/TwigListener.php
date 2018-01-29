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

namespace App\View\Event;

use App\View\Twig\BeditaTwigExtension;
use Cake\Event\EventListenerInterface;
use WyriHaximus\TwigView\Event\ConstructEvent;

/**
 * Listener class for twig custom extensions
 */
class TwigListener implements EventListenerInterface
{
    /**
     * Get implemented events
     *
     * @return array implemented events.
     */
    public function implementedEvents()
    {
        return [
            ConstructEvent::EVENT => 'construct',
        ];
    }

    /**
     * Constructor
     *
     * @param WyriHaximus\TwigView\Event\ConstructEvent $event construct event
     */
    public function construct(ConstructEvent $event)
    {
        $event->getTwig()->addExtension(new BeditaTwigExtension());
    }
}
