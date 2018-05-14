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
namespace App\View;

use App\View\Twig\BeditaTwigExtension;
use WyriHaximus\TwigView\View\TwigView;

/**
 * Application View default class
 *
 */
class AppView extends TwigView
{

    /**
     * {@inheritDoc}
     */
    public function initialize() : void
    {
        parent::initialize();

        $this->loadHelper('Flash');
        $this->loadHelper('Form', [
            'templates' => [
                'hiddenBlock' => '{{content}}',
                'submitContainer' => '{{content}}',
                'inputContainer' => '<div class="input {{type}}{{required}} {{containerClass}}">{{content}}</div>',
            ],
        ]);
        $this->loadHelper('Array');
        $this->loadHelper('Html');
        $this->loadHelper('Link');
        $this->loadHelper('Schema');
        $this->loadHelper('Text');
        $this->loadHelper('Time');
        $this->loadHelper('Media');
        $this->loadHelper('Url');

        $this->getTwig()
            ->addExtension(new BeditaTwigExtension());
    }
}
