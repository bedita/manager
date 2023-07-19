<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace App\Controller\Admin;

use Cake\Http\Response;

/**
 * Objects History Controller
 */
class ObjectsHistoryController extends AdministrationBaseController
{
    /**
     * @inheritDoc
     */
    protected $resourceType = 'applications';

    /**
     * @inheritDoc
     */
    protected $readonly = true;

    /**
     * @inheritDoc
     */
    protected $properties = [
        'name' => 'string',
    ];
}
