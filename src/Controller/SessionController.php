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

use Cake\Http\Exception\NotFoundException;

class SessionController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setClassName('Json');
    }

    public function view(string $name): void
    {
        $this->request->allowMethod(['GET']);
        $value = $this->request->getSession()->read($name);
        if ($value === null) {
            throw new NotFoundException('Session variable not defined');
        }

        $this->set(compact('value'));
        $this->viewBuilder()->setOption('serialize', ['value']);
    }

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

    public function delete(string $name): void
    {
        $this->request->allowMethod(['DELETE']);
        $exists = $this->request->getSession()->check($name);
        if (!$exists) {
            throw new NotFoundException('Session variable not defined');
        }

        $this->request->getSession()->delete($name);

        $this->setResponse($this->response->withStatus(204));
    }
}
