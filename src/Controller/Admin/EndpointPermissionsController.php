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
 * Permissions Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class EndpointPermissionsController extends AdministrationBaseController
{
    /**
     * Resource type in use
     *
     * @var string
     */
    protected $resourceType = 'endpoint_permissions';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'endpoint_id' => 'endpoints',
        'application_id' => 'applications',
        'role_id' => 'roles',
        'read' => 'bool',
        'write' => 'bool',
    ];
}
