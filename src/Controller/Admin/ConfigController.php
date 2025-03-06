<?php
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

use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Config Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ConfigController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected string $resourceType = 'config';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'name' => 'string',
        'context' => 'string',
        'content' => 'json',
        'application_id' => 'applications',
    ];

    /**
     * @inheritDoc
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);
        $this->set('applications', $this->fetchApplications());

        return null;
    }

    /**
     * Fetch applications
     *
     * @return array
     */
    public function fetchApplications(): array
    {
        $data = [];
        $pageCount = $page = 1;
        $pageSize = 100;
        $query = ['filter' => ['enabled' => 1], 'page_size' => $pageSize];
        while ($page <= $pageCount) {
            $response = (array)$this->apiClient->get('/admin/applications', $query + compact('page'));
            $applications = (array)Hash::combine($response['data'], '{n}.id', '{n}.attributes.name');
            $applications = array_flip($applications);
            $data = empty($data) ? $applications : array_merge($data, $applications);
            $pageCount = (int)Hash::get($response, 'meta.pagination.page_count');
            $page++;
        }
        ksort($data);
        $data = array_flip($data);

        return ['' => __('No application')] + $data;
    }
}
