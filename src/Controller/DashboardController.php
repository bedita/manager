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

use App\Utility\CacheTools;
use App\Utility\SchemaTrait;
use Cake\Utility\Hash;

/**
 * Dashboard controller.
 */
class DashboardController extends AppController
{
    use SchemaTrait;

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

        /** @var \Authentication\Identity $user */
        $user = $this->Authentication->getIdentity();
        $this->set(
            'jobsAllow',
            (array)Hash::extract($this->getMeta($user), 'resources./async_jobs.hints.allow')
        );

        // set modules counters
        $modules = array_keys((array)$this->viewBuilder()->getVar('modules'));
        foreach ($modules as $name) {
            if (CacheTools::existsCount($name)) {
                continue;
            }
            $endpoint = $name === 'tags' ? '/model/tags' : $name;
            $options = $name === 'tags' ? ['page_size' => 1] : ['limit' => 1, 'page_size' => 1, 'fields' => 'id'];
            try {
                $response = $this->apiClient->get($endpoint, $options);
                CacheTools::setModuleCount($response, $name);
            } catch (\Exception $e) {
                CacheTools::setModuleCount(['meta' => ['pagination' => ['count' => '-']]], $name);
            }
        }
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
}
