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
 * Applications Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class ApplicationsController extends AdministrationBaseController
{
    /**
     * @inheritDoc
     */
    protected $resourceType = 'applications';

    /**
     * @inheritDoc
     */
    protected $readonly = false;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'name' => 'string',
        'description' => 'text',
        'enabled' => 'bool',
    ];

    /**
     * @inheritDoc
     */
    protected $propertiesSecrets = ['api_key', 'client_secret'];
}
