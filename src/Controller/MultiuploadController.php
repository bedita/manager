<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace App\Controller;

/**
 * Multiupload Controller
 */
class MultiuploadController extends AppController
{
    /**
     * Object type currently used
     *
     * @var string
     */
    protected string $objectType = null;

    /**
     * @inheritDoc
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->objectType = $this->getRequest()->getParam('object_type');
        $this->Modules->setConfig('currentModuleName', ucfirst($this->objectType));
        $this->set('currentModule', ['name' => $this->objectType]);
        $this->set('objectType', $this->objectType);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function index(): void
    {
    }
}
