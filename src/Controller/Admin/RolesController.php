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

use Cake\Cache\Cache;
use Cake\Http\Response;

/**
 * Roles Controller
 *
 * @property \App\Controller\Component\PropertiesComponent $Properties
 */
class RolesController extends AdministrationBaseController
{
    public const CACHE_KEY_ROLES = 'roles';

    /**
     * @inheritDoc
     */
    protected $endpoint = '/roles';

    /**
     * @inheritDoc
     */
    protected $resourceType = 'roles';

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
    ];

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $response = parent::save();
        Cache::delete(self::CACHE_KEY_ROLES);

        return $response;
    }
}
