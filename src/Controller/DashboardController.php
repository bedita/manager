<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller;

use Cake\Utility\Hash;

/**
 * Dashboard controller.
 */
class DashboardController extends AppController
{
    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();

        // force `GET /home` reload
        $this->Modules->setConfig('clearHomeCache', true);
    }

    /**
     * Displays dashboard.
     *
     * @return void
     */
    public function index(): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->set('recentItems', $this->recentItems());
    }

    /**
     * Render flash messages without layout for fetch requests.
     *
     * @return void
     */
    public function messages(): void
    {
        $this->getRequest()->allowMethod(['get']);
        $this->viewBuilder()->disableAutoLayout();
        $this->render('/Element/Dashboard/messages');
    }

    /**
     * Load 20 most recent items modified by authenticated user.
     *
     * @return array
     */
    protected function recentItems(): array
    {
        $user = $this->Authentication->getIdentity();
        if (empty($user)) {
            return [];
        }
        $filter = ['modified_by' => $user['id']];
        $limit = 20;
        $sort = '-modified';
        $response = $this->apiClient->getObjects('objects', compact('filter', 'limit', 'sort'));

        return (array)Hash::extract((array)$response, 'data.{n}');
    }
}
