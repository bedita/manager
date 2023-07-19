<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

class SessionController extends AppController
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Security->setConfig('unlockedActions', ['save', 'delete']);
        $this->viewBuilder()->setClassName('Json');
    }

    /**
     * Get session variable.
     *
     * @param string $name Name of the session variable.
     * @return void
     */
    public function view(string $name): void
    {
        $this->request->allowMethod(['GET']);
        $value = $this->request->getSession()->read($name);
        $this->set(compact('value'));
        $this->viewBuilder()->setOption('serialize', ['value']);
    }

    /**
     * Save session variable.
     *
     * @return void
     */
    public function save(): void
    {
        $this->request->allowMethod(['POST', 'PATCH']);
        $name = $this->request->getData('name');
        $value = $this->request->getData('value');
        $exists = $this->request->getSession()->check($name);
        $this->request->getSession()->write($name, $value);

        $this->setResponse($this->response->withStatus($exists ? 200 : 201));
        $this->set(compact('value', 'name'));
        $this->viewBuilder()->setOption('serialize', ['value', 'name']);
    }

    /**
     * Delete session variable.
     *
     * @param string $name Name of the session variable.
     * @return void
     */
    public function delete(string $name): void
    {
        $this->request->allowMethod(['DELETE']);
        if ($this->request->getSession()->check($name)) {
            $this->request->getSession()->delete($name);
        }
        $this->setResponse($this->response->withStatus(204));
    }
}
