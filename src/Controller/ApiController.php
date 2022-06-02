<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use BEdita\WebTools\Controller\ApiProxyTrait;
use Cake\Event\EventInterface;
use Cake\Http\Response;

/**
 * ApiController class.
 *
 * It proxies requests to BEdita 4 API.
 * The response will be the raw json response of the API itself.
 * Links of API host is masked with manager host.
 */
class ApiController extends AppController
{
    use ApiProxyTrait;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        $this->Security->setConfig('unlockedActions', ['post', 'patch', 'delete']);

        return null;
    }
}
