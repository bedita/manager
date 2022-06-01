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
    protected $resourceType = 'config';

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
        $response = $this->apiClient->get('/admin/applications', ['filter' => ['enabled' => 1]]);
        $applications = ['' => __('No application')] + Hash::combine($response['data'], '{n}.id', '{n}.attributes.name');
        $this->set('applications', $applications);

        return null;
    }
}
