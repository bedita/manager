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

/**
 * Async Jobs Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class AsyncJobsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'async_jobs';

    /**
     * @inheritDoc
     */
    protected $deleteonly = true;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'service' => 'string',
        'scheduled_from' => 'date',
        'expires' => 'date',
        'max_attempts' => 'integer',
        'locked_until' => 'date',
    ];

    /**
     * @inheritDoc
     */
    protected $meta = ['created', 'modified', 'completed'];
}
