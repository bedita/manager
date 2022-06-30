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

use Cake\Http\Response;
use Cake\Utility\Hash;

/**
 * Appearence Controller
 *
 * @property \App\Controller\Component\ConfigComponent $Config
 */
class AppearenceController extends AdministrationBaseController
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
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
    public function index(): ?Response
    {
        $this->set('modules', $this->Config->read('Modules'));

        return null;
    }

    /**
     * Save data
     *
     * @return \Cake\Http\Response|null
     */
    public function save(): ?Response
    {
        $this->getRequest()->allowMethod(['post']);
        $data = (array)$this->getRequest()->getData();
        $modules = (string)Hash::get($data, 'Modules');
        $this->Config->save('Modules', (array)json_decode($modules, true));

        return $this->redirect(['_name' => 'admin:list:appearence']);
    }
}
