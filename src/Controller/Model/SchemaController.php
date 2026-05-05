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
namespace App\Controller\Model;

use App\Controller\AppController;
use Cake\Http\Response;

/**
 * Model schema visualizer page controller
 */
class SchemaController extends AppController
{
    /**
     * Show schema visualizer page.
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);

        $this->set('resourceType', 'schema');
        $this->set('moduleLink', ['_name' => 'model:schema']);

        return null;
    }
}
