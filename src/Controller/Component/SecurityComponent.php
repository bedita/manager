<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Component;

use Cake\Controller\Component\SecurityComponent as CakeSecurityComponent;
use Cake\Controller\Controller;
use Cake\Routing\Router;

/**
 * Security component, useful to prevent form tampering and enforce other security measures.
 *
 * We extend CakePHP's component to replace check on session ID, if user is logged in with "remember me" feature.
 * This is necessary because the PHP session can expire and be renewed by Authentication component on form submission,
 * failing the form tampering check.
 */
class SecurityComponent extends CakeSecurityComponent
{
    /**
     * {@inheritDoc}
     */
    protected function _hashParts(Controller $controller)
    {
        $request = $controller->getRequest();

        // Start the session to ensure we get the correct session id.
        $session = $request->getSession();
        $session->start();

        $data = $request->getData();
        $fieldList = $this->_fieldsList($data);
        $unlocked = $this->_sortedUnlocked($data);

        // As part of form tampering, use user's token if user is already logged in, otherwise use session ID.
        $identifier = $session->id();
        /** @var \Authentication\IdentityInterface|null $identity */
        $identity = $controller->getRequest()->getAttribute('identity');
        if (!empty($identity) && !empty($identity->get('token')) && rtrim($controller->getRequest()->getPath(), '/') !== '/login') {
            $identifier = $identity->get('token');
        }

        return [
            Router::url($request->getRequestTarget()),
            serialize($fieldList),
            $unlocked,
            $identifier,
        ];
    }
}
