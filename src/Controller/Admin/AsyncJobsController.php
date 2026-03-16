<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use BEdita\SDK\BEditaClientException;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Async Jobs Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class AsyncJobsController extends AdministrationBaseController
{
    /**
     * List of async service names to lookup
     *
     * @var array
     */
    protected array $services = ['credentials_change', 'mail', 'signup', 'thumbnail'];

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->getRequest()->allowMethod(['get']);
        $importFilters = Configure::read('Filters.import', []);
        foreach ($importFilters as $filter) {
            $value = (string)Hash::get($filter, 'class');
            $this->updateServiceList($value);
        }
        $this->set('services', $this->services);

        return null;
    }

    /**
     * Get jobs rendering as json.
     *
     * @return void
     */
    public function jobs(): void
    {
        $this->viewBuilder()->setClassName('Json');
        $this->getRequest()->allowMethod('get');
        $importFilters = Configure::read('Filters.import', []);
        foreach ($importFilters as $filter) {
            $value = (string)Hash::get($filter, 'class');
            $this->updateServiceList($value);
        }
        $this->set('services', $this->services);
        $this->loadAsyncJobs();
        $this->setSerialize(['pagination', 'jobs']);
    }

    /**
     * Load async jobs services to lookup
     *
     * @return void
     */
    protected function loadAsyncJobs(): void
    {
        $pagination = [];
        $jobs = [];
        $service = $this->getRequest()->getQuery('service');
        if (!empty($this->services) && !empty($service)) {
            $query = [
                'sort' => '-created',
                'filter' => ['service' => $service],
                'page_size' => 100,
                'page' => $this->getRequest()->getQuery('page', 1),
            ];
            try {
                $response = $this->apiClient->get('/async_jobs', $query);
                $jobs = (array)Hash::get($response, 'data', []);
                $pagination = (array)Hash::get($response, 'meta.pagination');
            } catch (BEditaClientException $e) {
                $this->log($e->getMessage(), 'error');
                $this->Flash->error($e->getMessage(), ['params' => $e]);
            }
        }
        $this->set('pagination', $pagination);
        $this->set('jobs', $jobs);
    }

    /**
     * Update services list to lookup
     *
     * @param string $filterClass Filter class
     * @return void
     */
    protected function updateServiceList(string $filterClass): void
    {
        $service = call_user_func([$filterClass, 'getServiceName']);
        if (!empty($service) && !in_array($service, $this->services)) {
            $this->services[] = $service;
        }
    }
}
