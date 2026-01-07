<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2026 ChannelWeb Srl, Chialab Srl
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

/**
 * Playground controller for testing Vue components in development.
 * Only accessible in DEVELOPMENT mode.
 *
 * {@codeCoverageIgnore}
 */
class PlaygroundController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        if (Configure::read('development') !== true) {
            $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
        parent::initialize();
    }

    /**
     * @inheritDoc
     */
    public function index(): void
    {
        $this->getRequest()->allowMethod(['get']);
    }
}
